@extends('layouts.template')

@section('title', 'Dashboard')

@section('head')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}?v={{ time() }}">
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
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createBusinessModal">
                Create Warehouse
            </button>
        </div>


    </div>

    <!-- Modal -->
    <div class="modal fade" id="createBusinessModal" tabindex="-1" aria-labelledby="createBusinessModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createBusinessModalLabel">Create New Business</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="text" class="form-control" id="businessName" placeholder="Enter business name">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary">Create</button>
                </div>
            </div>
        </div>
    </div>




@endsection
