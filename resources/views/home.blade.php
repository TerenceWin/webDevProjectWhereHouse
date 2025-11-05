@extends('layouts.template')

@section('title', 'Home')

@section('head')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}?v={{ time() }}">
@endsection

@section('content')

    <div class="intro">
        <div class="intro-text">
            <h1>Welcome to WhereHouse</h1>
            <p>Locate products with ease and efficiency</p>
            <div class="intro-button">
                <a href="{{ route('register') }}">Register Your Account</a>
            </div>
        </div>
        <img src="{{ asset('images/WhereHouse.png') }}" alt="WhereHouse Logo">
    </div>

    <div class="section1">
        <div class="section1-text">
            <h1>What is WhereHouse?</h1>
            <p>WhereHouse is a web app that provides a visual 2D layout of warehouses for workers to easily locate
                products.</p>
        </div>
        <img src="{{ asset('images/What.png') }}" alt="What Section">
    </div>

    <div class="section2">
        <div class="section2-text">
            <h1>How Does WhereHouse Work?</h1>
        </div>
        <div class="boxes">
            <div class="box">
                <h1>Create a Business</h1>
                <p>Register with us to create a <br> business and design your warehouse <br> layout.</p>
            </div>
            <div class="box">
                <h1>Add Products</h1>
                <p>Add storage sections to your <br> warehouse along with the products <br> located in them.</p>
            </div>
            <div class="box">
                <h1>Find Products</h1>
                <p>Workers can log in and easily <br> locate products in your warehouse <br> with our search functionality.
                </p>
            </div>
        </div>
    </div>

    <div class="section3">
        <div class="section3-text">
            <h1>Why Choose WhereHouse?</h1>
            <p>WhereHouse simplifies and speeds up the time it takes for workers to complete tasks at hand, whether it be
                fulfilling orders or moving inventory. </p>
        </div>
        <img src="{{ asset('images/Why.png') }}" alt="Why Section">
    </div>
@endsection
