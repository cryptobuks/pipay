<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cartalyst\Sentry\Sentry;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Refund;

class RefundController extends Controller
{

    protected $sentry;
    
    /**
     * Create a new ledger controller instance.
     *
     * @return void
     */
    public function __construct( Sentry $sentry)
    {
        $this->sentry = $sentry;

        $this->middleware( 'auth'  );

    }

    /**
     * Display a refund of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( $id )
    {
        return view('payments.refund');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store( Request $request  )
    {
        $input = $request->all();
        
        $user = $this->sentry->getUser();
        $input['user_id'] = $user->id;
        $input['pi_amount'] = $input['amount'];
        $input['amount'] = $input['pi_amount'] * 10000;
        $input['currency'] = 'PI';

        $refund = Refund::create($input);

        return redirect( 'payments' );
    }

}
