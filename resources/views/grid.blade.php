@extends('layouts.template')

@section('title', 'Warehouse Grid')

@section('head')
    <link rel="stylesheet" href="{{ asset('css/grid.css') }}?v={{ time() }}">

    <!-- CSRF token for AJAX requests -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')


    <h1>{{ $warehouse->warehouse_name }}</h1>




@endsection

@section('scripts')
    <script src="{{ asset('js/grid.js') }}?v={{ time() }}"></script>
@endsection
