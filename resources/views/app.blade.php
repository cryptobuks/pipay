<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    
    <meta property="og:url" content="https://www.pi-pay.net/" />
    <meta property="og:title" content="{{ Lang::get('pages.title1') }}" />
    <meta property="og:image" content="https://www.pi-pay.net/images/logo1.png" />
    <meta property="og:description" content="{{ Lang::get('pages.sub1') }}" />

    <title>{{ Lang::get('pages.title1') }}</title>

    <link rel="shortcut icon" href="{{ asset('/images/favicon.ico') }}" type="image/x-icon" />
    <link href="{{ asset('assets/css/app.css') }}?noCache={{ date('Y-m-d_h:i:s') }}" rel="stylesheet">
    <link href='//fonts.googleapis.com/css?family=Roboto:400,300' rel='stylesheet' type='text/css'><!-- Fonts -->
    <script src="{{ asset('assets/js/architekt.js') }}?noCache={{ date('Y-m-d_h:i:s') }}"></script>
</head>
<body>
@if (Session::has('flash_message') || Session::has('flash_notification.message'))
    <?php

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
                <img src="{{ asset('image/logo.png') }}" />
            </a>

            <ul id="pi_gnb_list">
                <li{{ Request::is('/') ? ' class="active"' : null }}><a href="{{ URL::to('/') }}">메인</a></li>
        @if ( ! Sentinel::check())
                <li{{ Request::is('login') ? ' class="active"' : null }}><a href="{{ URL::to('user/login') }}">로그인</a></li>
                <li{{ Request::is('register') ? ' class="active"' : null }}><a href="{{ URL::to('user/register') }}">회원가입</a></li>
        @else
                <li{{ Request::is('dashborad') ? ' class="active"' : null }}><a href="{{ URL::to('/') }}">대시보드</a></li>                    
                <li{{ Request::is('product') ? ' class="active"' : null }}><a href="{{ URL::to('/') }}">제품</a></li>                                   
                <li{{ Request::is('payment') ? ' class="active"' : null }}><a href="{{ URL::to('/') }}">결제</a></li>                                  
                <li{{ Request::is('leagder') ? ' class="active"' : null }}><a href="{{ URL::to('/') }}">Leagder</a></li>                                                                           
        @endif
            </ul>
        </div>
    </div>

    @yield('content')

    <!-- Footer -->
    <div id="pi_footer">
        <div class="pi-container">
            <div id="pi_footer_left">
                <img src="{{ asset('image/footer_logo.png') }}" />
                <ul id="pi_footer_nav">
                    <li><a href="#">파이페이</a></li>
                    <li><a href="#">이용약관</a></li>
                    <li><a href="#">자주묻는 질문</a></li>
                </ul>
            </div>
            <div id="pi_footer_right">
                <p>문의: 070-4849-6024 (평일 오전 10시 ~ 오후 6시)</p>
                <p><a href="mailto:works4pi@gmail.com">works4pi@gmail.com</a> | 부산시 부산진구 범일동 306</p>
                <p>진흥마제스타워 102-2433 | (주)파이웍스 대표 박제규</p>
                <p>통신판매업 신고 제 2015-부산동구-00154호</p>
                <p>사업자번호: 816-86-00146</p>
            </div>            
        </div>
    </div>

    <!-- load depencies -->
    <script src="{{ asset('assets/js/depend.js') }}"></script>
    <!-- load modules -->
    <script src="{{ asset('assets/js/architekt_modules.js') }}?noCache={{ date('Y-m-d_h:i:s') }}"></script>
    <!-- app -->
    <script src="{{ asset('assets/js/app.js') }}?noCache={{ date('Y-m-d_h:i:s') }}"></script>

    @include('synchronizer/sync_locale')
</body>
</html>