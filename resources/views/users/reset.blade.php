@extends('app')

@section('content')

	<div class="navSpace"></div>    <!-- This is space for fill the nav area -->

@if (count($errors) > 0)
<?php
	$errorString = '';
	$errorString = $errors->first();
?>
	<script>
		var errorString = '<?= $errorString ?>';

		window.addEventListener('load', function(){
			new Widget.Notice({
				text: errorString,
				headText: 'Pi-Pay',
				callback: function(){
					$('#email').focus();
				}
			});
		});
	</script>
@endif

	<div id="pi_auth">
		<form action="{{ url('/password/reset') }}" method="post" class="pi_authForm" id="pi_frmLogin" class="col-md-8">
			<input type="hidden" name="_token" value="{{ csrf_token() }}" />
			<h1>비밀번호 변경 요청</h1>

			<input type="email" class="pi_text{{ !empty($errors->get('email')) ? ' pi_error' : '' }}" maxlength="100" name="email" value="{{ old('email') }}" placeholder="!empty($errors->get('email')) ? $errors->get('email')[0] : Lang::get('pages.user.email') }}" />
			<input type="password" class="pi_text{{ !empty($errors->get('password')) ? ' pi_error' : '' }}" maxlength="100" name="password" placeholder="{{ !empty($errors->get('password')) ? $errors->get('password')[0] :Lang::get('pages.user.password') }}" />
			<input type="password" class="pi_text{{ !empty($errors->get('password_confirmation')) ? ' pi_error' : '' }}" maxlength="100" name="password_confirmation" placeholder="{{ !empty($errors->get('password')) ? $errors->get('password_confirmation')[0] :Lang::get('pages.user.pwordconfirm') }}" />

			<input type="submit" value="비밀번호 재설정" />
		</form>
	</div>

	<div class="footerSpace"></div>
@endsection