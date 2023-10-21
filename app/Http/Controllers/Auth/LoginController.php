<?php

namespace App\Http\Controllers\Auth;

use Exception;
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
        return Socialite::driver('facebook')->redirect();
    }
    
    //facebook callback  
    public function handleFacebookCallback(){

        try {

            $user = Socialite::driver('facebook')->user();

            $finduser = User::where('provider_id', $user->id)->first();

            if ($finduser) {

                Auth::login($finduser);

                return redirect()->intended('home');
            } else {
                $newUser = User::updateOrCreate(['email' => $user->email], [
                    'name' => $user->name,
                    'provider_id' => $user->id,
                    'password' => encrypt('123456dummy')
                ]);

                Auth::login($newUser);

                return redirect()->intended('home');
            }
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    
    }
}
