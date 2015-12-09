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

        if(Session::has('flash_message')) $text = Session::get('flash_message');
        else if(Session::has('flash_notification.message')) $text = Session::get('flash_notification.message');

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
    $loggedIn = Sentry::check();
    
    if($loggedIn){
        $user = Sentry::getUser();
        $username = $user->username ? $user->username : $user->email;
    }
?>

    <script>
        Architekt.event.on('ready', function() {
            function _register() {
                new Architekt.module.Widget.Confirm({
                    text: '파이 페이먼트를 이용하시려면 먼저 파이 거래소에 가입하셔야 합니다. 파이 거래소 페이지로 이동하시겠습니까?',
                    closeText: '취소',
                    confirmText: '파이 거래소 회원가입',
                    callback: function() {
                        window.open('https://www.pi-pay.net/user/register', '_blank');
                    }
                });
            }

            //Register - redirect to pipay site
            $('#nav_register').click(function() {
                _register();
                return false;   
            });
            $('#loginRegister').click(function() {
                $('#nav_register').trigger('click');
                return false;
            });


            //Gnb
            var profile = $('#pi_gnb_profile_menu');
            $('#gnb_profile').click(function() {
                profile.stop(true, true).fadeToggle(200);
                return false;   //prevent default
            });
        })
    </script>

    <!-- GNB -->
    <div id="pi_gnb">
        <div class="pi-container">
            <a href="{{ URL::to('/') }}">
                <img src="{{ asset('image/logo.png') }}" />
            </a>

            <ul id="pi_gnb_list">
        @if ( ! Sentry::check())
            <li{{ Request::is('/') ? ' class="active"' : null }}><a href="{{ URL::to('/') }}">메인</a></li>
                <li{{ Request::is('login') ? ' class="active"' : null }}><a href="{{ URL::to('user/login') }}">로그인</a></li>
                <li{{ Request::is('register') ? ' class="active"' : null }}><a id="nav_register" href="{{ URL::to('user/register') }}">회원가입</a></li>
        @else
                <li{{ Request::is('dashborad') ? ' class="active"' : null }}><a href="{{ URL::to('/') }}">홈</a></li>
                <li{{ Request::is('product') ? ' class="active"' : null }}><a href="{{ URL::to('/') }}">제품</a></li>
                <li{{ Request::is('payment') ? ' class="active"' : null }}><a href="{{ URL::to('/') }}">결제</a></li>
                <li{{ Request::is('leagder') ? ' class="active"' : null }}><a href="{{ URL::to('/') }}">정산내역</a></li>
                <li{{ Request::is('support') ? ' class="active"' : null }}><a href="{{ URL::to('/') }}">고객센터</a></li>
                <li{{ Request::is('login') ? ' class="active"' : null }}><a id="gnb_profile" href="{{ URL::to('user/profile') }}"><img src="{{ asset('image/profile_pic.png') }}" /></a></li>
        @endif
            </ul>

        @if (Sentry::check())
            <!-- GNB Profile menu -->
            <div id="pi_gnb_profile_menu">
                <div>
                    <h1>최민규</h1>
                    <p>rico345100@gmail.com</p>
                </div>

                <a href="{{ URL::to('user/profile') }}">정보수정</a>
                <a href="{{ URL::to('user/logout') }}">로그아웃</a>
            </div>
        @endif
        
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