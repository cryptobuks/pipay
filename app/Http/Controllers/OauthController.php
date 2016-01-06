<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Ouser;
use App\Account;
use App\Oaccount;
use App\UserKey;
use App\UserProfile;
use App\TwoFactor;
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


    /**
     * [loginOnce description]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
   public function loginOnce(Request $request )
    {
        $input = $request->all();
        $result = [];

        $this->validate( $request , [
            'email' => 'required|email|max:128',
            'password' => 'required|min:6',         
        ]);

        $credentials = $request->only('email','password');

        if (Auth::once($credentials)) {
            
            $user = Auth::user();

            DB::beginTransaction();
            try {

                $sms = TwoFactor::getType( $user , TWO_FACTOR_TYPE_SMS );
                $result = $sms->valid_phone_number( $user ) ;
                
                if( $result['success'] == true ) {
                    $sms->active();
                    $sms->send_sms( $user );
                }

                DB::commit();

            }  catch (Exception $e) {
                DB::rollback();
                return Response::json (  [ 'status' => 'save_failure' ] , 401 );
            }

            return Response::json( [ 'status' => 'success' ,  'id' => Crypt::encrypt( $user->id ) ] );
        } else {
            return Response::json( [ 'status' => 'failed' ]);
        }

    }


    public function smsAuth( Request $request )
    {
        $input = $request->all();

        $this->validate( $request , [
            'authcode' => 'required|digits:5',
            'cipher_id' => 'required|min:6',         
        ]);

        $input = $request->only('authcode','cipher_id');

        try {
            $id = Crypt::decrypt( $input['cipher_id'] );
        } catch ( DecryptException $e) {
             Response::json( [ 'status' => 'server_decryption' ] ) ;
        }

        $user = Ouser::find( $id );
        $account = Oaccount::whereUserId( $user->id )->whereCurrencyId( CURRENCY_COIN )->first();

        $result = 'failed' ;
        DB::beginTransaction();
        try {

            $sms = TwoFactor::getType( $user , TWO_FACTOR_TYPE_SMS );
            $result = $sms->verify( $input['authcode'] );            

            if( $result['success'] == true ) {
                if( $this->loginUser( $id ) == true ) {
                    $result = 'success' ;
                }
            }

           DB::commit();

        }  catch (Exception $e) {
            DB::rollback();
            return Response::json( [ 'status' => 'save_failure' ] );            
        }

        return Response::json( [ 'status' => $result  , 'balance' => amount_format( $account->balance ) , 'username' => $user->username , 'email' => $user->email ] );

    }

   public function loginUser( $id )
    {
        if (Auth::loginUsingId( $id )) {
            return true  ;
        } else {
            return false  ;
        }

    }


     /**
     * Logout the specified user.
     *
     * @return \Illuminate\Http\Response
     */

     public function logout( )
     {
        Auth::logout(); 
        return back();                     
     }

 
}
