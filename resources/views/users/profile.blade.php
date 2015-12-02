@extends('app')

@section('content')

	<div class="navSpace"></div>    <!-- This is space for fill the nav area -->

@if (count($errors) > 0)
	@include('user.error')
@endif

	<div id="pi_wallet" class="container">
		<h3>{{ Lang::get('users.profile') }}</h3>
		<form id="profileFrm" name="profileFrm" class="container" method="POST" action="{{ url('/user/profile' , $user->id  ) }}">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			<input type="hidden" id="user_id" name="user_id" value="{{ $user->id }}">			

			<div id="pi_box">
				{{ $user->level_name }} |  {{ $user->username }}  {{ $user->email }}
			</div>

			<div class="form-group">
				<label class="col-md-2 control-label col-xs-12">{{ Lang::get('users.category_title') }}</label>
				<div class="col-md-8 col-xs-12">
					{!! Form::select( 'category' , $user_categories , old('category' , $user->category ) , array( 'class' => 'form-control' ) ) !!}
				</div>
				<div class="col-md-2 hidden-xs"></div>
			</div>

			<div class="form-group">
				<label class="col-md-2 control-label col-xs-12">{{ Lang::get('users.company_title') }}</label>
				<div class="col-md-8 col-xs-12">
					<input type="text" id="company" class="pi_text{{ !empty($errors->get('company')) ? ' pi_error' : '' }}" name="company">
				</div>
				<div class="col-md-2 hidden-xs"></div>
			</div>

			<div class="form-group">
				<label class="col-md-2 control-label col-xs-12">{{ Lang::get('users.website_title') }}</label>
				<div class="col-md-8 col-xs-12">
					<input type="text" id="website" class="pi_text{{ !empty($errors->get('website')) ? ' pi_error' : '' }}" name="website">
				</div>
				<div class="col-md-2 hidden-xs"></div>
			</div>

			<div class="form-group">
				<label class="col-md-2 control-label col-xs-12">{{ Lang::get('users.phone_title') }}</label>
				<div class="col-md-8 col-xs-12">
					<input type="text" id="phone" class="pi_text{{ !empty($errors->get('phone')) ? ' pi_error' : '' }}" name="phone">
				</div>
				<div class="col-md-2 hidden-xs"></div>
			</div>


			<div class="form-group">
				<label class="col-md-2 control-label col-xs-12">{{ Lang::get('users.oldpw') }}</label>
				<div class="col-md-8 col-xs-12">
					<input type="password" id="password" class="pi_text{{ !empty($errors->get('old_password')) ? ' pi_error' : '' }}" name="old_password">
				</div>
				<div class="col-md-2 hidden-xs"></div>
			</div>

			<div class="form-group">
				<label class="col-md-2 control-label col-xs-12">{{ Lang::get('user.npw') }}</label>
				<div class="col-md-8 col-xs-12">
					<input type="password" id="new_password" class="pi_text{{ !empty($errors->get('new_password')) ? ' pi_error' : '' }}" name="new_password">
				</div>
				<div class="col-md-2 hidden-xs"></div>
			</div>

			<div class="form-group">
				<label class="col-md-2 control-label col-xs-12">{{ Lang::get('user.npwc') }}</label>
				<div class="col-md-8 col-xs-12">
					<input type="password" id="new_password_confirmation" class="pi_text{{ !empty($errors->get('new_password_confirmation')) ? ' pi_error' : '' }}" name="new_password_confirmation">
				</div>
				<div class="col-md-2 hidden-xs"></div>
			</div>

			<div class="form-group">
				<div class="col-md-2"></div>
				<div class="col-md-8 col-xs-12">
					<button id="profileBtnSubmit" type="submit" class="pi_button pi_themeB">&nbsp; &nbsp; &nbsp; {{ Lang::get('users.update') }} &nbsp; &nbsp; &nbsp; </button>
				</div>
				<div class="col-md-2"></div>
			</div>
		</form>

	</div>

	<div class="footerSpace"></div>
@endsection
