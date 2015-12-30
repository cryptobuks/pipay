<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Ledger;
use Exception;
use DB;

class Account extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'accounts';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'user_id', 'currency', 'balance', 'locked'
	];

	protected $reason = ACCOUNT_FIX;
	protected $fun = 0;
	protected $fee = 0;	
	protected $opts = NULL;

	public function legder()
	{
		return $this->hasMany('App\Legder' , 'account_id');
	}

	public function userProfile()
	{
		return $this->belongsTo('App\UserProfile');
	}

	public function change_balance( $balance , $locked  ) 
	{
		$this->balance += $balance;
		$this->locked += $locked;
		$this->save();

		$this->history_create( $balance , $locked  );		
	}

	public function amount(){
		return $this->balance + $this->locked;
	}		

	public function history_create( $balance , $locked )
	{
		$ledger = [
			'user_id' => $this->user_id,
			'account_id' => $this->id,
			'currency' => $this->currency ,
			'fun' => $this->fun,
			'reason' => $this->reason,
			'balance' => $balance ,
			'locked' => $locked ,
			'fee' => $this->fee ,
			'amount' => $this->amount(),
			'modifiable_id' => $this->opts->id,
			'modifiable_type' => get_class($this->opts),
		];

		return Ledger::firstOrCreate( $ledger );
	}

	public function plus_funds ( $amount , $fee = 0 , $reason , $obj )
	{
		if( $amount <= 0 || $fee > $amount ){
	 		throw new Exception("cannot add funds (balance: {$amount} )");
		} 

		$this->reason = $reason;
		$this->fun = PLUS_FUNDS;
		$this->fee = $fee;
		$this->opts = $obj;

		$this->change_balance( $amount , 0  );

	}

	public function sub_funds ( $amount , $fee = 0  , $reason , $obj )
	{
		if( $amount <= 0 || $amount > $this->balance ) {
	 		throw new Exception("cannot subtract funds (amount: {$amount} )");
		}

		$this->reason = $reason;
		$this->fun = SUB_FUNDS;
		$this->fee = $fee;		
		$this->opts = $obj;

		$this->change_balance( -$amount , 0  );
	}

	public function lock_funds ( $amount  , $reason , $obj )
	{
		if( $amount <= 0 || $amount > $this->balance ) {
	 		throw new Exception("cannot lock funds (amount: {$amount} )");
		}

		$this->reason = $reason;
		$this->fun = LOCK_FUNDS;
		$this->opts = $obj;

		$this->change_balance( -$amount , $amount  );
	}

	public function unlock_funds ( $amount  , $reason , $obj )
	{

		if( $amount <= 0 || $amount > $this->locked ) {
	 		throw new Exception("cannot unlock funds (amount: {$amount} )");
		}

		$this->reason = $reason;
		$this->fun = UNLOCK_FUNDS;
		$this->opts = $obj;

		$this->change_balance( $amount , -$amount  );
	}

	public function unlock_and_sub_funds ( $amount , $locked = 0 , $fee = 0 , $reason ,$obj )
	{
		if( $amount <= 0 || $amount > $locked ) {
	 		throw new Exception("cannot unlock and subtract funds (amount: {$amount} )");
		}

		if( $locked <= 0 || $locked > (float)$this->locked ) {
	 		throw new Exception("invalid lock balance (amount: {$amount}, locked: {$locked}, this.locked: {$this->locked} )");
		}

		$this->reason = $reason;
		$this->fee = $fee;		
		$this->fun = UNLOCK_AND_SUB_FUNDS;
		$this->opts = $obj;

		$this->change_balance( $locked - $amount , -$locked  );
	}

	public function account_sum_per_day( $user_id , $currency , $reason ) 
	{
		$balance = DB::table('account_histories')
		->whereRaw( "user_id = ? AND currency = ? AND reason = ? AND DATE_FORMAT(updated_at, '%Y-%m-%d') = DATE_FORMAT( NOW(), '%Y-%m-%d')" , [ $user_id , $currency , $reason ] )
		->sum('balance');

		return $balance;
	}

    
}
