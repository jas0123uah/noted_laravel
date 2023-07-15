@extends('layouts.app')

@section('content')

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>Laravel</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    @vite(['resources/js/app.js', 'resources/css/app.css'])


    <!-- Styles -->
    <!-- ... -->
    <style>
        @media (min-width: 992px) {
            .center-content {
                height: 100vh;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }
        }
    </style>
</head>

<div class="container">
    <div class="row justify-content-center align-items-center center-content">
        <div class="col-md-6 text-center">
            <div class="d-flex flex-column-reverse flex-md-row justify-content-center align-items-center">
                <homepage-notecard
                    class="mb-3 mb-md-0"
                    :can_be_deleted="{{ json_encode(false) }}"
                    :item="{{ json_encode(['front' => 'JavaScript is statically typed.', ]) }}"
                ></homepage-notecard>
                <div>
                    <p class="fw-bold">Learn what matters to you</p>
                    <ul style="text-align: justify;">
                        <li>Daily reminders to your inbox</li>
                        <li>Mobile-friendly UI</li>
                        <li>Spaced-repetition for efficient learning</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
