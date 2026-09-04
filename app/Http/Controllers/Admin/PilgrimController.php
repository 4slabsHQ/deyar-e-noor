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
use App\Services\HajjSeasonService;
use App\Services\PilgrimService;
use App\Support\SeasonValidation;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PilgrimController extends Controller
{
    /** @var array<string, string> */
    private const DOCUMENT_FIELDS = [
        'photo' => 'photo_path',
        'passport' => 'passport_path',
        'visa' => 'visa_path',
        'ticket' => 'ticket_path',
    ];

    public function index()
    {
        $pilgrims = Pilgrim::query()
            ->where('hajj_year', $this->activeHajjYear())
            ->with(['company', 'package', 'podCity', 'creator'])
            ->latest()
            ->get();

        return view('admin.pilgrims.index', compact('pilgrims'));
    }

    public function show(Pilgrim $pilgrim)
    {
        $pilgrim->load([
            'formOwner',
            'company',
            'maktabCategory',
            'package.accommodationPlan.slots.property',
            'package.accommodationPlan.slots.akad',
            'package.route.steps.airport',
            'package.route.steps.city',
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
            'company_id' => ['required', 'integer', SeasonValidation::existsActive('companies')],
            'hajj_year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'pilgrim_id' => ['nullable', 'integer', Rule::exists('pilgrims', 'id')],
            'family_number' => ['nullable', 'integer', 'min:1'],
            'family_move_to' => ['nullable', 'string', 'max:20'],
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
                $validated['family_move_to'] ?? null,
            )
        );
    }

    public function families(Request $request, PilgrimService $pilgrimService): JsonResponse
    {
        $validated = $request->validate([
            'company_id' => ['required', 'integer', SeasonValidation::existsActive('companies')],
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

        $this->applyDocumentUploads($request, $data);

        unset(
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
        $hajjYear = isset($data['hajj_year']) ? (int) $data['hajj_year'] : (int) $pilgrim->hajj_year;
        $newCompanyId = isset($data['company_id']) ? (int) $data['company_id'] : null;
        $oldCompanyId = $pilgrim->company_id !== null ? (int) $pilgrim->company_id : null;
        $companyChanged = $newCompanyId !== null && $newCompanyId !== $oldCompanyId;
        $existingFamilyNumber = isset($data['existing_family_number'])
            ? (int) $data['existing_family_number']
            : null;
        $familyMoveTo = $data['family_move_to'] ?? 'keep';

        $data = $this->applyDerivedPilgrimFields($data, $pilgrimService, $hajjYear);
        $data['updated_by'] = auth()->id();

        $this->applyDocumentUploads($request, $data, $pilgrim);

        unset(
            $data['family_member_suffix'],
            $data['existing_family_number'],
            $data['family_move_to'],
            $data['promote_single'],
            $data['existing_pilgrim_id'],
        );

        if ($companyChanged && $newCompanyId !== null) {
            $newCompany = Company::query()->findOrFail($newCompanyId);
            $familyData = $pilgrimService->transferPilgrimCompany(
                $pilgrim,
                $newCompany,
                $hajjYear,
                $existingFamilyNumber,
            );

            $data = array_merge($data, $familyData);
        } elseif ($this->shouldMoveFamilyWithinCompany($pilgrim, $familyMoveTo, $oldCompanyId)) {
            $company = Company::query()->findOrFail($oldCompanyId);
            $targetFamilyNumber = $familyMoveTo === 'new' ? null : (int) $familyMoveTo;
            $familyData = $pilgrimService->transferPilgrimCompany(
                $pilgrim,
                $company,
                $hajjYear,
                $targetFamilyNumber,
            );

            $data = array_merge($data, $familyData);
        } else {
            $data['family_code'] = $pilgrim->family_code;
            $data['family_number'] = $pilgrim->family_number;
            $data['family_member_suffix'] = $pilgrim->family_member_suffix;
        }

        $pilgrim->update($data);

        return redirect()->route('admin.pilgrims.index')->with('success', 'Hajj registration updated successfully.');
    }

    private function shouldMoveFamilyWithinCompany(Pilgrim $pilgrim, string $familyMoveTo, ?int $companyId): bool
    {
        if ($companyId === null || $familyMoveTo === 'keep') {
            return false;
        }

        if ($familyMoveTo === 'new') {
            return true;
        }

        if (! ctype_digit($familyMoveTo)) {
            return false;
        }

        return (int) $familyMoveTo !== (int) $pilgrim->family_number;
    }

    public function deletionPreview(Pilgrim $pilgrim, PilgrimService $pilgrimService): JsonResponse
    {
        return response()->json($pilgrimService->previewDeletion($pilgrim));
    }

    public function destroy(Pilgrim $pilgrim, PilgrimService $pilgrimService)
    {
        foreach (array_values(self::DOCUMENT_FIELDS) as $column) {
            if ($pilgrim->{$column}) {
                Storage::disk('public')->delete($pilgrim->{$column});
            }
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

    /** @param array<string, mixed> $data */
    private function applyDocumentUploads(Request $request, array &$data, ?Pilgrim $pilgrim = null): void
    {
        foreach (self::DOCUMENT_FIELDS as $input => $column) {
            $removeKey = 'remove_'.$input;

            if ($request->boolean($removeKey)) {
                if ($pilgrim?->{$column}) {
                    Storage::disk('public')->delete($pilgrim->{$column});
                }

                $data[$column] = null;
            }

            if ($request->hasFile($input)) {
                if ($pilgrim?->{$column}) {
                    Storage::disk('public')->delete($pilgrim->{$column});
                }

                $data[$column] = $request->file($input)->store('pilgrims', 'public');
            }

            unset($data[$input], $data[$removeKey]);
        }
    }

    /** @return array<string, mixed> */
    private function formOptions(): array
    {
        return [
            'activeHajjYear' => app(HajjSeasonService::class)->activeYear(),
            'formOwners' => FormOwner::query()->forActiveYear()->where('is_active', true)->orderBy('name')->get(),
            'companies' => Company::query()->forActiveYear()->where('is_active', true)->orderBy('name')->get(),
            'maktabCategories' => MaktabCategory::query()->forActiveYear()->where('is_active', true)->orderBy('name')->get(),
            'packages' => Package::query()->forActiveYear()->where('is_active', true)->orderBy('number')->get(),
            'careOffs' => CareOff::query()->forActiveYear()->where('is_active', true)->orderBy('name')->get(),
            'cities' => City::query()->where('is_active', true)->orderBy('name')->get(),
            'roomTypes' => RoomType::query()->forActiveYear()->where('is_active', true)->orderBy('name')->get(),
            'mehramRelations' => MehramRelation::query()->forActiveYear()->where('is_active', true)->orderBy('name')->get(),
            'warisRelations' => WarisRelation::query()->forActiveYear()->where('is_active', true)->orderBy('name')->get(),
        ];
    }
}
