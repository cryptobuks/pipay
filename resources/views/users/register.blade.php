@extends('app')

@section('content')

<div class="navSpace"></div>    <!-- This is space for fill the nav area -->
@if (count($errors) > 0)
	@include('users.error')
@endif
	<div class="content" id="pi_auth">
		<form action="{{ url('/user/register') }}" method="post" class="pi_authForm" id="pi_frmJoin" class="col-md-8">
			<input type="hidden" name="_token" value="{{ csrf_token() }}" />
			<h1>{{ Lang::get('users.register') }}</h1>

			<input type="email" id="email" class="pi_text{{ !empty($errors->get('email')) ? ' pi_error' : '' }}" maxlength="100" name="email" value="{{ old('email') }}" placeholder="{{ !empty($errors->get('email')) ? $errors->get('email')[0] : Lang::get('users.email') }}" />
			<input type="password" id="password" class="pi_text{{ !empty($errors->get('password')) ? ' pi_error' : '' }}" maxlength="100" name="password" placeholder="{{ !empty($errors->get('password')) ? $errors->get('password')[0] : Lang::get('users.password') }}" />
			<input type="password" id="password_confirm" class="pi_text{{ !empty($errors->get('password_confirmation')) ? ' pi_error' : '' }}" maxlength="100" name="password_confirmation" placeholder="{{ !empty($errors->get('password_confirmation')) ? $errors->get('password_confirmation')[0] : Lang::get('users.password_confirm') }}" />
			

			<input type="submit" value="{{ Lang::get('users.register') }}" />

		</form>
	</div>

	<div class="footerSpace"></div>
@endsection
