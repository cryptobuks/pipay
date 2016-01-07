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
    <link rel="stylesheet" href="{{ asset('assets/css/pi_payment.css') }}" />
    <script src="{{ asset('assets/js/architekt.js') }}?noCache={{ date('Y-m-d_h:i:s') }}"></script>
</head>
<body>
    <div id="payment_beginning_ball"></div>

    <div id="payment_module_background" class="colored">
        <div id="payment_module">
            <!-- top -->
            <div id="payment_module_top">
                <div id="payment_module_exit">×</div>
                <img src="http://placehold.it/200x200" />
                <h1>파이애플소프트</h1>
                <p>코리언박스게임기 (KRW 20,000)</p>
            </div>
            <!-- content -->
            <div id="payment_module_content">
                <!-- nav -->
                <div class="payment-nav">
                    <div class="payment-nav-item on">간편결제</div>
                    <div class="payment-nav-item">파이주소</div>
                </div>

                <!-- tabs -->
                <!-- easy tab -->
                <div class="payment-tab" id="tab_easy">
                    <h1>결제를 위해 로그인해주세요.</h1>

                    <!-- email -->
                    <div class="pi-payment-text">
                        <img src="{{ asset('image/gfx_letter.png') }}" />
                        <input type="email" placeholder="이메일" id="email" />
                    </div>
                    <!-- pw -->
                    <div class="pi-payment-text">
                        <img src="{{ asset('image/gfx_locked.png') }}" />
                        <input type="password" placeholder="비밀번호" id="password" />
                    </div>

                    <button class="pi-payment-button">로그인</button>    
                </div>
                <!-- pi tab -->
                <div class="payment-tab" id="tab_pi">
                    <div id="qr_wrap">
                        <div id="qrcode">
                            <img src="http://placehold.it/200x200" />
                        </div>
                        <div id="tab_pi_text">
                            <p>결제를 완료하려면 아래의 주소로 파이를 보내주시기 바랍니다.</p>
                            <p>입금주소:<br />doN45me:OdQOsldnEOflGcljmmB7</p>
                        </div>
                    </div>
                    
                    <div id="tab_pi_price">
                        <img src="{{ asset('image/gfx_pi.png') }}" />
                        <p>1.5750 Pi (KRW 15,750)</p>
                    </div>
                </div>

                <!-- footer -->
                <div class="payment-tab-footer">Powered by Pi-Pay</div>
            </div>
        </div>
    </div>


    <!-- load depencies -->
    <script src="{{ asset('assets/js/depend.js') }}"></script>
    <!-- load modules -->
    <script src="{{ asset('assets/js/architekt_modules_pay.js') }}?noCache={{ date('Y-m-d_h:i:s') }}"></script>
    <!-- app -->
    <script src="{{ asset('assets/js/pi_payment.js') }}"></script>

    @include('synchronizer/sync_locale')
</body>
</html>