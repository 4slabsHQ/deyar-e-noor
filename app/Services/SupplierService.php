<?php

namespace App\Services;

use App\Models\Supplier;
use Illuminate\Support\Facades\Hash;

class SupplierService
{
    /**
     * Create a new supplier.
     */
    public function create(array $data): Supplier
    {
        $data = $this->preparePortalPassword($data);
        $data['created_by'] = auth()->id();

        return Supplier::create($data);
    }

    /**
     * Update an existing supplier.
     */
    public function update(Supplier $supplier, array $data): Supplier
    {
        $data = $this->preparePortalPassword($data);
        $data['updated_by'] = auth()->id();

        $supplier->update($data);

        return $supplier;
    }

    /**
     * Hash the portal password if one was provided.
     * If left empty (e.g. on edit, when admin doesn't want to change it),
     * we simply drop it so the existing password stays untouched.
     */
    private function preparePortalPassword(array $data): array
    {
        if (! empty($data['portal_password'])) {
            $data['portal_password'] = Hash::make($data['portal_password']);
        } else {
            unset($data['portal_password']);
        }

        return $data;
    }
}