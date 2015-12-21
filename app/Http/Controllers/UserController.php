<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Account;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Cartalyst\Sentry\Sentry;
use Cartalyst\Sentry\Users\UserExistsException;
use Cartalyst\Sentry\Users\LoginRequiredException;
use Cartalyst\Sentry\Users\PasswordRequiredException;
use Cartalyst\Sentry\Users\UserNotFoundException;
use Exception;
use Crypt;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;
use Intervention\Image\Facades\Image;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\RedirectResponse;
use App\UserKey;
use App\UserProfile;
use Illuminate\Contracts\Encryption\DecryptException;

class UserController extends Controller
{

    protected $sentry;
    protected $throttleProvider;    

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct( Sentry $sentry )
    {

        $this->sentry = $sentry;

        // Get the Throttle Provider
        $this->throttleProvider = $this->sentry->getThrottleProvider();

        // Enable the Throttling Feature
        $this->throttleProvider->enable();

        $this->middleware( 'auth' , ['only' => 'getProfile' , 'postProfile'  , 'agreement' , 'postAgreement' , 'postLogo' ] );
    }

    public function decrypt( Request $request  , $crypt ) {

        $input = $request->only( 'lang' , 'livemode' );

        try {
            $decrypted = Crypt::decrypt( $crypt );
            $param = json_decode( $decrypted );
        } catch ( DecryptException $e) {
            //
        }
        return Response::json ( [ $param , $input ] , 200 );
    }

    public function getEncrypt( ) {
        return view('users.encrypt');
    }

    public function encrypt( Request $request ) {

        $input = $request->only( 'item_desc' , 'order_id' , 'currency' , 'amount' , 'settlement_currency' , 'email' , 'redirect' , 'ipn' );

        $param = [];
        foreach ( $input as $k => $v ) {
            $param[] = $v;
        }

        $return = json_encode( $param ,  JSON_UNESCAPED_UNICODE ) ;
        $crypt = Crypt::encrypt( $return );

        return Response::json( [ 'crypt' => $crypt ] , 200 );
    }

    /**
     * Display a user agreement of the resource.
     * @return \Illuminate\Http\Response
     */
    public function agreement()
    {
        return view('users.agreement');
    }

    /**
     * Display a user agreement of the resource.
     * @return \Illuminate\Http\Response
     */
    public function postAgreement()
    {
        $user = $this->sentry->getUser();

        DB::beginTransaction();
        try {
            $user_key = UserKey::keyCreate( $user->id );
            $user_profile = UserProfile::create(
                [  
                    'id' => $user->id , 
                    'email' => $user->email , 
                    'username' => $user->username ,                 
                    'cellphone' => $user->cellphone ,                                 
                    'level' => $user->level ,        
                    'agreement' => 1 ,
                ]
            );

            Account::create( [ 'user_id' => $user->id , 'currency' => 'PI' , 'balance' => 0 , 'locked' => 0  ] );        
            Account::create( [ 'user_id' => $user->id , 'currency' => 'KRW' , 'balance' => 0 , 'locked' => 0  ] );  
            DB::commit();

        } catch ( Exception $e) {
            DB::rollback();

            dd( $e );
        }

        return redirect('dashboard');
    }

