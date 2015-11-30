@extends('app')

@section('content')

	<div class="navSpace"></div>    <!-- This is space for fill the nav area -->

@if (count($errors) > 0)
	@include('user.error')
@endif

	<div id="pi_wallet" class="container">
		<h3>{{ Lang::get('pages.user.profile') }}</h3>
		<form id="profileFrm" name="profileFrm" class="container" method="POST" action="{{ url('/user/profile' , $user->id  ) }}">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			<input type="hidden" id="user_id" name="user_id" value="{{ $user->id }}">			

			<div class="form-group">
				<label class="col-md-2 control-label col-xs-12">{{ Lang::get('pages.user.name') }}</label>
				<div class="col-md-8 col-xs-12">
					<input type="text" class="pi_text" name="username" disabled  value="{!! old( 'username' , $user->username) !!}">
				</div>
				<div class="col-md-2 hidden-xs"></div>
			</div>
			<div class="form-group">
				<label class="col-md-2 control-label col-xs-12">{{ Lang::get('pages.user.cp') }}</label>
				<div class="col-md-8 col-xs-12">
					<input type="text" id="cp" class="pi_text{{ !empty($errors->get('cellphone')) ? ' pi_error' : '' }}" name="cellphone" value="{{ old('cellphone' , $user->cellphone ) }}" readonly >
				</div>
				<div class="col-md-2 hidden-xs">  </div>
			</div>

			<div class="form-group">
				<label class="col-md-2 control-label col-xs-12">{{ Lang::get('pages.user.email') }}</label>
				<div class="col-md-8 col-xs-12">
					<input type="email" class="pi_text" name="email" disabled value="{!! old( 'email' , $user->email) !!}">
				</div>
				<div class="col-md-2 hidden-xs"></div>
			</div>


			<div class="form-group">
				<label class="col-md-2 control-label col-xs-12">{{ Lang::get('pages.user.oldpw') }}</label>
				<div class="col-md-8 col-xs-12">
					<input type="password" id="password" class="pi_text{{ !empty($errors->get('oldPassword')) ? ' pi_error' : '' }}" name="oldPassword">
				</div>
				<div class="col-md-2 hidden-xs"></div>
			</div>

			<div class="form-group">
				<label class="col-md-2 control-label col-xs-12">{{ Lang::get('pages.user.npw') }}</label>
				<div class="col-md-8 col-xs-12">
					<input type="password" id="newPassword" class="pi_text{{ !empty($errors->get('newPassword')) ? ' pi_error' : '' }}" name="newPassword">
				</div>
				<div class="col-md-2 hidden-xs"></div>
			</div>

			<div class="form-group">
				<label class="col-md-2 control-label col-xs-12">{{ Lang::get('pages.user.npwc') }}</label>
				<div class="col-md-8 col-xs-12">
					<input type="password" id="newPasswordConfirm" class="pi_text{{ !empty($errors->get('newPassword_confirmation')) ? ' pi_error' : '' }}" name="newPassword_confirmation">
				</div>
				<div class="col-md-2 hidden-xs"></div>
			</div>

			<div class="form-group">
				<div class="col-md-2"></div>
				<div class="col-md-8 col-xs-12">
					<button id="profileBtnSubmit" type="submit" class="pi_button pi_themeB">&nbsp; &nbsp; &nbsp; {{ Lang::get('pages.user.update') }} &nbsp; &nbsp; &nbsp; </button>
				</div>
				<div class="col-md-2"></div>
			</div>
		</form>

	</div>

	<div class="footerSpace"></div>
@endsection
