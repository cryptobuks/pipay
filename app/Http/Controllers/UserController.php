<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use Cartalyst\Sentinel\Laravel\Facades\Reminder;
use Cartalyst\Sentinel\Checkpoints\NotActivatedException;
use Cartalyst\Sentinel\Checkpoints\ThrottlingException;
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

class UserController extends Controller
{

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware( 'auth' , ['only' => 'create' ] );
    }

    /**
     * Display a user login of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLogin()
    {
        if( $user = Sentinel::check() ) {
            return new RedirectResponse( url('/') );
        } else {
            return view('users.login');
        }
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
            $credentials = [
                'email'    => $input['email'],
                'password' => $input['password'],
            ];

            if( $auth = Sentinel::authenticate($credentials , $request->has('remember') ) )
            {
                $user = Sentinel::check();

                if( $user ) {
                    $result['success'] = true;
                    $result['message'] = trans('users.loggedIn') ;  //'You are now logged in ';         
                } else {
                    $result['success'] = false;
                    $result['message'] = trans('users.invalid') ;    
                }
            }


        }
        catch (NotActivatedException $e)
        {
            $errors = 'Account is not activated!';
            return Redirect::to('/user/resend')->with('user', $e->getUser());
        }
        catch (ThrottlingException $e)
        {
            $delay = $e->getDelay();
            $result['success'] = false;            
            $result['message'] = "Your account is blocked for {$delay} second(s).";
        }        
        catch( Exception $e)
        {
            $result['success'] = false;
            $result['message'] = trans('users.invalid') ;  
        }

        //dd( $result );

        if ( $result['success']  ) 
        {

            Event::fire('users.login', array(
                    'userId' => $user->id,
                    'email' => $user->email
             ));

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
        Sentinel::logout();
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
        if( $user = Sentinel::check() ) {
            return new RedirectResponse( url('/') );
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

            $credentials = [
                'email'    => $input['email'] ,
                'password' => $input['password'] ,
            ];

            if( $user = Sentinel::register($credentials) ) 
            {
                $activation = Activation::create($user);
                $code =  $activation->code;

                $user->permissions = [
                    'user.create' => true,
                    'user.delete' => false,
                ];

                // success!
                // You get activation code and email send to you before you click activation link.
                $result['success'] = true;
                $result['message'] = trans('users.created'); 
            
                $mail = [
                    'email' => $user->email ,               
                    'url' => URL::to(Config::get('app.url','/')),
                    'link' => URL::route('user.activate', ['id' => $user->id , 'code' => $code ]),
                ];

                Event::fire('user.signup', $mail  );

                DB::commit();                
            } else {
                DB::rollback();                
                throw new Exception( 'users.exception' );
            }
                
        } catch( Exception $e ) {
            $result['success'] = false;  
            $result['message'] = trans('users.notactivated');
        }

        flash()->overlay( $result['message']  , 'Message');

        if( $result['success'] == false ) {
            return Redirect::to('/user/register')
            ->withInput()
            ->withErrors('Failed to register.');
        } else {
            return Redirect::to('/');
        }
    }

    /**
     * Show the form for update resource.
     * 
     * @return \Illuminate\Http\View
     */
    public function getProfile()
    {

        $user = Sentinel::getUser();
        if( !$user ) {
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
            'old_password' => 'required|min:6',
            'new_password' => 'required|min:6|confirmed',
            'new_password_confirmation' => 'required' ,
            'category' => 'required' ,
            'shop_type' => 'required' ,                       
            'company' => 'required' ,                                   
            'website' => 'required' , 
            'phone' => 'required' ,             
        ]);

        try{
            
            $user = Sentinel::getUser();

            $data = [
                'password' => $input['new_password'],
                'category' => $input['category'],
                'shop_type' => $input['shop_type'],
                'company' => $input['company'] ,                            
                'website' => $input['website'] ,
                'phone' => $input['phone'] ,
            ];

            Sentinel::update( $user , $data );

            $result['success'] = true;
            $result['message'] =  trans('users.update_profile');             

        } catch( Exception $e) {
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

                $user = Sentinel::findById($id);
                if ( ! Activation::complete($user, $code))
                {
                    $result['success'] = false;                             
                    $result['message']  = trans('users.notactivated');
                } else {
                    $result['success'] = true;  
                    $url = URL::route('user.login');            
                    $result['message'] =  trans('users.activated', array('url' => $url));
                }

        } catch( Exception $e ) {
               $result['success'] = false;  
               $result['message'] = trans('users.notactivated');
        }
        
        if( $result['success'] == true ) 
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
        $reslt = array();

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

                $user = Sentinel::findByCredentials(compact('email'));

                if ( ! $user)
                {
                    throw new Exception( 'users.notfound' );
                }

               $reminder = Reminder::exists($user) ?: Reminder::create($user);
               $code = $reminder->code;

               $result['success'] = true;
               $result['message'] = trans('users.emailinfo');
               $mail = [
                    'email' => e($input['email'])  , 
                    'userId' => $user->id ,
                    'resetCode' => $user->getResetPasswordCode() 
               ] ;

               Event::fire('user.forgot', $mail );

         }
         catch (Exception $e)
        {
                $result['success'] = false;
                $result['message'] = trans( $e->getMessage() );
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
