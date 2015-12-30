<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cartalyst\Sentry\Sentry;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Transaction;
use App\Account;

class LedgerController extends Controller
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
     * Display a index of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request )
    {

        $user = $this->sentry->getUser();
        $user_id  = $user->id;

        $transactions = Transaction::where('user_id', '=', $user_id)->orderBy( 'id' , 'desc' )->paginate(15);
        $transactions->load('account');
        
        $jsonTable = [];
            
        foreach ( $transactions as $transaction){
            $jsonTable[] = array(
                'id' => $transaction->id
            );
        }

        if ($request->ajax()) {
            return $jsonTable;
        }

        return view('ledgers.index', compact('jsonTable'));
    }

}
