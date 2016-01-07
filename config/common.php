<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Common  Config
	|--------------------------------------------------------------------------
	*/

	'account_reason' => [
		0 => 'unknown',
		1 => 'fix',	// 픽스
		100 => 'strike_fee',	// 수수료
		110 => 'strike_add',	// 체결수량 증가
		120 => 'strike_sub',	// 체결 수량 감소					
		130 => 'strike_unlock', // 거래완료 동결 해지
		600 => 'order_submit',  // 주문
		610 => 'order_cancel',	  // 주문 취소
		620 => 'order_fullfilled',  	
		800 => 'withdraw_lock',  // 출금 동결
		810 => 'withdraw_unlock',  // 출금 거부
		1000 => 'deposit',  // 입금
		2000 => 'withdraw',  // 출금
	],

	'states' => [
		100   => 'submit',  // 입력
		200   => 'accept',  // 수락
		300   => 'done',   // 완료
		400   => 'cancel',  // 취소 
		500   => 'reject',  // 거부
		600   => 'suspect',  // 상태 이상
	],

	'funs' => [
		1   => 'unlock_funds',
		2   => 'lock_funds',
		3   => 'plus_funds',
		4   => 'sub_funds',
		5   => 'unlock_and_sub_funds',
	],

	'two_factor_type' => [
		'sms'   => 'sms',
		'opt'   => 'opt',
	],

	'user_categories' => [
		1   => '건강관리서비스 및 장비',
		2   => '게임',
		3   => '교육 서비스',
		4   => '금융',
		5   => '컨텐츠 개발',
		6   => '농수산',
		7   => '도소매',		
		8   => '무역',	
		9   => '방송 및 통신서비스',						
		10   => '부동산 및 임대',		
		11   => '사회적 기업',	
		12   => '소프트웨어 개발',						
		13   => '수리 /서비스',		
		14   => '여행 /숙박',	
		15   => '예술 /스포츠 /여가 ',						
		16   => '운수',		
		17   => '음식점/ 커피숍/ 주류',	
		18   => '의류',						
		19   => '자선단체',		
		20   => '제조',	
		21   => '기타',						
	],

	'user_levels' => [
		1 => '이메일인증 유저',
		2 => '개인회원',
		3 => '개인회원',
		4 => '법인회원',
	],

	'invoice_status' => [
		'new' => '대기' , 
		'pending' => '결제확인중' , 
		'confirmed' => '결제완료' , 		
		'failed' => '결제실패' , 				
		'expired' => '결제만료' , 						
		'refunded' => '전액환불' , 
		'refunded_partial' => '일부환불' , 	
		'settlement_complete' => '정산완료' , 			
	] , 

	'pay_user' => [
		'base_uri' => env('PI_EXCHANGE_URL', 'https://pay.pi-pay.net' ), 
		'username' => env('PAY_USERNAME' , '파이페이' ) ,
		'email' => env( 'PAY_EMAIL' , 'pay@pi-works.net' ) ,		
		'password' => env( 'PAY_PASSWORD' , '123456' ) ,	
		'client_id' => env( 'PAY_CLIENT_ID' , '2' ) ,
		'client_secret' => env( 'PAY_CLIENT_SECRET' , '5678' ) , 
	] , 

];
