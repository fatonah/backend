<?php

namespace App\Http\Controllers;
  
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Intervention\Image\Facades\Image;
use Illuminate\Http\Request;
use App\Lib\GoogleAuthenticator;

use DB;
use App\User;   


class UserController extends Controller
{
	   
	#################List Mmeber#########################
	public function member_list()
	{   
		$member = User::where('username','!=','admin')->get(); 
		 
		return view('admin.user.index', compact('member'));
		
	}
		 
	#################Update SecretPin#########################
	public function resetpin_update(Request $request)
	{  
		$row = User::where('id',$request->id)->first(); 
		$email = $row->email;
		$msubject = '['.settings('name').'] Reset secret PIN request';
		$mreceiver = $email; 
		  
		$ass = settings('url').'secretpin/reset/'.$row->email_hash;  
		$link_reset = '<a href="'.$ass.'" target="_blank"  style="display: inline-block; padding: 11px 30px; margin: 20px 0px 30px; font-size: 15px; color: #fff; background: #4fc3f7; border-radius: 60px; text-decoration:none;">Reset Secret Pin</a>';
		
		$message = 'Hello, '.$row->username.'<p></p>
		
		We received a request to reset secret PIN in our website. To change it click on link below.<p></p>
		'.$link_reset.'<p></p>

		If this email was not requested by you, please ignore it.<p></p>
			
		If you have some problems please feel free to contact with us on '.settings('supportemail');
		   
		send_email_basic002($mreceiver, $msubject, $row->username, $message);
		
		notify()->flash('Reset secret Pin!', 'success', [
			'timer' => 3000,
			'text' => '',
			'buttons' => true
		]);			 
		  
		return redirect()->back();
		
	} 
	
	#################List Transaction#########################
	public function user_transaction($label)
	{   
	
		return view('admin.user.transaction', compact('label'));
		
	}
		 
	
}  // tag