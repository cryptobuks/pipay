<?php

// 사용자 계정 이유  상수 
define('ACCOUNT_UNKNOWN' , 0 );
define('ACCOUNT_FIX' , 1 );
define('ACCOUNT_INVOICE_NEW' , 200 );
define('ACCOUNT_INVOICE_PENDING' , 210 );
define('ACCOUNT_INVOICE_CONFIRMED' , 220 );
define('ACCOUNT_INVOICE_FAILED' , 230 );
define('ACCOUNT_INVOICE_EXPIRED' , 240 );
define('ACCOUNT_REFUND' , 300 );
define('ACCOUNT_REFUND_LOCK' , 310 );
define('ACCOUNT_REFUND_UNLOCK' , 320 );
define('ACCOUNT_TRANSFER' , 400 );
define('ACCOUNT_TRANSFER_LOCK' , 410 );
define('ACCOUNT_TRANSFER_UNLOCK' , 420 );

//  코인 거래 상수 
define('STATES_SUBMIT' , 100 );
define('STATES_ACCEPT' , 200 );
define('STATES_DONE' , 300 );
define('STATES_CANCEL' , 400 );
define('STATES_REJECT' , 500 );
define('STATES_SUSPECT' , 600 );

// 사용자 계정  상태 상수 
define('UNLOCK_FUNDS' , 1 );
define('LOCK_FUNDS' , 2 );
define('PLUS_FUNDS' , 3 );
define('SUB_FUNDS' , 4 );
define('UNLOCK_AND_SUB_FUNDS' , 5 );

// 주문 상태 상수 
define('ORDER_WAIT' , 100 );
define('ORDER_DONE' , 200 );
define('ORDER_CANCEL' , 0 );

define('MARKET_PI_KRW' , 1 );

define('CURRENCY_COIN' , 1 );
define('CURRENCY_FIAT' , 2 );

define('FUND_TYPE_BANK' , 'bank' );
define('FUND_TYPE_COIN' ,  'coin' );

define('TWO_FACTOR_TYPE_SMS' ,  'sms' );
define('TWO_FACTOR_TYPE_APP' ,  'app' );

define('TWO_FACTOR_SINCE_TIME' , 180 );

define('NUMBER_ZERO' , 0 );

// 송금 관련 상수 
define('SEND_ADDRESS_PI' , 100 );
define('SEND_ADDRESS_INTERNAL_PI' , 200 );
define('SEND_ADDRESS_CELLPHONE' , 300 );
define('SEND_ADDRESS_EMAIL' , 400 );
