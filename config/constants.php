<?php

define('ACCOUNT_UNKNOWN' , 0 );
define('ACCOUNT_FIX' , 1 );
define('ACCOUNT_STRIKE_FEE' , 100 );
define('ACCOUNT_STRIKE_ADD' , 110 );
define('ACCOUNT_STRIKE_SUB' , 120 );
define('ACCOUNT_STRIKE_UNLOCK' , 130 );
define('ACCOUNT_ORDER_SUBMIT' , 600 );
define('ACCOUNT_ORDER_CANCEL' , 610 );
define('ACCOUNT_ORDER_FULLFILLED' , 620 );
define('ACCOUNT_WITHDRAW_LOCK' , 800 );
define('ACCOUNT_WITHDRAW_UNLOCK' , 810 );
define('ACCOUNT_DEPOSIT' , 1000 );
define('ACCOUNT_WITHDRAW' , 2000 );

define('STATES_SUBMIT' , 100 );
define('STATES_ACCEPT' , 200 );
define('STATES_DONE' , 300 );
define('STATES_CANCEL' , 400 );
define('STATES_REJECT' , 500 );
define('STATES_SUSPECT' , 600 );

define('UNLOCK_FUNDS' , 1 );
define('LOCK_FUNDS' , 2 );
define('PLUS_FUNDS' , 3 );
define('SUB_FUNDS' , 4 );
define('UNLOCK_AND_SUB_FUNDS' , 5 );

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

define('SEND_ADDRESS_PI' , 100 );
define('SEND_ADDRESS_INTERNAL_PI' , 200 );
define('SEND_ADDRESS_CELLPHONE' , 300 );
define('SEND_ADDRESS_EMAIL' , 400 );
