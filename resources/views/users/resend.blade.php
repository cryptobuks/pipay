@extends('app')

@section('content')
	@include('users.setFormHeight')

@if (count($errors) > 0)
	@include('users.error')
@endif

@if (session('status'))
	<script>
		Architekt.event.on('ready', function() {
			new Architekt.module.Widget.Notice({
				text: '{{ session('status') }}'
			});
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

			$('#frmResend').submit(function() {
				var email = $('#email');

				if(!email.val()) {
					_error('이메일을 입력해주세요.', email);
					return false;
				}

				return true;
			});
		});
	</script>

	<div id="pi_auth">
		<div class="pi-container">
			<form action="{{ url('/user/resend') }}" method="post" class="pi-form" id="frmResend">
				<input type="hidden" name="_token" value="{{ csrf_token() }}" />

				<h1>Resend Email</h1>

				<input id="email" type="email" class="pi-input" name="email" value="{{ old('email') }}">
				
				<div class="pi-button-container pi-button-centralize">
					<input class="pi-button pi-theme-confirm" type="submit" value="Resend" />
				</div>
				
			</form>
		</div>
	</div>

@endsection
