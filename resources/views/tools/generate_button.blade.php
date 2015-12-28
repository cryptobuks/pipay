@extends('app')
@section('content')

    <script>
    	//global binding
    	window.generateType = 'button';

    	Architekt.event.on('ready', function() {
    		//load payment module styles
    		Architekt.loadCSS("{{ asset('assets/css/pi_payment.css') }} ");
    	});
    </script>

	<div id="pi_top_space"></div>

	<div id="pi_product_create">
		<div class="pi-container" id="pi_product_generate">
			<h1 class="emp">결제버튼 만들기</h1>
			<div class="pi-form-split"></div>
			@include('tools/common')
		</div>
		<div class="pi-container" id="pi_product_generated">
			<h1 class="emp">스타일 및 언어 선택</h1>
			<div class="pi-form-split"></div>

			<div class="pi-form">
				<div id="selectType" class="pi-form-control">
					<div class="pi-radio">
						<input type="radio" class="controlLan" name="button_lan" value="ko" checked="checked" />
						<div class="pi-payment-button">
							<img src="{{ asset('image/pi-payment-logo.png') }}" />
							<span>파이결제</span>
						</div>
					</div>

					<div class="pi-radio">
						<input type="radio" class="controlLan" name="button_lan" value="en" />
						<div class="pi-payment-button">
							<img src="{{ asset('image/pi-payment-logo.png') }}" />
							<span>Pay with PI</span>
						</div>
					</div>
				</div>

				<h1>HTML 코드 사용하기</h1>
				<p>아래의 HTML 코드를 복사하여 웹 페이지에 붙여넣으시면 버튼이 생성됩니다.</p>

				<textarea id="pi_generated" class="pi-text pi-text-readonly" disabled="disabled"></textarea>

				<div class="pi-button-container">
					<button id="codeCopy" class="pi-button pi-theme-success">복사하기</button>
				</div>
			</div>
			
		</div>
	</div>

@endsection