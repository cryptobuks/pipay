@extends('app')
@section('content')

    <script>
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
				<div class="pi-form-control">
					<div class="pi-radio">
						<input type="radio" name="button_lan" value="ko" checked="checked" />
						<a href="#" class="pi-payment-button">
							<img src="{{ asset('image/pi-payment-logo.png') }}" />
							<span>파이결제</span>
						</a>
					</div>

					<div class="pi-radio">
						<input type="radio" name="button_lan" value="en" checked="checked" />
						<a href="#" class="pi-payment-button">
							<img src="{{ asset('image/pi-payment-logo.png') }}" />
							<span>Pay with PI</span>
						</a>
					</div>
				</div>

				<h1>HTML 코드 사용하기</h1>
				<p>아래의 HTML 코드를 복사하여 웹 페이지에 붙여넣으시면 버튼이 생성됩니다.</p>

				<div class="pi-text pi-text-readonly">
<code>
&lt;a href="#" class="pi-payment-button" data-token="" data-lang="" data-btn="" data-livemode=""&gt;
	&lt;img src="{{ asset('image/pi-payment-logo.png') }}" /&gt;
	&lt;span>파이결제&lt;/span&gt;
&lt;/a&gt;
</code>
				</div>



			</div>
			


		</div>
	</div>

@endsection