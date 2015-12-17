@extends('app')
@section('content')
    <script>

    </script>

<div id="pi_top_space"></div>

<div id="pi_product">
	<div class="pi-container">
		{!! Form::open(array('class' => 'form-inline', 'method' => 'post', 'url' => '/product')) !!}
			{!! Form::label('usage', '파이 결제 방법 선택', array('class' => '')) !!}
			{!! Form::radio('usage', 1 , array('class' => '')) !!} 
			{!! Form::radio('usage', 2 , array('class' => '')) !!} 
			{!! Form::radio('usage', 3 , array('class' => '')) !!} 


			{!! Form::label('item_desc', '상품명', array('class' => '')) !!}
			{!! Form::text('item_desc', null , array('class' => '')) !!} 

			{!! Form::label('order_id', '상품번호'  , array('class' => '')) !!}
			{!! Form::text( 'order_id', null , array('class' => ''  )) !!}
			
			{!! Form::label('amount', '상품가격'  , array('class' => '')) !!}
			{!! Form::select( 'settlement_currency',  array('KRW' => 'KRW', 'PI' => 'Pi')  , array('class' => ''  )) !!}
			{!! Form::text( 'amount', null , array('class' => ''  )) !!}

			{!! Form::label('currency', '정산통화'  , array('class' => '')) !!}
			{!! Form::radio( 'currency', 'KRW' , array('class' => ''  )) !!}
			{!! Form::radio( 'currency', 'PI', array('class' => ''  )) !!}

			{!! Form::label('email', '결제 확인 이메일'  , array('class' => '')) !!}
			{!! Form::text( 'email', null , array('class' => ''  )) !!}


			{!! Form::label('chk', '고객 정보'  , array('class' => '')) !!}
			{!! Form::checkbox( 'chk', '결제시 고객정보 받기' , array('class' => ''  )) !!}
			
			{!! Form::label('customer_email', '고객 이메일'  , array('class' => '')) !!}
			{!! Form::checkbox( 'customer_email', '이메일' , array('class' => ''  )) !!}

			{!! Form::label('customer_name', '고객 이름'  , array('class' => '')) !!}
			{!! Form::checkbox( 'customer_name', '이름' , array('class' => ''  )) !!}

			{!! Form::label('customer_phone', '고객 전화번호'  , array('class' => '')) !!}
			{!! Form::checkbox( 'customer_phone', '전화번호' , array('class' => ''  )) !!}

			{!! Form::label('customer_address', '고객 주소'  , array('class' => '')) !!}
			{!! Form::checkbox( 'customer_address', '주소' , array('class' => ''  )) !!}

			{!! Form::label('customer_custom', '고객 추가입력'  , array('class' => '')) !!}
			{!! Form::checkbox( 'customer_custom', '추가 입력 사항' , array('class' => ''  )) !!}


			{!! Form::label('redirect', '결제후 Redirect URL'  , array('class' => '')) !!}
			{!! Form::text( 'redirect', null , array('class' => ''  )) !!}

			{!! Form::label('callback', '결제후 Callback URL'  , array('class' => '')) !!}
			{!! Form::text( 'callback', null , array('class' => ''  )) !!}

			{!! Form::label('ipn', 'Instant Payment Notification URL'  , array('class' => '')) !!}
			{!! Form::text( 'ipn', null , array('class' => ''  )) !!}

			{!! Form::submit('저장하기',  array('class' => '')) !!}
		{!! Form::close() !!}
	</div>
</div>


@endsection