<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
use App\Models\Property;
use App\Models\PropertyAkad;
use Illuminate\Support\Facades\DB;

class PropertyController extends Controller
{
    public function index()
    {
        $properties = Property::query()
            ->forActiveYear()
            ->withCount('akads')
            ->orderBy('city')
            ->orderBy('name')
            ->get();

        return view('admin.properties.index', compact('properties'));
    }

    public function create()
    {
        return view('admin.properties.create');
    }

    public function store(StorePropertyRequest $request)
    {
        DB::transaction(function () use ($request): void {
            $property = Property::create($this->withActiveHajjYear($request->safe()->except('akads')));
            $this->syncAkads($property, $request->input('akads', []));
        });

        return redirect()->route('admin.properties.index')->with('success', 'Property created successfully.');
    }

    public function edit(Property $property)
    {
        $property->load('akads');

        return view('admin.properties.edit', compact('property'));
    }

    public function update(UpdatePropertyRequest $request, Property $property)
    {
        DB::transaction(function () use ($request, $property): void {
            $property->update($request->safe()->except('akads'));
            $this->syncAkads($property, $request->input('akads', []));
        });

        return redirect()->route('admin.properties.index')->with('success', 'Property updated successfully.');
    }

    public function destroy(Property $property)
    {
        return $this->deleteOrBack(
            $property,
            'admin.properties.index',
            'Property deleted successfully.',
        );
    }

    /** @param  list<array<string, mixed>>  $akads */
    private function syncAkads(Property $property, array $akads): void
    {
        $keptIds = [];

        foreach ($akads as $row) {
            $akadNumber = trim((string) ($row['akad_number'] ?? ''));

            if ($akadNumber === '') {
                continue;
            }

            $payload = [
                'akad_number' => $akadNumber,
                'label' => filled($row['label'] ?? null) ? (string) $row['label'] : null,
                'notes' => filled($row['notes'] ?? null) ? (string) $row['notes'] : null,
            ];

            if (! empty($row['id'])) {
                $akad = PropertyAkad::query()
                    ->where('property_id', $property->id)
                    ->findOrFail((int) $row['id']);
                $akad->update($payload);
                $keptIds[] = $akad->id;

                continue;
            }

            $akad = $property->akads()->create($payload);
            $keptIds[] = $akad->id;
        }

        $property->akads()->whereNotIn('id', $keptIds)->delete();
    }
}
