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
			<h1 class="emp">결제링크 만들기</h1>
			<div class="pi-form-split"></div>
			@include('tools/common')
		</div>
		<div class="pi-container" id="pi_product_generated">
		</div>
	</div>

@endsection