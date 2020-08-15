<?php

namespace App\Http\Controllers\AuthAdmin;
 
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers; 
use Illuminate\Http\Request;  
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Admin;

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
	protected $guard = 'admin';

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
   // protected $redirectTo = 'dashboard';

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

    	return view('admin.auth.login');
    }
	
	public function login(Request $request){


    	//validate the form data
     	$this->validate($request,[
     			'email' => 'required',
     			'password' => 'required'

     	]);
 
		$user = Admin::where('username',$request->email)->orWhere('email',$request->email)->count();
		$users = Admin::where('username',$request->email)->orWhere('email',$request->email)->first();
		
        if($user != 0)
        {
			if(!Hash::check($request->password, $users->password)){
				  
				return redirect()->back()->with('error','Wrong Password.');
			} 
			else{
                Auth::guard('admin')->login($users); 
                    //return redirect('admin/authy/admin'); 
					return redirect('admin/dashboard');
			} 
		}else{
			return redirect()->back()->with('error','Wrong Email or Username.'); 
		}

    	//attempt to login 
     	//if(Auth::guard('admin')->attempt($credentials, $remember)){
     	// or
		//if(Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password])){
 
		//if successful, then redirect to their intendend loacation
	//		 return redirect()->intended(route('authy.admin'));

	//	}else{

    	//if unsuccessful, then redirect back to the login with form data 
	// return back()->withErrors(['email' => 'Email or password are wrong.']);
	//	}
	}
	 

	public function logout()
    { 
        Auth::guard('admin')->logout();
        return redirect('/admin');
    
    }
	
	
}
