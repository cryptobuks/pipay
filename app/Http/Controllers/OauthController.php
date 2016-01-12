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
use Validator;
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
        $input = $request->only('email','password');
        $result = [];

        $validator = Validator::make( $input  , [
            'email' => 'required|email|max:128',
            'password' => 'required|min:6',         
        ]);

        if( $validator->fails() ) 
        {
            $messages = $validator->messages();
            if( $messages->first('email') ) {
                return Response::json ( api_error_handler(  'invalid_email' , 'The email is invalid.' ) , 400 );
            } elseif ( $messages->first('password')  )  {
                return Response::json ( api_error_handler(  'invalid_password' , 'The password is invalid.' ) , 400 );            
            } else {
                return Response::json ( api_error_handler(  'invalid_request' , 'The Input format is invalid.' ) , 400 );
            }
        }

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

            return Response::json( [ 'status' => 'success' ,  'id' => Crypt::encrypt( $user->id ) ] , 200 );
        } else {
            return Response::json( [ 'status' => 'failed' ] , 401 );
        }

    }


    public function smsAuth( Request $request )
    {
        $input = $request->only('authcode','cipher_id');

        $validator = Validator::make( $input  , [
            'authcode' => 'required|digits:5',
            'cipher_id' => 'required|min:6',         
        ]);

        if( $validator->fails() ) 
        {
            $messages = $validator->messages();
            if( $messages->first('authcode') ) {
                return Response::json ( api_error_handler(  'invalid_authcode' , 'The authcode is invalid.' ) , 400 );
            } elseif ( $messages->first('cipher_id')  )  {
                return Response::json ( api_error_handler(  'invalid_cipher_id' , 'The cipher_id is invalid.' ) , 400 );            
            } else {
                return Response::json ( api_error_handler(  'invalid_request' , 'The Input format is invalid.' ) , 400 );
            }
        }

        try {
            $id = Crypt::decrypt( $input['cipher_id'] );
        } catch ( DecryptException $e) {
             Response::json( [ 'status' => 'server_decryption' ]  , 400 ) ;
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
            return Response::json( [ 'status' => 'save_failure' ] , 400  );            
        }

        if( $result['success'] == true ) {
            return Response::json( [ 'status' => 'success'  , 'balance' => amount_format( $account->balance ) , 'username' => $user->username , 'email' => $user->email ] , 200  );
        } else {
            return Response::json( [ 'status' => 'failed'  ] , 400  );            
        }

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
        return Response::json( [ 'status' => 'success' ] );
     }

 
}
