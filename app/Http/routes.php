<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', ['as' => 'home.index', 'uses' => 'HomeController@index'] );

// login routes
$router->get('user/login', ['as' => 'user.login', 'uses' => 'UserController@getLogin']);
$router->post('user/login', ['as' => 'user.login.post', 'uses' => 'UserController@postLogin']);
$router->get('user/logout', ['as' => 'user.logout', 'uses' => 'UserController@getLogout']);

$router->get('user/register', ['as' => 'user.register', 'uses' => 'UserController@getRegister']);
$router->post('user/register', ['as' => 'user.register.post', 'uses' => 'UserController@postRegister']);

$router->get('user/profile', ['as' => 'user.profile', 'uses' => 'UserController@getProfile']);
$router->post('user/profile', ['as' => 'user.profile.post', 'uses' => 'UserController@postProfile']);


// activation routes
$router->get('user/activate/{id}/{code}', ['as' => 'user.activate', 'uses' => 'UserController@getActivate'])->where('id', '\d+');
$router->get('user/resend', ['as' => 'user.resend', 'uses' => 'UserController@getResend']);
$router->post('user/resend', ['as' => 'user.resend.post', 'uses' => 'UserController@postResend']);

// forgot routes
$router->get('user/forgot', ['as' => 'user.forgot', 'uses' => 'UserController@getForgot']);
$router->post('user/forgot', ['as' => 'user.forgot.post', 'uses' => 'UserController@postForgot']);
$router->get('user/password/{id}/{code}', ['as' => 'user.password', 'uses' => 'UserController@getPassword']);

// reset routes
$router->get('user/reset', ['as' => 'user.reset', 'uses' => 'UserController@getReset']);
$router->post('user/reset', ['as' => 'user.reset.post', 'uses' => 'UserController@postReset']);

// User login event
Event::listen('users.login', function($userId, $email)
{
    Session::put('userId', $userId);
    Session::put('email', $email);
});

// User logout event
Event::listen('users.logout', function()
{
    Session::flush();
});

// Subscribe to User Mailer events
Event::subscribe('App\Events\UserMailer');
Event::subscribe('App\Events\TransactionMailer');

Route::get('locale', [
    'as' => 'global.locale',
    function () {
        $cookie = cookie()->forever('l5locale', Request::input('l5locale'));

        Cookie::queue($cookie);

        return ($return = Request::input('return'))
            ? redirect(urldecode($return))->withCookie($cookie)
            : redirect()->route('home.index')->withCookie($cookie);
    } 
]);

