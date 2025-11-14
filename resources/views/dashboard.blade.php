@extends('layouts.template')

@section('title', 'Dashboard')

@section('head')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}?v={{ time() }}">

    <!-- CSRF token for AJAX requests -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')

    <div class="dashboard">

        <div class="dashboard-right">
            <a href="{{ route('profile.edit') }}">
                <button type="button">Profile</button>
            </a>
        </div>

        <div class="dashboard-right">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">Log Out</button>
            </form>
        </div>

        <div class="dashboard-left">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createWarehouseModal">
                Create Warehouse
            </button>
        </div>

    </div>

    <!-- Modal for Creating Warehouse -->
    <div class="modal fade" id="createWarehouseModal" tabindex="-1" aria-labelledby="createWarehouseModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createWarehouseModalLabel">Create New Warehouse</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="text" class="form-control" id="warehouseName" placeholder="Enter warehouse name">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="createWarehouseBtn">Create</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Warehouse Sections Container -->
    <div class="warehouse-sections-container">
        <!-- My Warehouses Section -->
        <div class="warehouse-section">
            <h3 class="section-title">My Warehouses</h3>
            <div class="warehouse-list my-warehouses" id="myWarehouses">
                <!-- My warehouses will be loaded here by JavaScript -->
            </div>
        </div>

        <!-- Shared Warehouses Section -->
        <div class="warehouse-section">
            <h3 class="section-title">Shared With Me</h3>
            <div class="warehouse-list shared-warehouses" id="sharedWarehouses">
                <!-- Shared warehouses will be loaded here by JavaScript -->
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('js/dashboard.js') }}?v={{ time() }}"></script>
@endsection
