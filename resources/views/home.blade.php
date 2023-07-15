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

    <script type="text/javascript">
        // Move your Vue code outside the template
        var stacks_and_first_notecard = {!! json_encode($stacks_and_first_notecard) !!};
        var my_token = {!! json_encode($my_token['token']) !!};
        localStorage.setItem("access_token", my_token);
        new Vue({
            el: '#app',
            data() {
                return {
                    stacksAndFirstNotecard: stacks_and_first_notecard,
                }
            },
            mounted() {
                this.stacksAndFirstNotecard = {!! json_encode($stacks_and_first_notecard) !!};
            }
        });

    </script>

    <!-- Styles -->
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
