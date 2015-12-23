@extends('checkout')
@section('content')
    
    <script>
        Architekt.event.on('ready', function() {

        });
    </script>

    <div id="pi_top_space"></div>
	<script>
		Architekt.event.on('ready', function() {
			function _error(text, focus, reset) {
				new Architekt.module.Widget.Notice({
					text: text,
					callback: function() {
						if(focus) focus.focus();
						if(reset) reset.val('');
					}
				});
			}

			//validation
			$('#frmLogin').submit(function() {
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

				return true;
			});
		});
	</script>

	<div id="pi_auth">
		<div class="pi-container">
			<form action="{{ url('/oauth/login') }}" method="post" class="pi-form" id="frmLogin">
				<input type="hidden" name="_token" value="{{ csrf_token() }}" />

				<h1>간편 결제</h1>

				<input type="email" class="pi-input{{ !empty($errors->get('email')) ? ' pi-error' : '' }}" id="email" maxlength="100" name="email" value="{{ old('email') }}" placeholder="{{ !empty($errors->get('email')) ? $errors->get('email')[0] : Lang::get('users.email') }}" />
				<input type="password" class="pi-input{{ !empty($errors->get('password')) ? ' pi-error' : '' }}" id="password" maxlength="100" name="password" placeholder="{{ !empty($errors->get('password')) ? $errors->get('password')[0] :Lang::get('users.password') }}" />
				
				<div class="pi-button-container pi-button-centralize">
					<input class="pi-button pi-theme-confirm" type="submit" value="{{ Lang::get('users.login') }}" />
				</div>
				
			</form>
		</div>
	</div>

	<div id="pi_auth">
		<div class="pi-container">
			{{ $invoice->inbound_address }}
		</div>
	</div>

@endsection