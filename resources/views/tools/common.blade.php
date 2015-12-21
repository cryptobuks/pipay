			{!! Form::open(array('class' => 'pi-form', 'method' => 'post', 'url' => '/tool/encrypt', 'id' => 'createProductForm')) !!}
				<div class="form-sprite-title">
					<p>파이 결제 방법 선택</p>
					<div class="sprite-question" id="question_purchase"></div>
				</div>
				<!-- web standard -->
				<label for="usage"></label>

				<!-- payment method -->
				<div class="pi-form-control">
					<div class="pi-radio">
						{!! Form::radio('usage', 1, array('class' => '')) !!} 
						<label for="usage">온라인 결제 버튼</label>
					</div>
					<div class="pi-radio">
						{!! Form::radio('usage', 2, array('class' => '')) !!} 
						<label for="usage">기부</label>
					</div>
					<div class="pi-radio">
						{!! Form::radio('usage', 3, array('class' => '')) !!} 
						<label for="usage">오프라인 결제</label>
					</div>
				</div>

				<h2>상품 정보 입력</h2>
				<!-- product name -->
				<div class="pi-form-control">
					{!! Form::label('item_desc', '상품명', array('class' => '')) !!}
					{!! Form::text('item_desc', null , array('class' => 'pi-input')) !!} 
					<div class="pi-input-required">*</div>
				</div>

				<!-- product number -->
				<div class="pi-form-control">
					{!! Form::label('order_id', '상품번호'  , array('class' => '')) !!}
					{!! Form::text('order_id', null , array('class' => 'pi-input')) !!}
				</div>
				
				<!-- price -->
				<div class="pi-form-control">
					{!! Form::label('amount', '상품가격'  , array('class' => '')) !!}
					{!! Form::select( 'currency',  array('KRW' => 'KRW', 'PI' => 'Pi')  , array('class' => 'pi-input'  )) !!}
					{!! Form::text( 'amount', null , array('class' => ''  )) !!}
					<div class="pi-input-required">*</div>
				</div>

				<!-- payment confirm email address -->
				<div class="pi-form-control">
					{!! Form::label('email', '결제 확인 이메일'  , array('class' => '')) !!}
					{!! Form::text( 'email', null , array('class' => 'pi-input'  )) !!}
				</div>

				<!-- customer info -->
				<div class="pi-form-control">
					{!! Form::label('chk', '고객 정보'  , array('class' => '')) !!}
					<div class="pi-checkbox">
						{!! Form::checkbox( 'chk', '결제시 고객정보 받기' , array('class' => ''  )) !!}
						{!! Form::label('chk', '결제시 고객정보 받기'  , array('class' => '')) !!}
					</div>
				</div>
				
				<!-- redirect after purchase -->
				<div class="pi-form-control">
					{!! Form::label('redirect', '결제 후 이동할 주소'  , array('class' => '')) !!}
					{!! Form::text( 'redirect', null , array('class' => 'pi-input'  )) !!}
					<div class="pi-form-control-icon sprite-question"></div>
				</div>

				<!-- instant payment notify url -->
				<div class="pi-form-control">
					{!! Form::label('ipn', 'Instant Payment Notification URL'  , array('class' => '')) !!}
					{!! Form::text( 'ipn', null , array('class' => 'pi-input'  )) !!}
					<div class="pi-form-control-icon sprite-question"></div>
				</div>


				<!-- submit -->
				<div class="pi-form-control">
					<div class="pi-form-control-space"></div>
					{!! Form::submit('저장하기',  array('class' => 'pi-button pi-theme-success', 'name' => 'createProductSubmit', 'id' => 'createProductSubmit')) !!}
				</div>
			{!! Form::close() !!}