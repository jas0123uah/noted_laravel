<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400&family=Roboto:wght@100&family=Water+Brush&display=swap" rel="stylesheet">
    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <!-- Custom Scripts -->
    @section('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Find the demo link element by its ID
                const demoLink = document.getElementById('demo-link');

                // Add a click event listener to the demo link
                demoLink.addEventListener('click', function(event) {
                    event.preventDefault(); // Prevent the default link behavior

                    // Create a new form element
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route('demo') }}'; // Replace with your demo login route

                    // Create a CSRF token input field
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}'; // Add the CSRF token value

                    // Append the CSRF token input field to the form
                    form.appendChild(csrfToken);

                    // Append the form to the document body
                    document.body.appendChild(form);

                    // Submit the form
                    form.submit();
                });
            });
        </script>
    @endsection
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-dark bg-black shadow-sm">
            <div class="container">
                <a class="text-white navbar-brand roboto fw-bolder" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto align-items-center d-flex flex-row justify-content-evenly">
                        <!-- Authentication Links -->
                        @guest
                            <!-- @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link text-white" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link text-white" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif -->
                            <!-- Testing -->
                            <li class="nav-item">
                                <a class="nav-link text-white" href="{{ route('about') }}">{{ __('About') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            <li class="nav-item">
                                <a id="demo-link" class="nav-link text-white" href="#">{{ __('Demo') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link btn button text-white" style="padding: 0.375rem 0.75rem;" href="{{ route('register') }}">{{ __('Sign Up') }}</a>
                            </li>
                            <!-- End Testing -->
                        @else
                            <li class="d-flex gap-5">
                                <a href="{{ route('about') }}" class="text-decoration-none hover-underline-animation">About</a>
                                <a href="{{ route('home') }}" class="text-decoration-none hover-underline-animation">My Learning</a>
                                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="text-decoration-none hover-underline-animation" id="logout-link">Logout</a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST">
                                    @csrf
                                </form>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>
</html>
