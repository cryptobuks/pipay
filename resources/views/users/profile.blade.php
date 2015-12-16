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
	<div id="pi_auth">
		<div class="pi-container">

		<h3>{{ Lang::get('users.profile') }}</h3>
		<form id="profileFrm" name="profileFrm" class="container" method="POST" action="{{ url('/user/profile' , $user->id  ) }}">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			<input type="hidden" id="user_id" name="user_id" value="{{ $user->id }}">
			<input type="hidden" id="shop_type" name="shop_type" value="1">									

			<div id="pi_box">
				{{ $user->level_name }} |  {{ $user->username }}  {{ $user->email }}
			</div>

			<div class="form-group">
				<label class="col-md-2 control-label col-xs-12">{{ Lang::get('users.category_title') }}</label>
				<div class="col-md-8 col-xs-12">
					{!! Form::select( 'category' , $user_categories , old('category' , $user_profile->category ) , array( 'class' => 'form-control' ) ) !!}
				</div>
				<div class="col-md-2 hidden-xs"></div>
			</div>

			<div class="form-group">
				<label class="col-md-2 control-label col-xs-12">{{ Lang::get('users.company_title') }}</label>
				<div class="col-md-8 col-xs-12">
					<input type="text" id="company" class="pi_text{{ !empty($errors->get('company')) ? ' pi_error' : '' }}" name="company" value="{{ $user_profile->company }}" >
				</div>
				<div class="col-md-2 hidden-xs"></div>
			</div>

			<div class="form-group">
				<label class="col-md-2 control-label col-xs-12">{{ Lang::get('users.website_title') }}</label>
				<div class="col-md-8 col-xs-12">
					<input type="text" id="website" class="pi_text{{ !empty($errors->get('website')) ? ' pi_error' : '' }}" name="website" value="{{ $user_profile->website }}" >
				</div>
				<div class="col-md-2 hidden-xs"></div>
			</div>

			<div class="form-group">
				<label class="col-md-2 control-label col-xs-12">{{ Lang::get('users.phone_title') }}</label>
				<div class="col-md-8 col-xs-12">
					<input type="text" id="phone" class="pi_text{{ !empty($errors->get('phone')) ? ' pi_error' : '' }}" name="phone" value="{{ $user_profile->phone }}" >
				</div>
				<div class="col-md-2 hidden-xs"></div>
			</div>

			<div class="form-group">
				<label class="col-md-2 control-label col-xs-12">{{ Lang::get('users.logo_title') }}</label>
				<div class="col-md-8 col-xs-12">
					<img src="/image/profile_pic.png" width="80">
					<input type="file" id="logo" class="pi_logo" name="logo">
				</div>
				<div class="col-md-2 hidden-xs"></div>
			</div>


			<div class="form-group">
				<div class="col-md-2"></div>
				<div class="col-md-8 col-xs-12">
					<button id="profileBtnSubmit" type="submit" class="pi_button">&nbsp; &nbsp; &nbsp; {{ Lang::get('users.update') }} &nbsp; &nbsp; &nbsp; </button>
				</div>
				<div class="col-md-2"></div>
			</div>
		</form>

        </div>
    </div>
@endsection