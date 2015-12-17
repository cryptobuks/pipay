@extends('app')
@section('content')

    <script>

    </script>

	<div id="pi_top_space"></div>

	<div id="pi_product_create">
		<div class="pi-container">
			{!! Form::open(array('class' => 'pi-form', 'method' => 'post', 'url' => '/product', 'id' => 'createProductForm')) !!}
				<h1>1. 상품정보</h1>
				<div class="pi-form-split"></div>

				<h2>파이 결제 방법 선택</h2>
				<!-- web standard -->
				<label for="usage"></label>
				<div class="pi-form-control">
					<div class="pi-radio">
						{!! Form::radio('usage', 1, array('class' => '')) !!} 
						<label for="usage">온라인 결제 버튼</label>
					</div>
					<div class="pi-radio">
						{!! Form::radio('usage', 2, array('class' => '')) !!} 
						<label for="usage">기부</label>
					</div>
					<div class="pi-radio">
						{!! Form::radio('usage', 3, array('class' => '')) !!} 
						<label for="usage">오프라인 결제</label>
					</div>
				</div>

				<h2>상품 정보 입력</h2>
				<div class="pi-form-control">
					{!! Form::label('item_desc', '상품명', array('class' => '')) !!}
					{!! Form::text('item_desc', null , array('class' => 'pi-input')) !!} 
					<div class="pi-input-required">*</div>
				</div>

				<div class="pi-form-control">
					{!! Form::label('order_id', '상품번호'  , array('class' => '')) !!}
					{!! Form::text('order_id', null , array('class' => 'pi-input')) !!}
					<div class="pi-input-required">*</div>
				</div>
				
				<div class="pi-form-control">
					{!! Form::label('amount', '상품가격'  , array('class' => '')) !!}
					{!! Form::select( 'settlement_currency',  array('KRW' => 'KRW', 'PI' => 'Pi')  , array('class' => 'pi-input'  )) !!}
					{!! Form::text( 'amount', null , array('class' => ''  )) !!}
				</div>
				
				<div class="pi-form-control">
					{!! Form::label('currency', '정산통화'  , array('class' => '')) !!}
					<div class="pi-radio">
						{!! Form::radio( 'currency', 'KRW' , array('class' => ''  )) !!}
						<label for="currency">KRW</label>
					</div>
					<div class="pi-radio">
						{!! Form::radio( 'currency', 'PI', array('class' => ''  )) !!}
						<label for="currency">PI</label>
					</div>
				</div>

				<div class="pi-form-control">
					{!! Form::label('email', '결제 확인 이메일'  , array('class' => '')) !!}
					{!! Form::text( 'email', null , array('class' => 'pi-input'  )) !!}
				</div>

				<div class="pi-form-control">
					{!! Form::label('chk', '고객 정보'  , array('class' => '')) !!}
					<div class="pi-checkbox">
						{!! Form::checkbox( 'chk', '결제시 고객정보 받기' , array('class' => ''  )) !!}
						{!! Form::label('chk', '결제시 고객정보 받기'  , array('class' => '')) !!}
					</div>
				</div>

				<!-- advanced -->


				<div class="pi-form-control">
					{!! Form::label('customer_email', '고객 이메일'  , array('class' => '')) !!}
					<div class="pi-checkbox">
						{!! Form::checkbox( 'customer_email', '이메일' , array('class' => ''  )) !!}
						{!! Form::label('chk', '고객 이메일'  , array('class' => '')) !!}
					</div>
				</div>

				<div class="pi-form-control">
					{!! Form::label('customer_address', '고객 주소'  , array('class' => '')) !!}
					<div class="pi-checkbox">
						{!! Form::checkbox( 'customer_address', '주소' , array('class' => ''  )) !!}
						{!! Form::label('customer_address', '고객 주소'  , array('class' => '')) !!}
					</div>
				</div>

				<div class="pi-form-control">
					{!! Form::label('customer_name', '고객 이름'  , array('class' => '')) !!}
					<div class="pi-checkbox">
						{!! Form::checkbox( 'customer_name', '이름' , array('class' => ''  )) !!}
						{!! Form::label('customer_name', '고객 이름'  , array('class' => '')) !!}
					</div>
				</div>

				<div class="pi-form-control">
					{!! Form::label('customer_custom', '고객 추가입력'  , array('class' => '')) !!}
					<div class="pi-checkbox">
						{!! Form::checkbox( 'customer_custom', '추가 입력 사항' , array('class' => ''  )) !!}
						{!! Form::label('customer_custom', '고객 추가입력'  , array('class' => '')) !!}
					</div>
				</div>

				<div class="pi-form-control">
					{!! Form::label('redirect', '결제후 Redirect URL'  , array('class' => '')) !!}
					{!! Form::text( 'redirect', null , array('class' => 'pi-input'  )) !!}
				</div>

				<div class="pi-form-control">
					{!! Form::label('callback', '결제후 Callback URL'  , array('class' => '')) !!}
					{!! Form::text( 'callback', null , array('class' => 'pi-input'  )) !!}
				</div>

				<div class="pi-form-control">
					{!! Form::label('ipn', 'Instant Payment Notification URL'  , array('class' => '')) !!}
					{!! Form::text( 'ipn', null , array('class' => 'pi-input'  )) !!}
				</div>


				<!-- submit -->
				<div class="pi-form-control">
					<div class="pi-form-control-space"></div>
					{!! Form::submit('저장하기',  array('class' => 'pi-button pi-theme-success', 'name' => 'createProductSubmit', 'id' => 'createProductSubmit')) !!}
				</div>
				
			{!! Form::close() !!}
		</div>
	</div>

@endsection