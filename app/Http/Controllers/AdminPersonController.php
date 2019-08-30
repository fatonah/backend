<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Lib\GoogleAuthenticator;
 
 use App\Admin;
 

class AdminPersonController extends Controller
{
    
	public function admin_person()
	{
		$uid = Auth::guard('admin')->user()->id;
		$user = Admin::where('id',$uid)->first();
		$issuer = 'Admin';
	 
		return view('admin.member.personal', compact('user','issuer'));
	}

	public function personal_password(Request $request)
	{
		$this->validate($request, [
			'password' =>'required',
			'confirmed' =>'required',
		]);
		
			$uid = Auth::guard('admin')->user()->id;
			
		if($request->password!=$request->confirmed){ 
 
		$msg = [
			'error' => 'The password confirmation does not match.',
		];
		return redirect()->back()->with($msg);

		} 
		else {

			Admin::whereId($uid)
			->update([
				'password' => bcrypt($request->password), 
			]);
			
		$msg = [
			'message' => 'Success, Password successfully changed.',
		];
		return redirect()->back()->with($msg);
		}
	}
	  
	
	

}//
