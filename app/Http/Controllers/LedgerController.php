<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cartalyst\Sentry\Sentry;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Transaction;

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
    public function index()
    {
        $transactions = Transaction::orderBy( 'id' , 'desc' )->paginate(15);
        $transactions->load('account');
        dd($transactions);
        return view('ledgers.index', compact('transactions'));
    }

}
