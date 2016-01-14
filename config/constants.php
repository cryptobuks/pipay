<?php

// 계정 관련 설정 상수 
// account_histoires 테이블에 reason 필드에서 쓰이는 숫자값들을 상수로 표현함 
define('ACCOUNT_UNKNOWN' , 0 );	// 아무것도 없음
define('ACCOUNT_FIX' , 1 );		// 고정값
define('ACCOUNT_INVOICE_NEW' , 200 );	// 결제 시작시 
define('ACCOUNT_INVOICE_PENDING' , 210 );	// 결제는 됐으나 검증이 되지 않은 시점
define('ACCOUNT_INVOICE_CONFIRMED' , 220 );	// 결제 완료 
define('ACCOUNT_INVOICE_FAILED' , 230 );	// 결제 실패 
define('ACCOUNT_INVOICE_EXPIRED' , 240 );	// 결제 만료 
define('ACCOUNT_REFUND' , 300 );		// 환불 완료  
define('ACCOUNT_REFUND_LOCK' , 310 );	// 환불시 자금 동결
define('ACCOUNT_REFUND_UNLOCK' , 320 );	// 환불시 자금 동결 풀림 
define('ACCOUNT_TRANSFER' , 400 );		// 정산 완료 
define('ACCOUNT_TRANSFER_LOCK' , 410 );	// 정산시 자금 동결 
define('ACCOUNT_TRANSFER_UNLOCK' , 420 );  // 정산시 자금 동결 풀림 

// coin_transactions의 state 필드에서 쓰이는 상수값 
define('STATES_SUBMIT' , 100 );	// 입력
define('STATES_ACCEPT' , 200 );	// 수락
define('STATES_DONE' , 300 );		// 완료
define('STATES_CANCEL' , 400 );	// 취소 
define('STATES_REJECT' , 500 );	// 거부
define('STATES_SUSPECT' , 600 );	// 상태 이상

// account_histories 테이블에 fun 필드에서 쓰이는 상수값 
define('UNLOCK_FUNDS' , 1 );		// 자금 동결 풀림
define('LOCK_FUNDS' , 2 );		// 자금 동결
define('PLUS_FUNDS' , 3 );		// 자금 더함
define('SUB_FUNDS' , 4 );		// 자금 빼기
define('UNLOCK_AND_SUB_FUNDS' , 5 );	// 자금 동결 풀리고 빼기

// orders 테이블에서 state 필드에서 쓰이는 상수값 
define('ORDER_WAIT' , 100 );		// 주문 대기
define('ORDER_DONE' , 200 );		// 주문 완료 
define('ORDER_CANCEL' , 0 );		// 주문 취소

// market_id 값으로 쓰이는 상수값  (orders , trades 에서 쓰임 )
define('MARKET_PI_KRW' , 1 );		// 마켓 숫자값 

// accounts에서 주로 쓰이는 값을 상수로 표현 
define('CURRENCY_COIN' , 1 );		// 파이 코인
define('CURRENCY_FIAT' , 2 );		// 원화 

// deposits , withdraws 테이블 type 필드에서 쓰이는 상수 
define('FUND_TYPE_BANK' , 'bank' );	// 은행 모드
define('FUND_TYPE_COIN' ,  'coin' );	// 코인 모드 

// two_factors 테이블 type 필드에서 쓰이는 상수 
define('TWO_FACTOR_TYPE_SMS' ,  'sms' );	// SMS 타입 
define('TWO_FACTOR_TYPE_APP' ,  'app' );	// OTP 타입 

define('TWO_FACTOR_SINCE_TIME' , 180 );	// 유효 시간 초단위 

define('NUMBER_ZERO' , 0 );			// 숫자 0

// deposits , withdraws 테이블에서 address_type 필드에서 쓰이는 상수값 
define('SEND_ADDRESS_PI' , 100 );		// 외부 파이 입출금 타입 
define('SEND_ADDRESS_INTERNAL_PI' , 200 );	// 내부 파이 입출금 타입 
define('SEND_ADDRESS_CELLPHONE' , 300 );	// 휴대폰 입출금 타입 
define('SEND_ADDRESS_EMAIL' , 400 );	// 이메일 입출금 타입 
