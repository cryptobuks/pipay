<?php namespace App;

use Cartalyst\Sentry\Groups\Eloquent\Group as SentryGroup;

class Group extends SentryGroup  {


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
	protected $table = 'groups';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['name', 'permissions'];


	/**
	 * The columns to select when displaying an index.
	 *
	 * @var array
	 */
	public static $index = [ 'id' , 'name' ];
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
	    'name'            => 'required|min:2|max:128',
	    'permissions'                 => 'required',
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
