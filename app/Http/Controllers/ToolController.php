<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cartalyst\Sentry\Sentry;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Crypt;

class ToolController extends Controller
{

    protected $sentry;


    /**
     * Create a new payment controller instance.
     *
     * @return void
     */
    public function __construct( Sentry $sentry)
    {
        $this->sentry = $sentry;
        $this->middleware( 'auth'  );

    }


    /**
     * Display a tool home of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('tools.index');
    }

    /**
     * Show the form for creating a generateButton resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function generateButton()
    {
        return view('tools.generate_button');
    }

    /**
     * Show the form for creating a generateLink resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function generateLink()
    {
        return view('tools.generate_link');
    }

    /**
     * encrypt parameter resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function encrypt(Request $request)
    {
        
        $input = $request->only( 'item_desc' , 'order_id' , 'currency' , 'amount'  , 'email' , 'redirect' , 'ipn' );

        // $this->validate( $request , [
        //     'item_desc' => 'required|min:2',
        //     'order_id' => 'alpha_num',
        //     'amount' => 'required|min:1|numeric',
        //     'email' => 'email|max:128',
        //     'redirect' => 'url',
        //     'ipn' => 'url'
        // ]);
        // dd($input);
        
        $param = [];
        foreach ( $input as $k => $v ) {
            if(!empty( $v ) ) $param[$k] = $v;
        }

        $user = $this->sentry->getUser();
        $user_key = UserKey::find( $user->id );
        $param['api_key'] = $user_key->live_api_key;

        $return = json_encode( $param ,  JSON_UNESCAPED_UNICODE ) ;
        $crypt = Crypt::encrypt( $return );

        return Response::json( [ 'crypt' => $crypt ] , 200 );
    }

}
