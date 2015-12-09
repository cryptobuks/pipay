<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Cartalyst\Sentry\Sentry;
use Cartalyst\Sentry\Users\UserExistsException;
use Cartalyst\Sentry\Users\LoginRequiredException;
use Cartalyst\Sentry\Users\PasswordRequiredException;
use Cartalyst\Sentry\Users\UserNotFoundException;
use Exception;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\RedirectResponse;

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

        $this->middleware( 'auth' , ['only' => 'getProfile' , 'postProfile' , 'getReset' , 'postReset' ] );
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

            //flash()->overlay( $result['message']  , 'Message');
            return redirect('/');
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
     * Delete a user.
     *
     * @return \Illuminate\Http\Response
     */

     public function postDelete(Request $request , $id )
     {
        try
        {
            $user = $this->sentry->findUserById($id);
            $user->delete();

        } catch(UserNotFoundException $e)
        {
            flash()->overlay( trans('users.unableDelete')  , 'Message');
            return redirect($this->redirectPath());
        }

        flash()->overlay( trans('users.deletedUser')  , 'Message');
        return redirect($this->redirectPath());

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
            foreach( $currencies as $currency )  Account::create( [ 'user_id' => $user->id , 'currency_id' => $currency->id , 'balance' => 0 , 'locked' => 0  ] );

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
        }
        catch (UserNotFoundException $e)
        {
            flash()->overlay( trans('users.notfound') , 'Message' );
            return redirect($this->redirectPath());         
        }

        return view( 'users.profile' , compact('user') );
    }

    /**
     * Update user data!
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postProfile( Request $request ) 
    {
        $input = $request->all();

        $this->validate( $request , [
            'category' => 'required' ,
            'shop_type' => 'required' ,                       
            'company' => 'required' ,                                   
            'website' => 'required' , 
            'phone' => 'required' ,             
        ]);

        try{
            
            $user = $this->sentry->getUserProvider()->findById( $id );
        
            $user->category = e( $input['category'] );
            $user->shop_type = e( $input['shop_type'] );
            $user->company = e( $input['company'] );
            $user->website = e( $input['website'] );
            $user->phone = e( $input['phone'] );                                    

            if ($user->save())
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
        catch (UserExistsException $e)
        {
            $result['success'] = false;
            $result['message'] = trans('users.loginExists'); 
        }
        catch (UserNotFoundException $e)
        {
            $result['success'] = false;         
            $result['message'] = trans('users.notfound'); 
        }       

        flash()->overlay( $result['message']  , 'Message' );                
        return Redirect::action('UserController@getProfile');

    }

     /**
     * Activate an existing user.
     *
     * @param int   $id
     * @param string $code 
     *
     * @return \Illuminate\Http\Response
     */

     public function getActivate( $id , $code)
     {
        if( !$id  || !$code ) {
            throw new BadRequestHttpException();
        }

        $result = array();

        try{
            $user = $this->sentry->findUserById($id);
            //print_r($user);
                
            if($user->attemptActivation($code))
            {
                $result['success'] = true;  
                $url = URL::route('user.login');            
                $result['message'] =  trans('users.activated', array('url' => $url));
            } 
            else 
            {
                $result['success'] = false;                             
                $result['message']  =trans('users.notactivated');
            }

        }
        catch(\Cartalyst\Sentry\Users\UserAlreadyActivatedException $e)
        {
            $result['success'] = false;                             
            $result['message']  = trans('users.alreadyactive');
        }
        catch(\Cartalyst\Sentry\Users\UserNotFoundException $e)
        {
            $result['success'] = false;                             
            $result['message']  = trans('users.exists');
        }
        catch (\Cartalyst\Sentry\Users\UserExistsException $e)
        {
            $result['success'] = false;
                $result['message'] =  trans('users.notfound');
        }
        
        if( $result['success'] ) 
        {
            flash()->overlay( $result['message']  , 'Success');
        }
         else 
        {
            flash()->overlay( $result['message']  , 'Failed');
        }

        return redirect('/');        

     }   

     /**
     * Display the resend form.
     *
     * @return \Illuminate\View\View
     */

     public function getResend()
     {
        return view('users.resend');                             
     }   

     /**
     * Queue the sending of the activation email.
     *
     * @return \Illuminate\Http\Response
     */

     public function postResend(Request $request)
     {
        $input = $request->all();
        $result = array();

        if ( ! $user = Sentinel::check())
        {
            return Redirect::to('/user/login');
        }

        $activation = Activation::exists($user) ?: Activation::create($user);
        
//     Activation::complete($user, $activation->code);

        $code = $activation->code;

        $mail = [
            'email' => $user->email ,               
            'url' => URL::to(Config::get('app.url','/')),
            'link' => URL::route('user.activate', ['id' => $user->id , 'code' => $code ]),
        ];

        Event::fire('user.signup', $mail  );

        $result['message'] = trans('users.resend'); 
        $result['success'] = true;          

        flash()->overlay( $result['message'] , 'Message');

        return redirect($this->redirectPath());

     }   

     /**
     * Display the password reset form.
     *
     * @return \Illuminate\View\View
     */

     public function getReset()
     {
        return view('users.reset');                                      
     }   

     /**
     * Queue the sending of the password reset email.
     *
     * @return \Illuminate\Http\Response
     */

     public function postReset(Request $request)
     {
        $input = $request->all();
        $result = array();

        try
        {
            // Find the user using the user email address
            $user = Sentry::findUserByLogin(e($input ('email') ) );

            // Get the password reset code
            $resetCode = $user->getResetPasswordCode();

            // Now you can send this code to your user via email for example.
            $result['success'] = true;
            $result['message']  = trans('users.resendpword');

                $mail = [
                    'email' => e($input['email'])  , 
                    'userId' => $user->getId() ,
                    'resetCode' => $user->getResetPasswordCode() 
                ] ;

            Event::fire('user.forgot', $mail );         

        }
        catch (\Cartalyst\Sentry\Users\UserNotFoundException $e)
        {
            $result['message']  = trans('users.notfound');
        }

        flash()->overlay( $result['message'] , 'Message');
        return redirect($this->redirectPath());

     }   



     /**
     * Display the password forgot Form.
     *
     * @return \Illuminate\View\View
     */

     public function getForgot()
     {
        return view('users.forgot');
     }

     public function postForgot(Request $request)
     {
        $input = $request->all();

        $this->validate( $request , [
            'email' => 'required|email|max:128',
        ]);

        $result = array();

        try
        {
            $user = $this->sentry->getUserProvider()->findByLogin(e($input['email']));

            $result['success'] = true;
                $result['message'] = trans('users.emailinfo');
                $mail = [
                    'email' => e($input['email'])  , 
                    'userId' => $user->getId() ,
                    'resetCode' => $user->getResetPasswordCode() 
                 ] ;

                    Event::fire('user.forgot', $mail );

            }
                catch (\Cartalyst\Sentry\Users\UserNotFoundException $e)
        {
            $result['success'] = false;
                $result['message'] = trans('users.notfound');
        }

        if($result['success'])
        {
            flash()->overlay( $result['message'] , 'Message');
            return redirect($this->redirectPath());
        } else {
            return redirect('/user/forgot')
                ->withErrors([
                    'email' => $result['message'] ,
            ]);
        }


     }

     /**
     * Queue the sending of the password reset email.
     *
     * @return \Illuminate\Http\Response
     */

     public function getPassword( $id , $code )
     {
        if( !$id  || !$code ) {
            throw new BadRequestHttpException();
        }

        $result = array();
        try
        {
            // Find the user
            $user = $this->sentry->getUserProvider()->findById($id);
            $newPassword = _generatePassword(8,8);

            // Attempt to reset the user password
            if ($user->attemptResetPassword($code, $newPassword))
            {
                // Email the reset code to the user
                    $result['success'] = true;
                    $result['message'] = trans('users.emailpassword');
                    $mail = [
                        'email' => $user->getLogin() ,                  
                        'newPassword' => $newPassword , 
                     ] ;

                Event::fire('user.newpassword', $mail );

            }
            else
            {
                // Password reset failed
                $result['success'] = false;
                $result['message'] = trans('users.problem');
            }
        }
        catch (\Cartalyst\Sentry\Users\UserNotFoundException $e)
        {
            $result['success'] = false;
            $result['message'] = trans('users.notfound');
        }

        if( $result['success'] ) 
        {
            flash()->overlay( $result['message']  , 'Success');
            return redirect('/');
        }
         else 
        {
            flash()->overlay( $result['message']  , 'Failed');
            return redirect('/');
        }

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
