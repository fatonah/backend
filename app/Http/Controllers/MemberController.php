<?php

namespace App\Http\Controllers;
  
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Intervention\Image\Facades\Image;
use Illuminate\Http\Request;
use App\Lib\GoogleAuthenticator;

use DB;
use App\Admin;   


class MemberController extends Controller
{
	 
	#################New Member#########################
	public function member_new(Request $request)
	{   
	$check = Admin::where('username',$request->username)->orWhere('email',$request->email)->first();
	
	if(!$check){
		$ga = new GoogleAuthenticator();	  
	    $secret = $ga->createSecret();
		
		$member = new Admin; 
		$member->name = $request->name;
		$member->username = $request->username;
		$member->role = $request->role; 
		$member->email = $request->email; 
		$member->password = bcrypt($request->password);
		$member->google_auth_code = $secret;
		$member->status = 'active'; 
		$member->save();
		  
		notify()->flash('Sucessfully Create!', 'success', [
		'timer' => 3000,
		'text' => '',
		'buttons' => true
		]);
	
	}else{
		notify()->flash('Username or Email already exist!', 'error', [
		'timer' => 3000,
		'text' => '',
		'buttons' => true
		]);
		
	}
		 
		return redirect()->back();
		
	}
	
	
	#################List Mmeber#########################
	public function member_list()
	{   
		$member = Admin::all(); 
		 
		return view('admin.member.index', compact('member'));
		
	}
		 
	#################Update Member#########################
	public function member_update(Request $request)
	{  
	$check1 = Admin::where('username',$request->username)->count();
	$check2 = Admin::where('email',$request->email)->count();
	 
	 if($check1>=2 || $check2>=2){	
	 notify()->flash('Error!!', 'error', [
		'timer' => 3000,
		'text' => '',
		'buttons' => true
		]);	
		
	 }else{
		$member = Admin::findorFail($request->id);
		$member->name = $request->name;
		$member->username = $request->username;
		$member->role = $request->role; 
		$member->email = $request->email;  
		$member->save();
		
		notify()->flash('Sucessfully Update!', 'success', [
		'timer' => 3000,
		'text' => '',
		'buttons' => true
		]);			 
		 
	 }
	 
		return redirect()->back();
		
	} 
		
	#################Change Password#########################
	public function member_password(Request $request) 
	{  
	 if($request->password==$request->confirm_password){
		$member = Admin::findorFail($request->id);
		$member->password = bcrypt($request->password); 
		$member->save();
		
		notify()->flash('Sucessfully Update!', 'success', [
		'timer' => 3000,
		'text' => '',
		'buttons' => true
		]);	
	 
	 }else{
		notify()->flash('Confirmation Password does not match!', 'error', [
		'timer' => 3000,
		'text' => '',
		'buttons' => true
		]);	
		 
	 }
	 
		return redirect()->back();
	}
		
	#################Delete Member#########################
	public function member_delete(Request $request) 
	{ 
		$ga = new GoogleAuthenticator();	  
	    $secret = $ga->createSecret();
		
		$member = Admin::findorFail($request->id);
		$member->status = 'delete'; 
		$member->google_auth_code = $secret; 
		$member->password = bcrypt($secret); 
		$member->save();
		
		
		notify()->flash('Successfully remove!!', 'success', [
		'timer' => 3000,
		'text' => '',
		'buttons' => true
		]);
		 
		return redirect()->back();
	}
	 
	
}  // tag