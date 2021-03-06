<?php namespace App;

use Cartalyst\Sentry\Groups\GroupInterface;
use Cartalyst\Sentry\Users\Eloquent\User as SentryUser;
use McCool\LaravelAutoPresenter\HasPresenter;

class User extends SentryUser  {


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
	protected $table = 'users';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['username', 'email', 'password'];


	/**
	 * The columns to select when displaying an index.
	 *
	 * @var array
	 */
	public static $index = ['id', 'email', 'username'];
	/**
	 * The max users per page when displaying a paginated index.
	 *
	 * @var int
	 */
	public static $paginate = 20;
	/**
	 * The columns to order by when displaying an index.
	 *
	 * @var string
	 */
	public static $order = 'id';
	/**
	 * The direction to order by when displaying an index.
	 *
	 * @var string
	 */
	public static $sort = 'desc';

	/**
	 * The user validation rules.
	 *
	 * @var array
	 */
	public static $rules = [
	    'username'            => 'required|min:2|max:128',
	    'email'                 => 'required|min:4|max:128|email',
	    'password'              => 'required|min:6|confirmed',
	    'password_confirmation' => 'required',
	    'activated'             => 'required',
	    'activated_at'          => 'required',
	];

	/**
	 * Create a new model instance.
	 *
	 * @return void
	 */
	public function __construct()
	{

	}

}
