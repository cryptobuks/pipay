@extends('app')

@section('content')

	<div class="navSpace"></div>    <!-- This is space for fill the nav area -->

@if (session('status'))
	<script>
		window.addEventListener('load', function(){
			new Widget.Notice({
				text: '{{session('status') }}',
				headText: '{{ session('status') }}',
				callback: function(){
					$('#email').focus();
				}
			});
		});
	</script>
@endif

@if (count($errors) > 0)
	@include('user.error')
@endif

	<div id="pi_auth">
		<form action="{{ url('/user/forgot') }}" method="post" class="pi_authForm" id="pi_frmForgot" class="col-md-8">
			<input type="hidden" name="_token" value="{{ csrf_token() }}" />
			<h1>{{ Lang::get('pages.user.resetPw') }}</h1>

			<input type="email" id="email" class="pi_text{{ !empty($errors->get('email')) ? ' pi_error' : '' }}" maxlength="100" name="email" value="{{ old('email') }}" placeholder="{{ Lang::get('pages.user.email') }}" />

			<input type="submit" value="{{ Lang::get('pages.user.resetPw') }}" />
		</form>
	</div>

	<div class="footerSpace"></div>
@endsection
