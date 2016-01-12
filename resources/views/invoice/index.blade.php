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
    <script>
        //data might be required from client form server should here:
        Architekt.event.on('preparing', function() {
            //put pre-data about user
            Architekt.userInfo = Architekt.userInfo || {};
<?php

    //if user already logged in
    if( $loggedIn = Auth::check() ) {

        $user = Auth::user();

?>
            Architekt.userInfo.email = '{{ $user->email }}';
<?php
    
    }
?>

            //put pre-data about product
            Architekt.productInfo = Architekt.productInfo || {};
            Architekt.productInfo.address = '{{ $invoice->inbound_address }}';
            Architekt.productInfo.id = '{{ $invoice->id }}';
            Architekt.productInfo.token = '{{ $invoice->token }}';
        });
    </script>

    <div id="payment_module_background" class="colored">
        <div id="payment_module">
            <!-- top -->
            <div id="payment_module_top">
                <div id="payment_module_exit">×</div>
                <img src="{{ asset('upload/profile/' . $user_profile->logo) }}" />
                <h1>{{ $user_profile->company }}</h1>
                <p>{{ $invoice->item_desc }}</p>
            </div>
            <!-- user info -->
            <div id="payment_module_ucp">
                <span id="ucp_email"></span>
                <span>|</span>
                <span id="ucp_logout"><a href="/oauth/logout">Logout</a></span>
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
                <form class="payment-tab" id="tab_easy" method="post">
                    <h1>결제를 위해 로그인해주세요.</h1>

                    <!-- email -->
                    <div class="pi-payment-text">
                        <img src="{{ asset('image/gfx_letter.png') }}" />
                        <input type="email" placeholder="이메일" id="email" name="email" maxlength="128" autofocus />
                    </div>
                    <!-- pw -->
                    <div class="pi-payment-text">
                        <img src="{{ asset('image/gfx_locked.png') }}" />
                        <input type="password" placeholder="비밀번호" id="password" name="password" maxlength="100" />
                    </div>

                    <input type="submit" class="pi-payment-button" value="로그인" />
                </form>
                <div class="payment-tab" id="tab_sms">
                    <p>결제를 위한 문자 인증을 해주세요.</p>

                    <form method="post" id="smsForm">
                        <input type="hidden" id="cipher_id" name="cipher_id" />
                        <input class="pi-payment-text" id="authcode" name="authcode" placeholder="인증 코드" maxlength="5" />
                        <input type="submit" class="pi-payment-button" id="smsSubmit" value="확인" />
                    </form>

                    <div id="sms_info">
                        <p>결제 코드가 고객님의 핸드폰으로 발송되었습니다. 만약 문자가 수신되지 않는다면 페이지를 새로 불러오시거나 관리자에게 문의해주세요.</p>
                    </div>
                </div>
                <!-- payment tab -->
                <div class="payment-tab" id="tab_payment">
                    <div id="payment_info">
                        <div id="payment_price">
                            <img src="http://ricopay.pi-pay.net/image/gfx_pi.png" />
                            <p>{{ number_format($invoice->pi_amount, 1) }} Pi (KRW {{ number_format($invoice->amount, 1) }})</p>
                        </div>
                        <div id="payment_balance">
                            <h1>홍길동님의 잔고</h1>
                            <p>930.15 PI</p>
                        </div>
                    </div>
                    
                    <div id="payment_complete">
                        <p>결제가 완료되었습니다.</p>
                        <p>이용해주셔서 감사합니다.</p>
                    </div>
                    <!-- issue that using button tag, absolute width not applied as expected -->
                    <div class="pi-payment-button" id="pay">결제하기</div>
                    <a id="receipt" href="{{ url('/receipt/' . $invoice->token) }}" target="_blank">영수증</a>
                </div>
                <!-- pi tab -->
                <div class="payment-tab" id="tab_pi">
                    <div id="qr_wrap">
                        <div id="qrcode"></div>
                        <div id="tab_pi_text">
                            <p>결제를 완료하려면 아래의 주소로 파이를 보내주시기 바랍니다.</p>
                            <p>입금주소:<br />{{ $invoice->inbound_address }}</p>
                        </div>
                    </div>
                    <div id="complete">
                        <p>파이 결제가 확인되었습니다.</p>
                        <p>이용해주셔서 감사합니다.</p>
                    </div>
                    
                    <div id="tab_pi_price">
                        <img src="{{ asset('image/gfx_pi.png') }}" />
                        <p>{{ number_format($invoice->pi_amount, 1) }} Pi (KRW {{ number_format($invoice->amount, 1) }})</p>
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