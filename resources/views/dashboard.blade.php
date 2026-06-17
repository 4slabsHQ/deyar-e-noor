@extends('layouts.app')

@section('page-title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-xl-3 col-sm-6">
        <div class="card">
            <div class="card-body">
                <h4 class="fs-18 font-w600">Total Customers</h4>
                <h2 class="fs-32 font-w700 mb-0">0</h2>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card">
            <div class="card-body">
                <h4 class="fs-18 font-w600">Total Suppliers</h4>
                <h2 class="fs-32 font-w700 mb-0">0</h2>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card">
            <div class="card-body">
                <h4 class="fs-18 font-w600">Active Branches</h4>
                <h2 class="fs-32 font-w700 mb-0">0</h2>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card">
            <div class="card-body">
                <h4 class="fs-18 font-w600">Logged in as</h4>
                <h4 class="fs-20 font-w700 mb-0">{{ auth()->user()->name }}</h4>
                <span class="badge bg-primary">{{ auth()->user()->getRoleNames()->first() ?? 'No Role' }}</span>
            </div>
        </div>
    </div>
</div>
@endsection