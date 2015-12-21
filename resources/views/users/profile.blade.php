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

	<div id="pi_top_space"></div>
	
	<div id="pi_profile">
		<div class="pi-container">
			<!-- user info -->
			<div class="pi-info pi-info-inline" id="pi-ucp">
				<h1>{{ $user->level_name }}</h1>
				<span class="inline-seperator">|</span>
				<h1>{{ $user->username }}  {{ $user->email }}</h1>
				<!-- keys -->
				<div>
					<span>Live API Key: {{ $user_key->live_api_key }}</span>
					<span>|</span>
					<span>Test API Key: {{ $user_key->test_api_key }}</span>
				</div>
			</div>

			<!-- profile form! -->
			<form id="profileFrm" class="pi-form" method="POST" action="{{ url('/user/profile' , $user->id ) }}" enctype="multipart/form-data">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<input type="hidden" id="user_id" name="user_id" value="{{ $user->id }}">
				<input type="hidden" id="shop_type" name="shop_type" value="1">	
				
				<h1>{{ Lang::get('users.profile') }}</h1>
				<p>사업의 종류를 선택해주세요.</p>

				<!-- category -->
				<div class="pi-form-control">
					<label for="category">{{ Lang::get('users.category_title') }}</label>
					{!! Form::select( 'category' , $user_categories , old('category' , $user_profile->category ) , array( 'class' => 'pi-input' ) ) !!}
					<div class="pi-input-required">*</div>
				</div>

				<!-- type -->
				<div class="pi-form-control">
					<label for="shop_type"></label>

					<div class="pi-form-control-inline">
						<div class="pi-radio">
							<input type="radio" name="shop_type" value="1" <?= ($user_profile->shop_type === '1') ? "checked" : "" ?>/>
							<label for="shop_type">온라인 상점</label>
						</div>

						<div class="pi-radio">
							<input type="radio" name="shop_type" value="2" <?= ($user_profile->shop_type === '2') ? "checked" : "" ?>/>
							<label for="shop_type">오프라인 상점</label>
						</div>
					</div>
				</div>

				<!-- settlement currency -->
<?php

	$settlementCurrency = $user->settlementCurrency;
	if(is_null($settlementCurrency)) $settlementCurrency = 'KRW';

	$settlementCurrency = strtoupper($settlementCurrency);

	$isCurrencyKrw = $settlementCurrency === 'KRW';
	$isCurrencyPi = $settlementCurrency === 'PI';

?>
				<div class="pi-form-control">
					{!! Form::label('settlement_currency', '정산통화'  , array('class' => '')) !!}
					<div class="pi-radio">
						{!! Form::radio('settlement_currency', 'KRW' , $isCurrencyKrw, array('class' => ''  )) !!}
						<label for="currency">KRW</label>
					</div>
					<div class="pi-radio">
						{!! Form::radio('settlement_currency', 'PI', $isCurrencyPi, array('class' => ''  )) !!}
						<label for="currency">PI</label>
					</div>

					<div class="pi-form-control-icon sprite-question"></div>
				</div>

				<div class="pi-form-split"></div>


				<p>프로필 판매자의 정보이며, 고객 결제 화면에 표시됩니다.</p>

				<!-- company name -->
				<div class="pi-form-control">
					<label for="company">{{ Lang::get('users.company_title') }}</label>
					<input type="text" id="company" class="pi-input{{ !empty($errors->get('company')) ? ' pi-error' : '' }}" name="company" value="{{ $user_profile->company }}" >
					<div class="pi-input-required">*</div>
				</div>

				<!-- website -->
				<div class="pi-form-control">
					<label for="">{{ Lang::get('users.website_title') }}</label>
					<input type="text" id="website" class="pi-input{{ !empty($errors->get('website')) ? ' pi-error' : '' }}" name="website" value="{{ $user_profile->website }}" >
				</div>

				<!-- contact -->
				<div class="pi-form-control">
					<label for="">{{ Lang::get('users.phone_title') }}</label>
					<input type="text" id="phone" class="pi-input{{ !empty($errors->get('phone')) ? ' pi-error' : '' }}" name="phone" value="{{ $user_profile->phone }}" >
				</div>

				<!-- profile image -->
				<div class="pi-form-control">
					<label for="logo">프로필 사진</label>
					<input type="file" id="photo" name="logo" />

					<p>* 권장 크기: 가로 세로 512 픽셀 이하</p>
				</div>

<?php
	$userProfile = $user_profile->logo;

	if(!$userProfile || is_null($userProfile)) {
		$userProfile = asset('image/profile_pic.png');
	}
	else {
		$userProfile = url('/upload/profile/', $userProfile);
	}
?>

				<!-- user image -->
				<div id="userPhoto">
					<img src="{{ $userProfile }}" />
				</div>

				<!-- submit -->
				<div class="pi-form-control">
					<div class="pi-form-control-space"></div>
					<input name="profileBtnSubmit" id="profileBtnSubmit" type="submit" class="pi-button pi-theme-success" value="{{ Lang::get('users.update') }}" />
				</div>
			</form>


		</div>
	</div>

@endsection