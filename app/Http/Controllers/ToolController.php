<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cartalyst\Sentry\Sentry;
use App\Http\Requests;
use App\Http\Controllers\Controller;

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
        //
    }

}
