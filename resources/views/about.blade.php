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
</head>

<div class="container">
    <div class="row justify-content-center align-items-center">
        <div class="col-md-6 text-center">
            <img class="img-fluid rounded-circle" style="max-width: 200px; height: auto;" src="{{ asset('images/profile_picture.png') }}" alt="Profile Picture">
            <p style="text-align: justify;  margin: 1em;">
                Hi, I'm Jay! I built this application as a personal project to help myself learn concepts in web development. (or any subject), via spaced repetition. Each day, a daily stack of review notecards is emailed to my inbox, composed of notecards I need to study the most according to the Supermemo2 algorithm.
                <br>
                Feel free to create an account to demo how this project works! I'd love to hear from you!
            </p>
            <div class="d-flex gap-5 justify-content-center">
                <a href="mailto:jspencer5396@gmail.com" target="_blank" class="btn button link-button text-white">Email Me</a>
                <a href="{{ asset('Jay_Spencer_Resume.pdf') }}" target="_blank" class="btn link-button button text-white">See My Resume</a>
            </div>
        </div>
    </div>
</div>

@endsection
