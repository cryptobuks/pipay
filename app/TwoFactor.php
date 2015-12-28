<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use App\User;
use App\Jobs\SendSms;
use Carbon\Carbon;
use Illuminate\Support\Facades\Queue;
use Validator;


class TwoFactor extends Model
{

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'two_factors';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [ 
		'user_id', 'opt_secret', 'activated','type', 'last_verify_at', 'refreshed_at' , 
	];

	/**
	 * The user validation rules.
	 *
	 * @var array
	 */
	public static $rules = [
	];

	public $timestamps = false;

	public function ouser()
	{
		return $this->belongsTo('App\Ouser' );
	}

	public static function getType( Ouser $user , $type  )
	{
		$param = [
			'user_id' => $user->id ,
			'type' => $type,
		];

		return static::firstOrCreate( $param ); 
	}

	public function active(){
		$this->activated = true;
		$this->last_verify_at = Carbon::now();
		$this->save();		
	}

	public function deactive(){
		$this->activated = false;
		$this->save();		
	}

	public function gencode(){
		return getRandomString();
	}

	public function gen_code( $length = 5 ){
		$this->opt_secret = getRandomNumber( $length );
		$this->refreshed_at = Carbon::now();
	}

	public function send_sms( Ouser $user ){
		if( !$this->expired() ) $this->refresh();
		Queue::push( new SendSms(  $user->cellphone , trans( 'users.sms_auth' ,  array( 'authcode' => $this->opt_secret ) )  )  );
	}

	public function refresh(){
		$this->gen_code();
		$this->save();
	}

	public function expired(){
		if( $this->refreshed_at ) {
			$dt = new Carbon( $this->refreshed_at );
			$dt->addSeconds( TWO_FACTOR_SINCE_TIME );

			return  $dt->diffInSeconds( Carbon::now()  , false )  < 0 ;
		} else {
			return 0;
		}
	}

	public function verify( $authcode ){
		if( $this->expired() && $authcode == $this->opt_secret ) {
			$this->last_verify_at = Carbon::now();
			$this->save();

			return [ 'success' => true ] ;
		} else {
			return [ 'success' => false , 'error' => 'authcode_invalid' , 'message' => trans('users.authcode_invalid')  ] ;				
		}
	}

	public function valid_phone_number( Ouser $user ) {
		
		$validator = Validator::make( 
			 [ 'phone' => $user->cellphone ] ,
			 [ 'phone' => 'required|phone:KR,mobile', ]
		 );

		if( $validator->fails() ){
			return [ 'success' => false , 'error' => 'invalid_phone' , 'message' => 'The Cellphone format is invalid.'  ] ;							
		}

		return [ 'success' => true ] ;
	}
    
}
