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
	    'rpc'      => env('COIN_RPC', 'http://pak36:cjsWocjswOSksm3!@127.0.0.1:18545') ,
	    'blockchain' => 'http://blockchainkk.info/tx/{txid}',
	    'exchange' => 'https://pay.pi-pay.net/api/v1/',
	    'instant_name' => 'pipayment_instant',	
	    'rate'  => 10000 ,
	    'feerate' => 0 , 	    
	),

	'krw' => [
	    'rate'  => 0.0001 ,	
	    'feerate' => 0.01 , 
	],

);