    /**
     * Display a user login of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLogin()
    {

        if ($this->sentry->check())
        {
            return new RedirectResponse(url('/'));
        }

        return view('users.login');

    }

    public function postLogin(Request $request )
    {
        $input = $request->all();

        $this->validate( $request , [
            'email' => 'required|email|max:128',
            'password' => 'required|min:6',         
        ]);

        $result['success'] = false;
        $result['message'] = '';

        try
        {
            $credentials = $request->only('email','password');

            //Check for suspension or banned status
            $user = $this->sentry->getUserProvider()->findByLogin( $credentials['email'] );
            $throttle = $this->throttleProvider->findByUserId($user->id);
            $throttle->check();

            // Try to authenticate the user
            $user = $this->sentry->authenticate($credentials , $request->has('remember') );
            $result['success'] = true;
            $result['message'] = trans('users.loggedIn') ;  //'You are now logged in ';         

        }
        catch(\Cartalyst\Sentry\Users\UserNotFoundException $e)
        {
            $result['success'] = false;         
            $result['message'] =  trans('users.invalid');  
        }
        catch (\Cartalyst\Sentry\Users\UserNotActivatedException $e)
        {
            $result['success'] = false;     
            $url = route('user.resend');
            $result['message'] = trans('users.notactive' , ['url' => $url] );   
        }

        // The following is only required if throttle is enabled
        catch (\Cartalyst\Sentry\Throttling\UserSuspendedException $e)
        {
            $result['success'] = false;         
            $result['message'] = trans('users.suspended'); 
        }
        catch (\Cartalyst\Sentry\Throttling\UserBannedException $e)
        {
            $result['success'] = false;         
            $result['message'] = trans('users.banned');             
        }

        if ( $result['success']  ) 
        {
                    Event::fire('user.login', array(
                            'userId' => $user->getId(),
                            'email' => $user->email
                        ));

            $user_key = UserKey::find( $user->id );
            $user_profile = UserProfile::find( $user->id );

            if( !$user_key || !$user_profile ) {
                return redirect('user/agreement');                
            }

            //flash()->overlay( $result['message']  , 'Message');
            return redirect('dashboard');
        } else {
            return redirect($this->loginPath())
                ->withInput($request->only('email', 'remember'))
                ->withErrors([
                    'email' => $result['message'] ,
            ]);
        }

    }

     /**
     * Logout the specified user.
     *
     * @return \Illuminate\Http\Response
     */

