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
	<div id="receipt_bg">
		<div id="receipt_wrap">
			<table id="receipt_box">
				<thead></thead>
				<tbody>
					<!-- title -->
					<tr id="receipt_hasBorder">
						<td id="receipt_title" colspan="2">영수증</td>
					</tr>
<?php

	$status = $invoice->status;

	switch($status) {
        case 'new':
            $status = '대기';
            break;
        case 'pending':
            $status = '결제 확인 중';
            break;
        case 'confirmed':
            $status = '결제 완료';
            break;
        case 'failed':
            $status = '결제 실패';
            break;
        case 'expired':
            $status = '결제 만료';
            break;
        case 'refunded':
            $status = '전액 환불';
            break;
        case 'refunded_partial':
            $status = '일부 환불';
            break;
        case 'settlement_complete':
            $status = '정산 완료';
            break;
        default:
            $status = '';
            break;
    }

?>
					<!-- product details -->
					<tr>
						<td>상품명</td>
						<td>{{ $invoice->item_desc }}</td>
					</tr>
					<tr>
						<td>결제액</td>
						<td>{{ number_format( $invoice->pi_amount, 1 ) }} Pi</td>
					</tr>
					<tr>
						<td>결제일</td>
						<td>{{ $invoice->completed_at }}</td>
					</tr>
					<tr>
						<td>거래번호</td>
						<td>{{ $invoice->id }}</td>
					</tr>
					<tr>
						<td>결제상태</td>
						<td>{{ $status }}</td>
					</tr>

					<!-- widget info -->
					<tr id="receipt_hasBorder">
						<td id="receipt_bottom" colspan="2">파이 페이먼트</td>
					</tr>

					<!-- thank you for using our service -->
					<tr id="receipt_hasBorder">
						<td id="receipt_thanks" colspan="2">이용해 주셔서 감사합니다.</td>
					</tr>
				</tbody>
			</table>

			<div id="receipt_menu">
				<span>Support</span>
				<span>|</span>
				<span>Powered by Pi-PAY</span>
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