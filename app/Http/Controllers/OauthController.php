<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Account;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Exception;
use Crypt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\RedirectResponse;
use App\UserKey;
use App\UserProfile;
use Illuminate\Contracts\Encryption\DecryptException;

class OauthController extends Controller
{

    protected $auth;

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct( Auth $auth )
    {

        $this->auth = $auth;

        $this->middleware( 'guest' );
    }

   public function login(Request $request )
    {
        $input = $request->all();

        $this->validate( $request , [
            'id' => 'required',
        ]);

        $id = $request->input('id');

        if (Auth::loginUsingId($id)) {
            return Response::json( [ 'status' => 'success' ,  'test' => 'test123' ] );
        } else {
            return Response::json( ['status' => 'failed' ]);
        }

    }


   public function loginOnce(Request $request )
    {
        $input = $request->all();

        $this->validate( $request , [
            'email' => 'required|email|max:128',
            'password' => 'required|min:6',         
        ]);

        $credentials = $request->only('email','password');

        if (Auth::attempt($credentials)) {
//        if (Auth::once($credentials)) {
            return Response::json( [ 'status' => 'success' ,  'id' => Auth::user()->id ] );
        } else {
            return Response::json( [ 'status' => 'failed' ]);
        }

    }

     /**
     * Logout the specified user.
     *
     * @return \Illuminate\Http\Response
     */

     public function logout()
     {
        Auth::logout();        
     }

 
}
