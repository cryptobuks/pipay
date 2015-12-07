@extends('app')

@section('content')

@if (count($errors) > 0)
	@include('users.error')
@else
	<script>
		Architekt.event.on('ready', function() {
			//Get height
			var h = Architekt.device.height;
			h -= 80;	//gnb height
			h -= 143;	//footer height
			console.log(h);
			$('#pi_auth > .pi-container').css('height', h + 'px');

			$('#email').focus();
		});
	</script>
@endif
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
					<input class="pi-button" type="button" value="{{ Lang::get('users.register') }}" />
				</div>
				
			</form>
		</div>
	</div>
@endsection
