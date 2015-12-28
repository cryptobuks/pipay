@extends('app')
@section('content')

    <script>
    	//global binding
    	window.generateType = 'link';

    	Architekt.event.on('ready', function() {
    		//load payment module styles
    		Architekt.loadCSS("{{ asset('assets/css/pi_payment.css') }} ");
    	});
    </script>
    
	<div id="pi_top_space"></div>

	<div id="pi_product_create">
		<div class="pi-container" id="pi_product_generate">
			<h1 class="emp">결제링크 만들기</h1>
			<div class="pi-form-split"></div>
			@include('tools/common')
		</div>
		<div class="pi-container" id="pi_product_generated">
			<h1 class="emp">언어 선택</h1>
			<div class="pi-form-split"></div>

			<div class="pi-form">
				<h1>사용하실 언어를 선택해주세요.</h1>
				<div class="pi-form-control">
					<div class="pi-radio">
						<input type="radio" class="controlLan" name="button_lan" value="ko" checked="checked" />
						<label for="button_lan">한국어</label>
					</div>

					<div class="pi-radio">
						<input type="radio" class="controlLan" name="button_lan" value="en" />
						<label for="button_lan">영어</label>
					</div>
				</div>

				<h1>링크 사용하기</h1>
				<p>아래 생성된 링크를 원하는 부분에 삽입하여 사용하실 수 있습니다.</p>

				<textarea id="pi_generated" class="pi-text pi-text-readonly" disabled="disabled"></textarea>

				<div class="pi-button-container">
					<button id="codeCopy" class="pi-button pi-theme-success">복사하기</button>
				</div>
			</div>
			
		</div>
	</div>

@endsection