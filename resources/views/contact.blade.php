@extends('layouts.template')

@section('title', 'Contact')

@section('head')
    <link rel="stylesheet" href="{{ asset('css/contact.css') }}?v={{ time() }}">
    <style>
        body {
            background-image: url('{{ asset('images/ContactBackground.png') }}');
            background-size: cover;
        }
    </style>
@endsection

@section('content')
    <div class="contact">
        <h1>Contact</h1>
        <p>Need assistance or have questions? We're here to help! Reach out and our team will
            respond as quickly as possible to assist you.</p>
        <a href="mailto:louistiboldo@gmail.com"> Email us</a>

    </div>
@endsection
