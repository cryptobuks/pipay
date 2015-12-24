<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cartalyst\Sentry\Sentry;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
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
    public function index( Request $request )
    {
        $invoices = Invoice::orderBy( 'id' , 'desc' )->paginate(15);
        $jsonTable = [];
            
        foreach ( $invoices as $invoice){
            $jsonTable[] = array(
                'id' => $invoice->id,
                'created_at' => $invoice->created_at,
                'item_desc' => $invoice->item_desc,
                'amount' => $invoice->amount,
                'status' => $invoice->status,
                'pi_amount_received' => $invoice->pi_amount_received,
                'pi_amount' => $invoice->pi_amount
            );
        }

        if ($request->ajax()) {
            return $jsonTable;
        }

        return view('payments.index', compact('jsonTable') );
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