     public function getLogout()
     {
        $this->sentry->logout();
        Event::fire('users.logout');        
        return redirect(property_exists($this, 'redirectAfterLogout') ? $this->redirectAfterLogout : '/');      
     }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\View
     */
    public function getRegister()
    {
        if ($this->sentry->check())
        {
            return new RedirectResponse(url('/'));
        }
        
        return view( 'users.register' );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postRegister(Request $request)
    {
        $input = $request->all();

        $this->validate( $request , [
            'email' => 'required|email|max:128|unique:users',
            'password' => 'required|confirmed|min:6',           
        ]);

        $result['success'] = false;

        DB::beginTransaction();

        try
        {
            unset($input['password_confirmation']);

            // User created.
            $input['activated'] = false;
            $user = $this->sentry->register( $input );

            // Update group memberships
            $allGroups = $this->sentry->getGroupProvider()->findAll();
            foreach ($allGroups as $group)
            {
                if ( $group->id == 1 ) 
                {
                    $user->addGroup($group);
                }
            }

            // Account created to each currency.
            $currencies = Currency::all();
            foreach( $currencies as $currency )  Account::create( [ 'user_id' => $user->id , 'currency' => $currency->id , 'balance' => 0 , 'locked' => 0  ] );

            // success!
            // You get activation code and email send to you before you click activation link.
            $result['success'] = true;
            $result['message'] = trans('users.created'); 
            $code = $user->GetActivationCode(); 
                
                $mail = [
                    'email' => $user->email ,               
                    'url' => URL::to(Config::get('app.url','/')),
                    'link' => URL::route('user.activate', ['id' => $user->id , 'code' => $code ]),
                ];

                Event::fire('user.signup', $mail  );
                DB::commit();                
        }         
        catch (LoginRequiredException $e)
        {
            DB::rollback();                            
            $result['success'] = false;
            $result['message'] = trans('users.loginreq');   
        }       
        catch (UserExistsException $e)
        {
            DB::rollback();                                        
            $result['success'] = false;
            $result['message'] = trans('users.exists');  
        }

        flash()->overlay( $result['message']  , 'Message');

        return redirect($this->redirectPath());

    }

    /**
     * [postLogo upload function ]
     * @return [json] [logo_url return]
     */
    public function postLogo( Request $request ){


        $this->validate( $request , [
            'logo' => 'required|mimes:png,jpg,jpeg',
        ]);

        //dd( $request->all() );


        $user = $this->sentry->getUser();

        $result['status'] = 'success' ; 

        try
        {
            $logoName = $user->id . '_' . time() . '_logo.' . $request->file('logo')->getClientOriginalExtension();
            $path = public_path() . '/upload/profile/';
            
            /*
            $request->file('logo')->move( public_path() . '/upload/profile/' , $logoName  );
            */

            Image::make( $request->file('logo')->getRealPath() )->resize(200, 200)->save($path . $logoName);

            $user_profile = UserProfile::find( $user->id );
            $user_profile->logo = $logoName;
            $user_profile->save();

            $result['status'] = 'success' ; 
        } catch ( Exception $e ) {
            $result['status'] = 'error' ; 
            dd( $e );
        }

        if( $result['status'] == 'success') {
            return Response::json ( [
                'logo_url' => url('upload/profile/'. $logoName ) ,
            ]  , 200 ) ;
        } else {
            return Response::json ( [
                'error' => 'upload_failed'  ,
                ] , 200 ) ;
        }
        
    }

    /**
     * Show the form for update resource.
     * 
     * @return \Illuminate\Http\View
     */
    public function getProfile()
    {

      try{
            $user = $this->sentry->getUser();
            if(!$user)
            {
                flash()->overlay( trans('users.notfound') , 'Message' );
                return redirect($this->redirectPath());
            }
            $user_profile = UserProfile::find( $user->id );
            $user_key = UserKey::find( $user->id );

        }
        catch (UserNotFoundException $e)
        {
            flash()->overlay( trans('users.notfound') , 'Message' );
            return redirect($this->redirectPath());         
        }

        $user_categories = Config::get('common.user_categories');
        $user_levels = Config::get('common.user_levels');        
        $user->level_name = $user_levels[$user_profile->level];

        return view( 'users.profile' , compact('user' , 'user_profile' ,  'user_categories'  , 'user_key' ) );
    }

    /**
     * Update user data!
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postProfile( Request $request , $id ) 
    {
        $input = $request->all();

        $this->validate( $request , [
            'category' => 'required|numeric' ,
            'shop_type' => 'required|numeric' ,                       
            'settlement_currency' => 'required|alpha|max:5' ,                       
            'company' => 'required|max:255' ,                                   
            'website' => 'url' , 
            'phone' => 'phone:KR' ,      
            'logo' => 'mimes:png,jpg,jpeg',                   
        ]);

        try{
            
            $user_profile = UserProfile::find( $id );

            $user_profile->category = e( $input['category'] );
            $user_profile->shop_type = e( $input['shop_type'] );
            $user_profile->company = e( $input['company'] );
            $user_profile->website = e( $input['website'] );
            $user_profile->phone = e( $input['phone'] );  
            $user_profile->settlement_currency = e( $input['settlement_currency'] );              

            if( $request->hasFile('logo') ) {
                $logoName = $user_profile->id . '_' . time() . '_logo.' . $request->file('logo')->getClientOriginalExtension();
                $path = public_path() . '/upload/profile/';
                
                Image::make( $request->file('logo')->getRealPath() )->resize(200, 200)->save($path . $logoName);
                $user_profile->logo = $logoName;              
            }

            if ($user_profile->save())
            {
                // User saved
                $result['success'] = true;
                $result['message'] =  trans('users.update_profile'); 
            }
            else
            {
                // User not saved
                $result['success'] = false;                 
                $result['message'] = trans('users.failed_profile'); 
            }

        }
        catch (Exception $e)
        {
            $result['success'] = false;         
            $result['message'] = trans('users.notfound'); 
        }       

        flash()->overlay( $result['message']  , 'Message' );                
        return Redirect::action('UserController@getProfile');

    }

    /**
     * Check the user model.
     *
     * @param mixed $user
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     *
     * @return void
     */
    protected function checkUser($user)
    {
        if (!$user) {
            throw new NotFoundHttpException('User Not Found');
        }
    }

    /**
     * Get the post register / login redirect path.
     *
     * @return string
     */
    public function redirectPath()
    {
        if (property_exists($this, 'redirectPath'))
        {
            return $this->redirectPath;
        }

        return property_exists($this, 'redirectTo') ? $this->redirectTo : '/';
    }

    /**
     * Get the path to the login route.
     *
     * @return string
     */
    public function loginPath()
    {
        return property_exists($this, 'loginPath') ? $this->loginPath : '/user/login';
    }

 
}
