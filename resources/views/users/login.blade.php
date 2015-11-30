@extends('app')

@section('content')

<div class="navSpace"></div>    <!-- This is space for fill the nav area -->

@if (count($errors) > 0)
	@include('users.error')
@else
	<script>
		window.addEventListener('load', function(){
			$('#email').focus();
		});
	</script>
@endif
	<div class="content" id="pi_auth">
		<form action="{{ url('/user/login') }}" method="post" class="pi_authForm" id="pi_frmLogin" class="col-md-8">
			<input type="hidden" name="_token" value="{{ csrf_token() }}" />
			<h1>{{ Lang::get('users.login') }}</h1>

			<input type="email" class="pi_text{{ !empty($errors->get('email')) ? ' pi_error' : '' }}" id="email" maxlength="100" name="email" value="{{ old('email') }}" placeholder="{{ !empty($errors->get('email')) ? $errors->get('email')[0] : Lang::get('users.email') }}" />
			<input type="password" class="pi_text{{ !empty($errors->get('password')) ? ' pi_error' : '' }}" id="password" maxlength="100" name="password" placeholder="{{ !empty($errors->get('password')) ? $errors->get('password')[0] :Lang::get('users.password') }}" />

			<input type="submit" value="{{ Lang::get('users.login') }}" />

			<div class="form-group">
				<div class="checkbox">
					<label for="remember">
						<input type="checkbox" name="remember"> {{ Lang::get('users.rememberPw') }}
					</label>
				</div>
			</div>

			<div class="pi_contain_right">
				<a href="{{ url('/user/register') }}">{{ Lang::get('users.register') }} </a>&nbsp;|&nbsp;<a href="{{ url('/user/forgot') }}">{{ Lang::get('users.resetPw') }}</a>
			</div>
		</form>
	</div>

	<div class="footerSpace"></div>
@endsection
