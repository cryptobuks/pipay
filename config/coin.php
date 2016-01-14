<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Pi Config
	|--------------------------------------------------------------------------
	|
	|
	*/

	'pi' => array(
	    'rpc'      => env('COIN_RPC', 'http://pak36:cjsWocjswOSksm3!@127.0.0.1:18545') ,	// 파이 접속시 필요한 정보
	    'blockchain' => 'http://blockchainkk.info/tx/{txid}',
	    'exchange' => 'https://pay.pi-pay.net/api/v1/',
	    'instant_name' => 'pipayment_instant',	// 결제시 파이주소 계정이름 
	    'rate'  => 10000 ,			// 파이대 원화 환율 
	    'feerate' => 0 , 	    		// 수수료 비율 
	),

	'krw' => [
	    'rate'  => 0.0001 ,			// 환율 
	    'feerate' => 0.01 , 			// 수수료 비율 
	],

);
