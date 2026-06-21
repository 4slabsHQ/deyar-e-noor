<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubServiceRequest;
use App\Http\Requests\UpdateSubServiceRequest;
use App\Models\Service;
use App\Models\SubService;

class SubServiceController extends Controller
{
    public function index()
    {
        $subServices = SubService::with('service')->orderBy('name')->paginate(15);
        $services = Service::orderBy('name')->get();

        return view('admin.sub-services.index', compact('subServices', 'services'));
    }

    public function store(StoreSubServiceRequest $request)
    {
        SubService::create($request->validated());

        return redirect()->route('admin.sub-services.index')->with('success', 'Sub-service created successfully.');
    }

    public function update(UpdateSubServiceRequest $request, SubService $sub_service)
    {
        $sub_service->update($request->validated());

        return redirect()->route('admin.sub-services.index')->with('success', 'Sub-service updated successfully.');
    }

    public function destroy(SubService $sub_service)
    {
        if ($sub_service->leads()->exists()) {
            return back()->with('error', 'Cannot delete a sub-service that has leads linked to it.');
        }

        $sub_service->delete();

        return redirect()->route('admin.sub-services.index')->with('success', 'Sub-service deleted successfully.');
    }
}