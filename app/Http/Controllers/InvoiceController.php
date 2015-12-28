<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cartalyst\Sentry\Sentry;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\UserKey;
use App\UserProfile;
use App\Invoice;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;
use Validator;
use Carbon\Carbon;

class InvoiceController extends Controller
{

    protected $sentry;
    
    /**
     * Create a new checkout controller instance.
     *
     * @return void
     */
    public function __construct( Sentry $sentry)
    {

        $this->sentry = $sentry;
        $this->middleware( 'guest'  );

    }

    /**
     * Display a index of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request  , $token )
    {
        $invoice = Invoice::where( 'token' , $token )->first();

        if( $invoice ) {
            return view('invoice.index' , compact ( 'invoice' , 'token') );
        } else {
            return "server error!!!";
        }
    }

    /**
     * [payment description]
     * @param  Request $request [description]
     * @param  [type]  $token   [description]
     * @return [type]           [description]
     */
    public function payment( Request $request , $token )
    {
        $input = $request->only( 'id' );

        // 입력값 검증
        $validator = Validator::make( $input , [
            'id'  => 'required|number',
        ]);

        if( $validator->fails() ) 
        {
            $messages = $validator->messages();
            if( $messages->first('id') ) {
                return Response::json ( api_error_handler(  'invalid_api_key' , 'The api_key is invalid.' ) , 400 );
            } else {
                return Response::json ( api_error_handler(  'invalid_request' , 'The Input format is invalid.' ) , 400 );
            }
        }

        return [ 'token' => $token ];
    }
    
}
