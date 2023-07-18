@extends('layouts.app')

@section('content')

<head>
    <script type="text/javascript">
        let stacks_and_first_notecard = {!! json_encode($stacks_and_first_notecard) !!};
        let my_token = {!! json_encode($my_token['token']) !!};
        localStorage.setItem("access_token", my_token);
    </script>
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
