<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cartalyst\Sentry\Sentry;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Invoice;

class PaymentController extends Controller
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
     * Display a index of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $invoices = Invoice::orderBy( 'id' , 'desc' )->paginate(15);

        return view('payments.index', compact('invoices') );
    }

    /**
     * Display a show of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show( $id )
    {
        $invoice = Invoice::find( $id );

        return view('payments.show', compact('invoice'));
    }

    /**
     * Display a receipt of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function receipt( $id )
    {
        $invoice = Invoice::where('token', '=',  $id )->first();

        return view('payments.receipt', compact('invoice'));
    }
    
}
