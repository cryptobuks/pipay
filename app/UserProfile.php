<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users_profile';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [ 
		'user_id' , 'email', 'category', 'shop_type', 'company', 'website' , 'phone' , 'logo' , 'agreement' ,
	];


}
