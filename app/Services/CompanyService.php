<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CompanyService
{
    /*
    |--------------------------------------------------------------------------
    | Retrieve
    |--------------------------------------------------------------------------
    */
    public function getAll()
    {
        return Company::latest()->paginate(10);
    }

    public function findById(int $id): Company
    {
        return Company::findOrFail($id);
    }

    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */
    public function store(array $data): Company
    {
        if (isset($data['logo']) && $data['logo'] instanceof UploadedFile) {
            $data['logo'] = $this->uploadLogo($data['logo']);
        }

        return Company::create($data);
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */
    public function update(Company $company, array $data): Company
    {
        if (! empty($data['remove_logo'])) {
            $this->deleteLogo($company->logo);
            $data['logo'] = null;
        }

        if (isset($data['logo']) && $data['logo'] instanceof UploadedFile) {
            $this->deleteLogo($company->logo);
            $data['logo'] = $this->uploadLogo($data['logo']);
        }

        unset($data['remove_logo']);

        $company->update($data);

        return $company->fresh();
    }

    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */
    public function delete(Company $company): void
    {
        $this->deleteLogo($company->logo);
        $company->delete();
    }

    /*
    |--------------------------------------------------------------------------
    | Logo Helpers
    |--------------------------------------------------------------------------
    */
    private function uploadLogo(UploadedFile $file): string
    {
        return $file->store('companies/logos', 'public');
    }

    private function deleteLogo(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
