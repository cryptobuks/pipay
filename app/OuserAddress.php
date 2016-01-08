<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Nbobtc\Bitcoind\Bitcoind;
use Nbobtc\Bitcoind\Client;
use Config;

class OuserAddress extends Model {

	/**
	 * The connection name for the model.
	 *
	 * @var string
	 */
	protected $connection = 'pi';

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'user_addresses';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [ 
		'user_id', 'account_id' , 'currency_id', 'address'
	];

	/**
	 * The user validation rules.
	 *
	 * @var array
	 */
	public static $rules = [
	    'user_id'            => 'required|integer',
	    'currency_id'            => 'required|integer',	    
	    'address'            => 'required',
	];


	public function ouser()
	{
		return $this->belongsTo('App\Ouser');
	}

	/**
	 * [getValidateAddress 파이 주소 검증 ]
	 * @param  [type] $address [파이 주소]
	 * @return [boolean]          [결과값]
	 */
	public function getValidateAddress( $address ) {
		try {
			$bitcoind = new Bitcoind(new Client(Config::get('coin.pi.rpc')));
			$result = $bitcoind->validateaddress($address);			
		} catch (Exception $ex) {
			return false;
		}

		return $result;
	}

}
