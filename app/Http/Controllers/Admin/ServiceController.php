<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\Service;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::orderBy('name')->paginate(15);

        return view('admin.services.index', compact('services'));
    }

    public function store(StoreServiceRequest $request)
    {
        Service::create($request->validated());

        return redirect()->route('admin.services.index')->with('success', 'Service created successfully.');
    }

    public function update(UpdateServiceRequest $request, Service $service)
    {
        $service->update($request->validated());

        return redirect()->route('admin.services.index')->with('success', 'Service updated successfully.');
    }

    public function destroy(Service $service)
    {
        if ($service->leads()->exists() || $service->subServices()->exists()) {
            return back()->with('error', 'Cannot delete a service that has sub-services or leads linked to it.');
        }

        $service->delete();

        return redirect()->route('admin.services.index')->with('success', 'Service deleted successfully.');
    }
}