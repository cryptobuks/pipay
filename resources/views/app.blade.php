<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    
    <meta property="og:url" content="{{ url('/') }}" />
    <meta property="og:title" content="{{ Lang::get('pages.title1') }}" />
    <meta property="og:image" content="{{ url('/images/logo.png') }}" />
    <meta property="og:description" content="{{ Lang::get('pages.sub1') }}" />

    <title>{{ Lang::get('pages.title1') }}</title>

    <link rel="shortcut icon" href="{{ asset('/images/favicon.ico') }}" type="image/x-icon" />
    <link href="{{ asset('assets/css/app.css') }}?noCache={{ date('Y-m-d_h:i:s') }}" rel="stylesheet">
    <link href='//fonts.googleapis.com/css?family=Roboto:400,300' rel='stylesheet' type='text/css'><!-- Fonts -->
</head>
<body>
@if (Session::has('flash_message') || Session::has('flash_notification.message'))
    @include('synchronizer/instance_framework_executor')
    <?php
        
        $script_loaded = true;
        $text = '';

        if(Session::has('flash_message')) $text = Session::get('flash_message');
        else if(Session::has('flash_notification')) $text = Session::get('flash_notification');

    ?>

    <script>
        Architekt.event.on('ready', function() {
            new Architekt.module.Widget.Notice({
                text: '{!! $text !!}',
            });
        });
    </script>
@endif

<?php
    $loggedIn = Sentinel::check();
    
    if($loggedIn){
        $user = Sentinel::getUser();
        $username = $user->username ? $user->username : $user->email;
    }
?>

    <!-- GNB -->
    <div id="pi_gnb">
        <div class="pi-container">
            <a href="{{ URL::to('/') }}">
                <img src="image/logo.png" />
            </a>

            <ul id="pi_gnb_list">
                <li{{ Request::is('/') ? ' class="active"' : null }}><a href="{{ URL::to('/') }}">메인으로</a></li>
        @if ( ! Sentinel::check())
                <li{{ Request::is('login') ? ' class="active"' : null }}><a href="{{ URL::to('user/login') }}">로그인</a></li>
                <li{{ Request::is('register') ? ' class="active"' : null }}><a href="{{ URL::to('user/register') }}">회원가입</a></li>
        @else
                <li{{ Request::is('dashborad') ? ' class="active"' : null }}><a href="{{ URL::to('/') }}">Dashboard</a></li>                    
                <li{{ Request::is('product') ? ' class="active"' : null }}><a href="{{ URL::to('/') }}">Product</a></li>                                   
                <li{{ Request::is('payment') ? ' class="active"' : null }}><a href="{{ URL::to('/') }}">Payment</a></li>                                  
                <li{{ Request::is('leagder') ? ' class="active"' : null }}><a href="{{ URL::to('/') }}">Leagder</a></li>                                                                           
        @endif
            </ul>
        </div>
    </div>

    @yield('content')

    @if (isset($script_loaded) === false)
    <script src="{{ asset('assets/js/all.js') }}?noCache={{ date('Y-m-d_h:i:s') }}"></script>
    @endif

    @include('synchronizer/sync_locale')
</body>
</html>            