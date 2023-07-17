@extends('layouts.app')

@section('content')

<head>
    <!-- ... -->
    @vite('resources/js/app.js')
    <!-- ... -->
</head>

<div class="container d-flex flex-column align-items-center">
    <div class=" w-100 row justify-content-center">
        <div class="col-md-8">
            <div id="app">
                <router-view></router-view>
            </div>
        </div>
    </div>
</div>

@endsection
