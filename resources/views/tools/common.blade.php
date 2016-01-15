			{!! Form::open(array('class' => 'pi-form', 'method' => 'post', 'url' => '/tool/encrypt', 'id' => 'createProductForm')) !!}
				<!-- web standard -->
				<label for="usage"></label>

				<h2>상품 정보 입력</h2>
				<!-- product name -->
				<div class="pi-form-control">
					{!! Form::label('item_desc', '상품명', array('class' => '')) !!}
					{!! Form::text('item_desc', null , array('class' => 'pi-input', 'id' => 'item_desc', 'placeholder' => '상품명은 2글자 이상입니다.')) !!} 
					<div class="pi-input-required">*</div>
				</div>

				<!-- product number -->
				<div class="pi-form-control">
					{!! Form::label('order_id', '상품번호'  , array('class' => '')) !!}
					{!! Form::text('order_id', null , array('class' => 'pi-input', 'id' => 'order_id', 'placeholder' => '상품번호는 영문자와 숫자의 조합으로 입력해주세요.')) !!}
				</div>
				
				<!-- price -->
				<div class="pi-form-control">
					{!! Form::label('amount', '상품가격'  , array('class' => '')) !!}
					{!! Form::select('currency',  array('KRW' => 'KRW', 'PI' => 'Pi')  , null, array('class' => 'pi-input', 'id' => 'currency' )) !!}
					{!! Form::text( 'amount', null , array('class' => 'pi-input', 'id' => 'amount'  )) !!}
					<div class="pi-input-required">*</div>
				</div>

				<!-- payment confirm email address -->
				<div class="pi-form-control">
					{!! Form::label('email', '결제 확인 이메일'  , array('class' => '')) !!}
					{!! Form::text( 'email', null , array('class' => 'pi-input', 'id' => 'email'  )) !!}
				</div>
				
				<!-- redirect after purchase -->
				<div class="pi-form-control">
					{!! Form::label('redirect', '결제 후 이동할 주소'  , array('class' => '')) !!}
					{!! Form::text( 'redirect', null , array('class' => 'pi-input', 'id' => 'redirect'  )) !!}
					<div class="pi-form-control-icon sprite-question"></div>
				</div>

				<!-- instant payment notify url -->
				<div class="pi-form-control">
					{!! Form::label('ipn', 'Instant Payment Notification URL'  , array('class' => '')) !!}
					{!! Form::text( 'ipn', null , array('class' => 'pi-input', 'id' => 'ipn' )) !!}
					<div class="pi-form-control-icon sprite-question"></div>
				</div>


				<!-- submit -->
				<div class="pi-form-control">
					<div class="pi-form-control-space"></div>
					{!! Form::submit('생성하기',  array('class' => 'pi-button pi-theme-success', 'name' => 'createProductSubmit', 'id' => 'createProductSubmit')) !!}
				</div>
			{!! Form::close() !!}