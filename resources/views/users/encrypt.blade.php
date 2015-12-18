@extends('app')
@section('content')

    <script>

    </script>

	<div id="pi_top_space"></div>

	<div id="pi_product_create">
		<div class="pi-container">
			{!! Form::open(array('class' => 'pi-form', 'method' => 'post', 'url' => '/user/encrypt', 'id' => 'createProductForm')) !!}
				<h1>1. 상품정보</h1>
				<div class="pi-form-split"></div>

				<div class="form-sprite-title">
					<p>파이 결제 방법 선택</p>
					<div class="sprite-question" id="question_purchase"></div>
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
					<div class="pi-form-control-icon sprite-question"></div>
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

					<div class="pi-form-control-icon sprite-question"></div>
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

				<div class="pi-form-control">
					{!! Form::label('redirect', '결제 후 이동할 주소'  , array('class' => '')) !!}
					{!! Form::text( 'redirect', null , array('class' => 'pi-input'  )) !!}
					<div class="pi-form-control-icon sprite-question"></div>
				</div>

				<div class="pi-form-control">
					{!! Form::label('ipn', 'Instant Payment Notification URL'  , array('class' => '')) !!}
					{!! Form::text( 'ipn', null , array('class' => 'pi-input'  )) !!}
					<div class="pi-form-control-icon sprite-question"></div>
				</div>


				<!-- submit -->
				<div class="pi-form-control">
					<div class="pi-form-control-space"></div>
					{!! Form::submit('저장하기',  array('class' => 'pi-button pi-theme-success', 'name' => 'createProductSubmit', 'id' => 'createProductSubmit')) !!}
				</div>
				
			{!! Form::close() !!}
		</div>
	</div>
