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
        console.log(my_token, "MY TOKEN")
        new Vue({
            el: '#app',
            data() {
                return {
                    stacksAndFirstNotecard: stacks_and_first_notecard,
                    testing: "HELLO"
                }
            },
            mounted() {
                this.stacksAndFirstNotecard = {!! json_encode($stacks_and_first_notecard) !!};
            }
        });

        console.log(stacks_and_first_notecard, "LOOK");
    </script>

    <!-- Styles -->
    <!-- ... -->
</head>

<div class="container d-flex flex-column align-items-center">
    <div class=" w-100 row justify-content-center">
        <div class="col-md-8">

                    <div id="app">
                        <router-view></router-view>
                        <!-- @foreach($stacks_and_first_notecard as $stack_name => $notecard)
                        <div class=" d-flex flex-column width-fit-content">
                            <span>{{$notecard->stack_id}}</span>
                                <homepage-notecard :notecard="{{json_encode($notecard)}}"></homepage-notecard>
                                <stack-metadata class="align-self-center" :stack-id="{{ json_encode($notecard->stack_id) }}" :stack-title="{{ json_encode($stack_name) }}"></stack-metadata>


                        </div>    
                        @endforeach -->
                        <!-- <template v-for="notecard in stacksAndFirstNotecard">
                            <span>this should appear</span>
                            <homepage-notecard :notecard="notecard"></homepage-notecard>
                        </template> -->
                    </div>
        </div>
    </div>
</div>
@endsection
