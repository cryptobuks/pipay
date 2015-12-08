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
				new Architekt.module.Widget.Notice({
					text: text,
					callback: function() {
						if(focus) focus.focus();
						if(reset) reset.val('');
					}
				});
			}
			
			//register validations
			$('#frmRegister').submit(function() {
				var email = $('#email');
				var password = $('#password');
				var passwordConfirm = $('#password_confirm');

				if(!email.val()) {
					_error('이메일을 입력해주세요.', email);
					return false;
				}
				else if(!password.val()) {
					_error('비밀번호를 입력해주세요.', password);
					return false;
				}
				else if(!passwordConfirm.val()) {
					_error('비밀번호 확인을 입력해주세요.', password);
					return false;
				}
				else if(password.val() !== passwordConfirm.val()) {
					_error('비밀번호가 일치하지 않습니다.', password, passwordConfirm);
					return false;
				}

				return true;				
			});

			//cancel
			$('#cancelRegister').click(function() {
				var href = $(this).attr('href');

				new Architekt.module.Widget.Confirm({
					text: '가입을 취소하시겠습니까?',
					callback: function() {
						location.href = href;
					}
				});

				return false;	//prevent default
			});
		});
	</script>

	<div id="pi_auth">
		<div class="pi-container">
			<form action="{{ url('/user/register') }}" method="post" class="pi-form" id="frmRegister">
				<input type="hidden" name="_token" value="{{ csrf_token() }}" />

				<h1>{{ Lang::get('users.register') }}</h1>

				<input type="email" id="email" class="pi-input{{ !empty($errors->get('email')) ? ' pi-error' : '' }}" maxlength="100" name="email" value="{{ old('email') }}" placeholder="{{ !empty($errors->get('email')) ? $errors->get('email')[0] : Lang::get('users.email') }}" />
				<input type="password" id="password" class="pi-input{{ !empty($errors->get('password')) ? ' pi-error' : '' }}" maxlength="100" name="password" placeholder="{{ !empty($errors->get('password')) ? $errors->get('password')[0] : Lang::get('users.password') }}" />
				<input type="password" id="password_confirm" class="pi-input{{ !empty($errors->get('password_confirmation')) ? ' pi-error' : '' }}" maxlength="100" name="password_confirmation" placeholder="{{ !empty($errors->get('password_confirmation')) ? $errors->get('password_confirmation')[0] : Lang::get('users.password_confirm') }}" />

				<div class="pi-button-container pi-button-centralize">
					<input class="pi-button pi-theme-confirm" type="submit" value="{{ Lang::get('users.register') }}" />
					<a id="cancelRegister" href="/user/login" class="pi-button">가입 취소</a>
				</div>
				
			</form>
		</div>
	</div>

@endsection
