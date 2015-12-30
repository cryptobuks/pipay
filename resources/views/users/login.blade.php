@extends('app')

@section('content')
	@include('users.setFormHeight')

@if (count($errors) > 0)
	@include('users.error')
@else
	<script>
		Architekt.event.on('ready', function() {
			$('#email').focus();
		});
	</script>
@endif

	<script>
		Architekt.event.on('ready', function() {
			function _error(text, focus, reset) {
				if(focus) focus.blur();

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
			<form action="{{ url('/user/login') }}" method="post" class="pi-form" id="frmLogin">
				<input type="hidden" name="_token" value="{{ csrf_token() }}" />

				<h1>{{ Lang::get('users.login') }}</h1>
				<h2>파이페이 계정으로 로그인해주시기 바랍니다.</h2>

				<input type="email" class="pi-input{{ !empty($errors->get('email')) ? ' pi-error' : '' }}" id="email" maxlength="100" name="email" value="{{ old('email') }}" placeholder="{{ !empty($errors->get('email')) ? $errors->get('email')[0] : Lang::get('users.email') }}" />
				<input type="password" class="pi-input{{ !empty($errors->get('password')) ? ' pi-error' : '' }}" id="password" maxlength="100" name="password" placeholder="{{ !empty($errors->get('password')) ? $errors->get('password')[0] :Lang::get('users.password') }}" />
				
				<div class="pi-button-container pi-button-centralize">
					<input class="pi-button pi-theme-confirm" type="submit" value="{{ Lang::get('users.login') }}" />
					<a id="loginRegister" href="/user/register" class="pi-button">{{ Lang::get('users.register') }}</a>
				</div>
				
			</form>
		</div>
	</div>
@endsection
