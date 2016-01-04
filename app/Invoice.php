<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Nbobtc\Bitcoind\Bitcoind;
use Nbobtc\Bitcoind\Client;
use Illuminate\Support\Facades\Config;

class Invoice extends Model
{

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'invoices';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [ 
		'user_id' , 'token' , 'status' , 'exception_status' , 'api_key' , 'product_id' , 'product_token' ,
		 'amount' , 'amount_received' , 'pi_amount' , 'pi_amount_received' , 'rate' , 'currency' ,
		 'inbound_address' , 'refund_address' , 'livemode' , 'item_desc' , 'order_id' , 'reference' , 
		 'email' , 'expiration_at' , 'url' , 'payment_url' , 'customer_email' , 'customer_name' , 
		 'customer_phone' , 'customer_address' , 'customer_custom' , 'lang' , 'redirect' , 'ipn' , 'fee' , 
	];
   
	/**
	 * [getValidateAddress 파이 주소 검증 ]
	 * @param  [type] $address [파이 주소]
	 * @return [boolean]          [결과값]
	 */
	public static function getValidateAddress( $address ) {
		try {
			$bitcoind = new Bitcoind(new Client(Config::get('coin.pi.rpc')));
			$result = $bitcoind->validateaddress($address);			
		} catch (Exception $ex) {
			return false;
		}

		return $result;
	}

	/**
	 * [getNewAddress 파이 결제 주소 생성 ]
	 * @return [type] [ address ]
	 */
	public static function getNewAddress() {
		try {		
			$bitcoind = new Bitcoind(new Client(Config::get('coin.pi.rpc')));
			$address = $bitcoind->getnewaddress( Config::get('coin.pi.instant_name') );
		} catch (Exception $ex) {
			return false;
		}

		return $address;
	}

}
