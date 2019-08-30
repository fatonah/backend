<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;  
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\User;

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
   // protected $redirectTo = 'users/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
	
	public function showLoginForm(){

    	return view('auth.login');
    }
	
	public function login(Request $request){


    	//validate the form data
     	$this->validate($request,[
     			'email' => 'required',
     			'password' => 'required'

     	]);
 
		$user = User::where('username',$request->email)->orWhere('email',$request->email)->count();
		$users = User::where('username',$request->email)->orWhere('email',$request->email)->first();
		
        if($user != 0)
        {
			if(!Hash::check($request->password, $users->password)){
				  
				return redirect()->back()->with('error','Wrong Password.');
			} 
			else{
				Auth::login($users);
                    return redirect('authy');
			} 
		}else{
			return redirect()->back()->with('error','Wrong Email or Username.'); 
		}
	}
	
	public function logout()
    {
        auth()->logout();
        session()->flush();
 
		return redirect('/');
      
    }
	
}
