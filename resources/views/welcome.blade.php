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
    <!-- Import jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js" integrity="sha512-3gJwYpMe3QewGELv8k/BX9vcqhryRdzRMxVfq6ngyWXwo03GFEzjsUm8Q7RZcHPHksttq7/GFoxjCVUjkjvPdw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script type="module">
        $(document).ready(function() {
        let is_front_visible = true;
        setInterval(() => {
            if (is_front_visible) {
                $('.front-notecard').fadeOut(1000, function() {
                    $('.back-notecard').fadeIn(1000);
                });
            } else {
                $('.back-notecard').fadeOut(1000, function() {
                    $('.front-notecard').fadeIn(1000);
                });
            }
            is_front_visible = !is_front_visible;
        }, 5000);
        
        const message_button = document.getElementById('message-button');
        message_button?.addEventListener('click', function(){
            const message_container = document.getElementById('message-container');
            message_container.style.display = 'none';
            console.log(message_container, "Message ")
        })
    })
    </script>


    <!-- Styles -->
    <!-- ... -->
</head>
<div id="app">
    <div class="container">
        @if(isset($message))
        <div id="message-container" class="align-self-center welcome-message-container">
            <button id="message-button" @click="$message=null" type="button" class="close position-relative x-button x-color welcome-x" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                {{ $message }}
            </div>
        </div>

        @endif
        <div class="row justify-content-center align-items-center center-content">
            <div class="col-md-6 text-center">
                <div class="d-flex position-relative flex-column-reverse flex-md-row justify-content-center align-items-center">
                    <div class="front-notecard">
                        <notecard
                                class="mb-3 mb-md-0 card-lg"
                                :can_be_deleted="{{ json_encode(false) }}"
                                :allow_truncate="false"
                                :item="{{ json_encode(['front' => 'True or False: JavaScript is statically typed.', ]) }}"
                        ></notecard>
                    </div>
                    <div class="back-notecard" style="display:none;">
                        <notecard
                                class="mb-3 mb-md-0 card-lg"
                                :can_be_deleted="{{ json_encode(false) }}"
                                :allow_truncate="false"
                                :item="{{ json_encode(['front' => 'False - JavaScript is dynamically typed.', ]) }}"
                        ></notecard>
                    </div>
                    
                    <div>
                        <p class="fw-bold text-nowrap">Learn what matters to you</p>
                        <ul style="text-align: justify;">
                            <li class="text-nowrap">Daily reminders to your inbox</li>
                            <li class="text-nowrap">Mobile-friendly UI</li>
                            <li class="text-nowrap">Spaced-repetition for efficient learning</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @endsection

</div>
