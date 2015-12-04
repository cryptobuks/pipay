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
    <!--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" integrity="sha512-dTfge/zgoMYpP7QbHy4gWMEGsbsdZeCXz7irItjcC3sPUFtf0kuFbDz/ixG7ArTxmDjLXDmezHubeNikyKGVyQ==" crossorigin="anonymous">    -->
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
                <li{{ Request::is('login') ? ' class="active"' : null }}><a href="{{ URL::to('user/login') }}">Login</a></li>
                <li{{ Request::is('register') ? ' class="active"' : null }}><a href="{{ URL::to('user/register') }}">Register</a></li>
        @else
                <li{{ Request::is('dashborad') ? ' class="active"' : null }}><a href="{{ URL::to('/') }}">Dashboard</a></li>                    
                <li{{ Request::is('product') ? ' class="active"' : null }}><a href="{{ URL::to('/') }}">Product</a></li>                                   
                <li{{ Request::is('payment') ? ' class="active"' : null }}><a href="{{ URL::to('/') }}">Payment</a></li>                                  
                <li{{ Request::is('leagder') ? ' class="active"' : null }}><a href="{{ URL::to('/') }}">Leagder</a></li>                                                                           
        @endif
            </ul>
        </div>
    </div>

    <!-- Main top -->
    <div id="pi_main_top">
        <div class="pi-container">
            <div id="pi_main_top_text">
                <h1>파이 결제를 시작하고<br />쇼핑몰 매출이 증가하였습니다.</h1>
                <a href="#" class="pi-button pi-theme-point">파이 결제 받기</a>
                <p>파이 페이먼트는 어떻게 동작하나요?</p>
            </div>
        </div>

        <img id="pi_main_top_yummy" src="image/yummy.png" />
    </div>

    <!-- Feature overview -->
    <div id="pi_main_feature_overview">
        <div class="pi-container">
            <div class="pi_main_feature_item">
                <img src="image/feature_icon_1.png" />
                <p>수수료 제로</p>
            </div>
            <div class="pi_main_feature_item">
                <img src="image/feature_icon_3.png" />
                <p>글로벌 결제</p>
            </div>
            <div class="pi_main_feature_item">
                <img src="image/feature_icon_2.png" />
                <p>실시간 판매 및 정산</p>
            </div>
            <div class="pi_main_feature_item">
                <img src="image/feature_icon_4.png" />
                <p>간편한 설치와 결제</p>
            </div>
        </div>
    </div>

    <!-- Feature: Low fee -->
    <div id="pi_main_feature_1" class="pi_main_feature">
        <div class="pi-container">
            <div class="pi_feature_texts">
                <h1>수수료 제로</h1>
                <p>
                    신용카드, 계좌이체 등 기존의 수수료가 부담스러우셨나요?<br />
                    파이 결제시 수수료 제로, 원화 환전시 1% 수수료.<br />
                    가입비도 연회비도 없습니다.
                </p>    
            </div>
            
            <img src="image/feature_big_1.png" />
        </div>
    </div>

    <!-- Feature: Global trade -->
    <div id="pi_main_feature_2" class="pi_main_feature alt">
        <div class="pi-container">
            <div class="pi_feature_texts">
                <h1>글로벌 결제</h1>
                <p>
                    파이결제는 쉽고 빠른 해외결제를 가능하게 해줍니다.<br />
                    파이 페이먼트와 함께 여러분의 비즈니스 기회를 전세계적으로 넓혀보세요. 
                </p>
            </div>
            
            <img src="image/feature_big_3.png" />
        </div>
    </div>

    <!-- Feature: Realtime trade -->
    <div id="pi_main_feature_3" class="pi_main_feature">
        <div class="pi-container">
            <div class="pi_feature_texts">
                <h1>실시간 판매 및 정산</h1>
                <p>
                    결제상황 조회 및 파이정산이 실시간으로 처리됩니다.<br />
                    원화로 정산을 받는 경우에도 내일 바로 가능합니다. 
                </p>
            </div>
            
            
            <img src="image/feature_big_2.png" />
        </div>
    </div>

    <!-- Feature: Easy to use -->
    <div id="pi_main_feature_4" class="pi_main_feature alt">
        <div class="pi-container">
            <div class="pi_feature_texts">
                <h1>간편한 설치와 결제</h1>
                <p>
                    간단한 상품 정보를 입력하면 복잡한 설치과정없이 바로 파이결제를 받을 수 있습니다.<br />
                    여러분의 고객에게도 쉬운 결제를 경험하게 해주세요. 
                </p>
            </div>
            
            <img src="image/feature_big_4.png" />
        </div>
    </div>

    <!-- Bottom -->
    <div id="pi_main_bottom">
        <div class="pi-container">
            <a href="#" class="pi-button pi-theme-proceed">파이 결제받기</a>
        </div>
    </div>

    <!-- Footer -->
    <div id="pi_footer">
        <div class="pi-container">
            <div id="pi_footer_left">
                <img src="image/footer_logo.png" />
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

    @yield('content')

    @if (isset($script_loaded) === false)
    <script src="{{ asset('assets/js/all.js') }}?noCache={{ date('Y-m-d_h:i:s') }}"></script>
    @endif

    @include('synchronizer/sync_locale')
</body>
</html>            