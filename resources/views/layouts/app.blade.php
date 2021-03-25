<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @stack('style')
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
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

    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <script>
        let received_id = ''
        let my_id = '{{Auth::id()}}'
        $.ajaxSetup({
            headers: {
                'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
            }
        });

        // Enable pusher logging - don't include this in production
        Pusher.logToConsole = true;

        var pusher = new Pusher('d396b0b82840fc395df2', {
            cluster: 'ap1'
        });

        var channel = pusher.subscribe('my-channel');
        channel.bind('my-event', function(data) {
            if(my_id == data.from){
                $(`#${data.to}`).click()
            }else if(my_id == data.to){
                if(received_id == data.from){
                    $(`#${data.from}`).click()
                }else{
                    let pending  = parseInt($(`#${data.from}`).find('.notif').text())

                    if(pending){
                        $(`#${data.from}`).find('.notif').text(pending + 1)
                    }else{
                        $(`#${data.from}`).append(`<span class="notif">1</span>`)
                    }
                }
            }
        });

        $('.list-group-item').click(function(){
            $('.list-group-item').removeClass('user-active')
            $(this).addClass('user-active')
            $('.user-to-chat').removeClass('d-none')
            $('.user-to-chat .card-header').text($(this).find('.username').text())
            received_id = $(this).attr('id')
            $(this).find('.notif').remove()

            getAllMessageISelectedUser(received_id)
        })


        function getAllMessageISelectedUser(received_id){
            $.ajax({
                type: 'GET',
                url: `{{url('/message/${received_id}')}}`,
                data: "",
                success: (res)=> {
                    $('.user-to-chat .card-body').html(res)
                    scrollToBottom()
                }
            })
        }


        $('.send-message').keyup(function(e){
            let message = $(this).val()
            if(e.keyCode == 13 &&  message !== '' && received_id !== ''){
                $(this).val('')

                sendMessage(received_id, message)

            }
        })

        $('.btn-send-message').click(function(){
            let message = $(this).parent('.col-md-2').siblings('.col-md-10').find('.send-message').val()

            if(message !== '' && received_id !== ''){
                $(this).parent('.col-md-2').siblings('.col-md-10').find('.send-message').val('')
                sendMessage(received_id, message)

            }
        })

        function sendMessage(received_id, message){
            $.ajax({
                    type: 'POST',
                    url: `{{route('message')}}`,
                    data: {
                        received_id,
                        message,
                        _token: '{{csrf_token()}}'
                    },
                    success: (res)=> {

                    }
                })
        }

        function scrollToBottom(){
            $('.user-to-chat .card-body').animate({
                scrollTop: $('.user-to-chat .card-body').get(0).scrollHeight
            })
        }
    </script>
</body>
</html>
