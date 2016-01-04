@extends('checkout')
@section('content')
    
    <script>

	Architekt.event.on('ready', function() {


		// 메뉴 간단 버튼 
		$('#pi_easy_btn').click(function() {
			@if( !Auth::check() )			
			$('#pi_oauth').css('display' , '');	
			$('#pi_payment').css('display' , 'none');			
			@else 
			$('#pi_oauth').css('display' , 'none');	
			$('#pi_payment').css('display' , '');			
			@endif 
			$('#pi_sms_auth').css('display' , 'none');
			$('#pi_address').css('display' , 'none');
		});

		// 메뉴 주소 버튼 
		$('#pi_address_btn').click(function() {
			$('#pi_oauth').css('display' , 'none');						
			$('#pi_sms_auth').css('display' , 'none');									
			$('#pi_payment').css('display' , 'none');
			$('#pi_address').css('display' , '');			
		});


		function _error(text, focus, reset) {
			new Architekt.module.Widget.Notice({
				text: text,
				callback: function() {
					if(focus) focus.focus();
					if(reset) reset.val('');
				}
			});
		}

		// Login process
		$('#LoginForm').submit(function() {
			var email = $('#email');
			var password = $('#password');

			if(!email.val()) {
				_error('이메일을 입력해주세요.', email);
				return false;
			}
			else if(!password.val()) {
				_error('비밀번호를 입력해주세요.', password);
				return false;
			}

			//Send POST request
			Architekt.module.Http.post({
				url: '/oauth/loginOnce' ,
				data: {
					'email':  email.val(),
					'password': password.val() ,
				},
				success: function(data) {
					if( data.status == 'success') {
						var id = data.id;
						$('#cipher_id').val( id );
						$('#pi_oauth').css('display' , 'none');	
						$('#pi_top_menu').css('display' , 'none' );						
						$('#pi_sms_auth').css('display' , '');
					} else {
						_error('이메일이나 비밀번호가 맞지 않습니다.', email);
					}
				},
				error: function(text, status) {
					new Notice({
						text: (text + status).join(", "),
					});
				},
				complete: function() {

				}
			});

			return false;
		});

		// Sms auth process
		$('#SmsForm').submit(function() {
			var authcode = $('#authcode');
			var cipher_id = $('#cipher_id');

			if(!authcode.val()) {
				_error('문자인증 번호를 입력해주세요.', authcode);
				return false;
			}

			if(!cipher_id.val()) {
				_error('잘못된 숫자값이 입력되었습니다.', authcode);
				return false;
			}


			//Send POST request
			Architekt.module.Http.post({
				url: '/oauth/smsAuth' ,
				data: {
					'authcode':  authcode.val(),
					'cipher_id': cipher_id.val() ,
				},
				success: function(data) {
					if( data.status == 'success') {
						$('#pi_balance').html( data.balance );
						$('#pi_username').html( data.username );
						$('#pi_email').html( data.email );						
						$('#pi_sms_auth').css('display' , 'none');	
						$('#pi_top_menu').css('display' , '' );										
						$('#pi_payment').css('display' , '');
					} else {
						_error('문자 인증번호가 맞지 않습니다.', authcode);
					}
				},
				error: function(text, status) {
					new Notice({
						text: (text + status).join(", "),
					});
				},
				complete: function() {

				}
			});

			return false;
		});

		// Payment process

		$('#PaymentForm').submit(function() {
			var token = $('#token');

			//Send POST request
			Architekt.module.Http.post({
				url: '/invoice/payment' ,
				data: {
					'token':  token.val(),
				},

				success: function(data) {
					if( data.status == 'success') {
						$('#pi_sms_auth').css('display' , 'none');	
						$('#pi_payment').css('display' , 'none');
						$('#pi_pay_end').css('display' , '');

						$('#redirect').val( data.redirect );
						$('#reference').val( data.reference );						
						$('#order_id').val( data.order_id );												
					} else {
						_error('결제가 제대로 진행되지 않았습니다. 다시 시도해 주세요. ' );
					}
				},
				error: function(text, status) {
					new Notice({
						text: (text + status).join(", "),
					});
				},
				complete: function() {

				}
			});

			return false;
		});

		$('#btnEnd').click(function() {
			var redirect = $('#redirect');
			var reference = $('#reference');
			var order_id = $('#order_id');			

			if( redirect.val() ) {
				document.location.href = redirect.val() + '?reference=' + reference.val() + '&order_id=' + order_id.val();
			}

		});



	});
    </script>

    <div id="pi_top_space"></div>
	<div id="pi_top_menu" style="text-align:center">    	
	          	<a href="#" id="pi_easy_btn" ><h1>간편 결제</h1></a>
	          	<a href="#" id="pi_address_btn"><h1>파이주소</h1></a>          	
	</div>

	@if( !Auth::check() )
	<div id="pi_oauth">
	@else
	<div id="pi_oauth" style="display:none">	
	@endif
		<div class="pi-container">

			<h1>간편 결제</h1>
			{!! Form::open(array('class' => 'pi-form', 'method' => 'post', 'url' => '/oauth/loginOnce', 'id' => 'LoginForm' , 'return' => 'false' )) !!}
			<input type="email" class="pi-input{{ !empty($errors->get('email')) ? ' pi-error' : '' }}" id="email" maxlength="100" name="email" value="{{ old('email') }}" placeholder="{{ !empty($errors->get('email')) ? $errors->get('email')[0] : Lang::get('users.email') }}" />
			<input type="password" class="pi-input{{ !empty($errors->get('password')) ? ' pi-error' : '' }}" id="password" maxlength="100" name="password" placeholder="{{ !empty($errors->get('password')) ? $errors->get('password')[0] :Lang::get('users.password') }}" />
			
			<div class="pi-button-container pi-button-centralize">
				{!! Form::submit( '파이페이 로그인' ,  array('class' => 'pi-button pi-theme-success', 'name' => 'btnLoginSubmit', 'id' => 'btnLoginSubmit')) !!}
			</div>
			{!! Form::close() !!}				
		</div>
	</div>
	

	<div id="pi_sms_auth" style="display:none">
		<div class="pi-container">

			<h1>문자인증</h1>
			{!! Form::open(array('class' => 'pi-form', 'method' => 'post', 'url' => '/oauth/smsAuth', 'id' => 'SmsForm' , 'return' => 'false' )) !!}
			<input type="hidden" name="cipher_id" id="cipher_id" value="">
			<input type="text" class="pi-input{{ !empty($errors->get('authcode')) ? ' pi-error' : '' }}" id="authcode" maxlength="20" name="authcode" value="{{ old('authcode') }}" placeholder="{{ !empty($errors->get('authcode')) ? $errors->get('authcode')[0] : Lang::get('users.authcode') }}" />
			
			<div class="pi-button-container pi-button-centralize">
				{!! Form::submit( '확인' ,  array('class' => 'pi-button pi-theme-success', 'name' => 'btnSmsSubmit', 'id' => 'btnSmsSubmit')) !!}
			</div>
			{!! Form::close() !!}				
		</div>
	</div>

	@if( Auth::check() )
	<?php
		$user = Auth::user();
		$account = Oaccount::whereUserId($user->id)->first();
	?>
	<div id="pi_payment" >
		<div class="pi-container">
			<h1>결제</h1>			
			<span id="pi_email">{{ $user->email }}</span> | <a href="/oauth/logout"> 로그아웃 </a>
			{!! Form::open(array('class' => 'pi-form', 'method' => 'post', 'url' => "/invoice/payment", 'id' => 'PaymentForm' , 'return' => 'false' )) !!}
			<input type="hidden" name="token" id="token" value="{{ $token}}">			
			결제금액 :  {{ amount_format( $invoice->pi_amount ) }} PI  ( {{ amount_format( $invoice->amount )}} KRW ) <br />
			<span id="pi_username">{{ $user->username }}</span>님의 파이 잔고 : <span id="pi_balance">{{ amount_format( $account->balance ) }}</span> PI
			<div class="pi-button-container pi-button-centralize">
				{!! Form::submit( '결제하기' ,  array('class' => 'pi-button pi-theme-success', 'name' => 'btnPaymentSubmit', 'id' => 'btnPaymentSubmit')) !!}
			</div>
			{!! Form::close() !!}				
		</div>
	</div>
	@else
	<div id="pi_payment" style="display:none">	
		<div class="pi-container">
			<h1>결제</h1>			
			<span id="pi_email"></span> | <a href="/oauth/logout"> 로그아웃 </a>
			{!! Form::open(array('class' => 'pi-form', 'method' => 'post', 'url' => "/invoice/payment", 'id' => 'PaymentForm' , 'return' => 'false' )) !!}
			<input type="hidden" name="token" id="token" value="{{ $token }}">
			결제금액 :  {{ amount_format( $invoice->pi_amount ) }} PI  ( {{ amount_format( $invoice->amount )}} KRW ) <br />
			<span id="pi_username"></span>님의 파이 잔고 : <span id="pi_balance">0</span> PI
			<div class="pi-button-container pi-button-centralize">
				{!! Form::submit( '결제하기' ,  array('class' => 'pi-button pi-theme-success', 'name' => 'btnPaymentSubmit', 'id' => 'btnPaymentSubmit')) !!}
			</div>
			{!! Form::close() !!}				
		</div>
	</div>	
	@endif

	<div id="pi_pay_end" style="display:none">	
		<div class="pi-container">
			<input type="hidden" name="redirect" id="redirect" value="">			
			<input type="hidden" name="reference" id="reference" value="">						
			
			<h1>결제</h1>			
			간편 결제가 완료되었습니다. <br />
			결제를 해주셔서 감사합니다. <br />
			<div class="pi-button-container pi-button-centralize">
				{!! Form::button( '완료' ,  array('class' => 'pi-button pi-theme-success', 'name' => 'btnEnd', 'id' => 'btnEnd')) !!}
			</div>
			<div class="pi-button-container pi-button-centralize"><a href="/receipt/{{ $token }}" target="_BLANK">영수증 보기</a> </div>
		</div>
	</div>	



	<div id="pi_address" style="display:none">
		<div class="pi-container">
			<h1>파이 주소</h1>
			결제를 완료하려면, 아래의 주소로 파이를 보내주시기 바랍니다. <br />
			입금주소 : {{ $invoice->inbound_address }} <br />
			결제금액 :  {{ amount_format( $invoice->pi_amount ) }} PI  ( {{ amount_format( $invoice->amount )}} KRW ) <br />
		</div>
	</div>
	<br />
	<div class="pi-container">	
		Support | Powered By PiPay
	</div>
@endsection