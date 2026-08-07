@extends('layouts.app')

@section('page-title', 'Dashboard')

@section('content')
<div class="row g-3">
    <div class="col-xl-3 col-sm-6">
        <div class="card h-100">
            <div class="card-body">
                <span class="deyar-metric__label">Customers</span>
                <span class="deyar-metric__value">0</span>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card h-100">
            <div class="card-body">
                <span class="deyar-metric__label">Suppliers</span>
                <span class="deyar-metric__value">0</span>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card h-100">
            <div class="card-body">
                <span class="deyar-metric__label">Branches</span>
                <span class="deyar-metric__value">0</span>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card h-100">
            <div class="card-body">
                <span class="deyar-metric__label">Signed in as</span>
                <span class="deyar-metric__value deyar-metric__value--text">{{ auth()->user()->name }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
