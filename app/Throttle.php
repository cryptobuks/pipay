<?php namespace App;

use Cartalyst\Sentry\Throttling\Eloquent\Throttle as SentryThrottle;

class Throttle extends SentryThrottle  {


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
	protected $table = 'throttle';

	/**
	 * Create a new model instance.
	 *
	 * @return void
	 */
	public function __construct()
	{

	}

}
