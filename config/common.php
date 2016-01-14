<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Common  Config
	|--------------------------------------------------------------------------
	*/

	// 계정 입출금 이유 
	'account_reason' => [
		0 => 'unknown',
		1 => 'fix',	// 픽스
		200 => 'invoice_new',	// 결제 시작시 
		210 => 'invoice_pending',	// 결제는 됐으나 검증이 되지 않은 시점
		220 => 'invoice_confirmed',	// 결제 완료 				
		230 => 'invoice_failed', // 결제 실패 
		240 => 'invoice_expired',  // 결제 만료 
		300 => 'refund',	  // 환불 완료
		310 => 'refund_lock',  	// 환불시 자금 동결
		320 => 'refund_unlock',  //  환불시 자금 동결 풀림 
		400 => 'transfer',  // 정산 완료 
		410 => 'transfer_lock',  // 정산시 자금 동결 
		420 => 'transfer_unlock',  // 정산시 자금 동결 풀림 
	],

	// 코인 트랜젝션 상황 
	'states' => [
		100   => 'submit',  // 입력
		200   => 'accept',  // 수락
		300   => 'done',   // 완료
		400   => 'cancel',  // 취소 
		500   => 'reject',  // 거부
		600   => 'suspect',  // 상태 이상
	],

	// 자금 입출금  상황 
	'funs' => array(
		1   => 'unlock_funds',	// 자금 동결 풀림
		2   => 'lock_funds',	// 자금 동결
		3   => 'plus_funds',	// 자금 더함
		4   => 'sub_funds',	// 자금 빼기
		5   => 'unlock_and_sub_funds',	// 자금 동결 풀리고 빼기
	),

	// 인증 관련 기기 타입 
	'two_factor_type' => [
		'sms'   => 'sms',
		'opt'   => 'opt',
	],

	// 상점 업종 
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

	// 유저 등급 
	'user_levels' => [
		1 => '이메일인증 유저',
		2 => '개인회원',
		3 => '개인회원',
		4 => '법인회원',
	],

	// 결제 요구 상황 
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

	// 결제  관리자 정보  
	'pay_user' => [
		'base_uri' => env('PI_EXCHANGE_URL', 'https://pay.pi-pay.net' ), 
		'username' => env('PAY_USERNAME' , '파이페이' ) ,
		'email' => env( 'PAY_EMAIL' , 'pay@pi-works.net' ) ,		
		'password' => env( 'PAY_PASSWORD' , '123456' ) ,	
		'client_id' => env( 'PAY_CLIENT_ID' , '2' ) ,
		'client_secret' => env( 'PAY_CLIENT_SECRET' , '5678' ) , 
	] , 

];
