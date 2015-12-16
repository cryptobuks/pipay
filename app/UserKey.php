<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;
use Response;

class UserKey extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users_key';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [ 
		'id', 'live_api_key', 'test_api_key' ,
	];

	/**
	 * [keyCreate description]
	 * @param  [type] $user_id [ user's id ]
	 * @return [type]          [ return userKey object ]
	 */
	public static function keyCreate( $user_id ) {

		$param = [
			'id' => $user_id , 
			'live_api_key' => 'sk_live_' . Hashids::connection('main')->encode($user_id) , 
			'test_api_key' => 'sk_test_' . Hashids::connection('alternative')->encode($user_id) , 
		];

		return static::firstOrCreate( $param ); 
	}

	/**
	 * [authenticate description]
	 * @param  [type]  $credentials   [ user's api_key ]
	 * @param  boolean $test_mode [ test_mode check]
	 * @return [type]             [ return userKey ]
	 */
	public static function authenticate( $credentials ) {
		$api_key = $credentials['api_key'];
		$livemode = $credentials['livemode'];

		if( $livemode == '1' ) {
			$userKey = $this->whereLiveApiKey ( $api_key )->first();
		} else {
			$userKey = $this->whereTestApiKey ( $api_key )->first();
		}

		return static::$userKey;		
	}
}
