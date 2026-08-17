<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePilgrimRequest;
use App\Http\Requests\UpdatePilgrimRequest;
use App\Models\CareOff;
use App\Models\City;
use App\Models\Company;
use App\Models\FormOwner;
use App\Models\MaktabCategory;
use App\Models\MehramRelation;
use App\Models\Package;
use App\Models\Pilgrim;
use App\Models\RoomType;
use App\Models\WarisRelation;
use App\Services\PilgrimService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PilgrimController extends Controller
{
    public function index()
    {
        $pilgrims = Pilgrim::query()
            ->with(['company', 'package', 'podCity'])
            ->latest()
            ->paginate(15);

        return view('admin.pilgrims.index', compact('pilgrims'));
    }

    public function show(Pilgrim $pilgrim)
    {
        $pilgrim->load([
            'formOwner',
            'company',
            'maktabCategory',
            'package',
            'careOff',
            'podCity',
            'roomType',
            'mehramRelation',
            'warisRelation',
            'creator',
        ]);

        return view('admin.pilgrims.show', compact('pilgrim'));
    }

    public function create()
    {
        return view('admin.pilgrims.create', $this->formOptions());
    }

    public function previewFamilyCode(Request $request, PilgrimService $pilgrimService): JsonResponse
    {
        $validated = $request->validate([
            'company_id' => ['required', 'integer', Rule::exists('companies', 'id')],
            'hajj_year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'pilgrim_id' => ['nullable', 'integer', Rule::exists('pilgrims', 'id')],
            'family_number' => ['nullable', 'integer', 'min:1'],
        ]);

        $company = Company::query()->findOrFail($validated['company_id']);

        if (! $company->code) {
            return response()->json([
                'family_code' => '',
                'message' => 'Selected company has no code configured.',
            ], 422);
        }

        $pilgrim = isset($validated['pilgrim_id'])
            ? Pilgrim::query()->find($validated['pilgrim_id'])
            : null;

        return response()->json(
            $pilgrimService->previewFamilyCode(
                $company,
                (int) $validated['hajj_year'],
                $pilgrim,
                isset($validated['family_number']) ? (int) $validated['family_number'] : null,
            )
        );
    }

    public function families(Request $request, PilgrimService $pilgrimService): JsonResponse
    {
        $validated = $request->validate([
            'company_id' => ['required', 'integer', Rule::exists('companies', 'id')],
            'hajj_year' => ['required', 'integer', 'min:2000', 'max:2100'],
        ]);

        return response()->json([
            'families' => $pilgrimService->existingFamiliesForCompany(
                (int) $validated['company_id'],
                (int) $validated['hajj_year'],
            ),
        ]);
    }

    public function store(StorePilgrimRequest $request, PilgrimService $pilgrimService)
    {
        $data = $request->validated();
        $company = isset($data['company_id'])
            ? Company::query()->find($data['company_id'])
            : null;
        $hajjYear = isset($data['hajj_year']) ? (int) $data['hajj_year'] : null;
        $existingFamilyNumber = isset($data['existing_family_number'])
            ? (int) $data['existing_family_number']
            : null;

        $data = $this->applyDerivedPilgrimFields($data, $pilgrimService, $hajjYear);

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('pilgrims', 'public');
        }

        unset(
            $data['photo'],
            $data['remove_photo'],
            $data['family_member_suffix'],
            $data['existing_family_number'],
            $data['promote_single'],
            $data['existing_pilgrim_id'],
        );

        if ($company && $company->code && $hajjYear) {
            $pilgrimService->withFamilyLock(
                $company->id,
                $hajjYear,
                $existingFamilyNumber,
                function () use ($pilgrimService, $company, $hajjYear, $existingFamilyNumber, &$data): void {
                    if ($existingFamilyNumber) {
                        $familyData = $pilgrimService->prepareAddToFamily($company, $hajjYear, $existingFamilyNumber);

                        if ($familyData['promote_single'] ?? false) {
                            $existingPilgrim = Pilgrim::query()->findOrFail($familyData['existing_pilgrim_id']);
                            $pilgrimService->promoteSingleToA($existingPilgrim, $company);
                        }
                    } else {
                        $familyData = $pilgrimService->prepareNewSingleFamily($company, $hajjYear);
                    }

                    $data = array_merge($data, $familyData);
                    $data['created_by'] = auth()->id();

                    Pilgrim::query()->create($data);
                }
            );
        } else {
            $data['family_code'] = null;
            $data['family_number'] = null;
            $data['family_member_suffix'] = null;
            $data['created_by'] = auth()->id();

            Pilgrim::query()->create($data);
        }

        return redirect()->route('admin.pilgrims.index')->with('success', 'Hajj registration saved successfully.');
    }

    public function edit(Pilgrim $pilgrim)
    {
        return view('admin.pilgrims.edit', array_merge(
            ['pilgrim' => $pilgrim],
            $this->formOptions()
        ));
    }

    public function update(UpdatePilgrimRequest $request, Pilgrim $pilgrim, PilgrimService $pilgrimService)
    {
        $data = $request->validated();

        $data['family_code'] = $pilgrim->family_code;
        $data['family_number'] = $pilgrim->family_number;
        $data['family_member_suffix'] = $pilgrim->family_member_suffix;

        $data = $this->applyDerivedPilgrimFields(
            $data,
            $pilgrimService,
            isset($data['hajj_year']) ? (int) $data['hajj_year'] : null,
        );
        $data['updated_by'] = auth()->id();

        if ($request->boolean('remove_photo')) {
            if ($pilgrim->photo_path) {
                Storage::disk('public')->delete($pilgrim->photo_path);
            }
            $data['photo_path'] = null;
        }

        if ($request->hasFile('photo')) {
            if ($pilgrim->photo_path) {
                Storage::disk('public')->delete($pilgrim->photo_path);
            }
            $data['photo_path'] = $request->file('photo')->store('pilgrims', 'public');
        }

        unset($data['photo'], $data['remove_photo'], $data['family_member_suffix']);

        $pilgrim->update($data);

        return redirect()->route('admin.pilgrims.index')->with('success', 'Hajj registration updated successfully.');
    }

    public function destroy(Pilgrim $pilgrim, PilgrimService $pilgrimService)
    {
        if ($pilgrim->photo_path) {
            Storage::disk('public')->delete($pilgrim->photo_path);
        }

        $pilgrimService->deletePilgrim($pilgrim);

        return redirect()->route('admin.pilgrims.index')->with('success', 'Hajj registration deleted successfully.');
    }

    /** @return array<string, mixed> */
    private function applyDerivedPilgrimFields(array $data, PilgrimService $pilgrimService, ?int $hajjYear): array
    {
        $fullName = $pilgrimService->buildFullName(
            (string) ($data['surname'] ?? ''),
            (string) ($data['given_name'] ?? ''),
        );

        $data['full_name'] = $fullName !== '' ? $fullName : null;

        if (isset($data['date_of_birth']) && $hajjYear) {
            $data['age'] = $pilgrimService->calculateAge(
                Carbon::parse($data['date_of_birth']),
                $hajjYear,
            );
        } else {
            $data['age'] = null;
        }

        return $data;
    }

    /** @return array<string, mixed> */
    private function formOptions(): array
    {
        return [
            'formOwners' => FormOwner::query()->where('is_active', true)->orderBy('name')->get(),
            'companies' => Company::query()->where('is_active', true)->orderBy('name')->get(),
            'maktabCategories' => MaktabCategory::query()->where('is_active', true)->orderBy('name')->get(),
            'packages' => Package::query()->where('is_active', true)->orderBy('number')->get(),
            'careOffs' => CareOff::query()->where('is_active', true)->orderBy('name')->get(),
            'cities' => City::query()->where('is_active', true)->orderBy('name')->get(),
            'roomTypes' => RoomType::query()->where('is_active', true)->orderBy('name')->get(),
            'mehramRelations' => MehramRelation::query()->where('is_active', true)->orderBy('name')->get(),
            'warisRelations' => WarisRelation::query()->where('is_active', true)->orderBy('name')->get(),
        ];
    }
}
