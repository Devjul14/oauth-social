<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function _registerOrLoginUser($data){
        $user = User::where('email',$data->email)->first();
          if(!$user){
             $user = new User();
             $user->name = $data->name;
             $user->email = $data->email;
             $user->provider_id = $data->id;
             $user->avatar = $data->avatar;
             $user->save();
          }
        Auth::login($user);
        }

    //Facebook Login
    public function redirectToFacebook(){
    return Socialite::driver('facebook')->stateless()->redirect();
    }
    
    //facebook callback  
    public function handleFacebookCallback(){
    
        try{
            //if Authentication is successfull.
            $user = Socialite::driver('facebook')->user();
            /**
             *  Below are fields that are provided by
             *  every provider.
             */
            $provider_id = $user->getId();
            $name = $user->getName();
            $email = $user->getEmail();
            $avatar = $user->getAvatar();
            //$user->getNickname(); is also available
            // return the user if exists or just create one.
            $user = User::firstOrCreate([
                'provider_id' => $provider_id,
                'name'        => $name,
                'email'       => $email,
                'avatar'      => $avatar,
            ]);
            /**
             * Authenticate the user with session.
             * First param is the Authenticatable User object
             * and the second param is boolean for remembering the
             * user.
             */ 
            Auth::login($user,true);
            //Success
            return redirect()->route('home');
        }catch(\Exception $e){
            //Authentication failed
            return redirect()
                ->back()
                ->with('status','authentication failed, please try again!');
        }
    }
}
