<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Cartalyst\Sentry\Sentry;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Product;

class ProductController extends Controller
{

    protected $sentry;

    /**
     * Create a new product controller instance.
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
        $product = Product::orderBy( 'id' , 'desc' )->paginate(15);
        //dd($product);
        return view('products.index', compact('product'));
    }

    /**
     * Display a create of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * Display a edit of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit( $id )
    {
        return view('products.edit');
    }

    /**
     * Display a show of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show( $id )
    {
        return view('products.show');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store( Request $request )
    {
        $input = $request->all();
        
        $user = $this->sentry->getUser();
        $input['user_id'] = $user->id;
        
        unset($input['chk']);
        $input['customer_email'] = (\Input::has('customer_email')) ? true : false;
        $input['customer_name'] = (\Input::has('customer_name')) ? true : false;
        $input['customer_phone'] = (\Input::has('customer_phone')) ? true : false;
        $input['customer_address'] = (\Input::has('customer_address')) ? true : false;
        $input['customer_custom'] = (\Input::has('customer_custom')) ? true : false;

        // dd($input);
        $product = Product::create($input);

        return redirect( 'product' );
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request , $id)
    {

        return redirect( 'product' );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {

        return redirect('product');
    }

}
