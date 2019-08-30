<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class MainController extends Controller
{ 

	#################Activate email####################

	public function activEmail($hash){ 
		$users = User::where('email_hash', $hash)->first();
	 
		if($users){ 
		
		$user = User::findorFail($users->id);
		$user->email_verify = '1';
		$user->save();
		
		$walletBTC = WalletAddress::where('label',$users->label)->where('crypto','BTC')->first();
		if(!$walletBTC){
		//$addressBTC = addCrypto($crypto, $label);
		$addressBTC = 1;
		$wallAddress = new WalletAddress;
		$wallAddress->uid = $user->id;
		$wallAddress->label = $user->label;
		$wallAddress->address = $addressBTC;
		$wallAddress->private_key = '';
		$wallAddress->balance = '0.00000000';
		$wallAddress->crypto = 'BTC';
		$wallAddress->save();
		}
		
		
		$walletBCH = WalletAddress::where('label',$users->label)->where('crypto','BCH')->first();
		if(!$walletBCH){
		//$addressBCH = addCrypto($crypto, $label);
		$addressBCH = 1;
		$wallAddress = new WalletAddress;
		$wallAddress->uid = $user->id;
		$wallAddress->label = $user->label;
		$wallAddress->address = $addressBCH;
		$wallAddress->private_key = '';
		$wallAddress->balance = '0.00000000';
		$wallAddress->crypto = 'BCH';
		$wallAddress->save();
		}
		
		
		$walletDOGE = WalletAddress::where('label',$users->label)->where('crypto','DOGE')->first();
		if(!$walletDOGE){
		//$addressDOGE = addCrypto($crypto, $label);
		$addressDOGE = 1;
		$wallAddress = new WalletAddress;
		$wallAddress->uid = $user->id;
		$wallAddress->label = $user->label;
		$wallAddress->address = $addressDOGE;
		$wallAddress->private_key = '';
		$wallAddress->balance = '0.00000000';
		$wallAddress->crypto = 'DOGE';
		$wallAddress->save();
		}
		
		
		$mesej = "Congratulations!! Successfully Active your account...";
		
		return view('verifyEmail',compact('mesej'));
		
		}else{
			
		$mesej = "Sorry!! Error in your link. Please contact administrator";
		
		return view('verifyEmail',compact('mesej'));
		
		}
	}
   

    /*##### FORGOT PASSWORD form#####*/

    public function resetPassword() {   

       return view('auth.passwords.email', compact('token'));
	}
 

    /*##### FORGOT PASSWORD #####*/

    public function resetPasswordsubmit(Request $request) {   

      $this->validate($request, [
        'email' => 'required|email'
		]);                      

      $email = $request->email;

      $sql_email = User::where('email',$email)->count();

      if($sql_email==0) 
      {  
       $msg = [
        'error' => 'No such user with this email address.',
		];
		return redirect()->back()->with($msg); 

		}
		else 
		{
		  $hash = sha1($email);

		  $user = User::where('email',$email)->first();

		  $id = $user->id;
 
		$email_msj = $user->username.'<p> We received a request to reset your FRIWALLET password, please click link below.</p><p><a href="'.settings('url').'password/reset/'.$hash.'" style="display: inline-block; padding: 11px 30px; margin: 20px 0px 30px; font-size: 15px; color: #fff; background: #4fc3f7; border-radius: 60px; text-decoration:none;">Reset Pasword</a></p>';
		
		  send_email_basic($user->email, 'FRIWALLET', settings('infoemail'), 'FRIWALLET Account Reset Password', $message);

		  $msg = [
			'message' => 'Email with instructions to reset password was sent. Please check your inbox.',
		];
		return redirect()->back()->with($msg);


		}	


	}
	

	public function showResetForm($token)
	{
		return view('auth.passwords.reset', compact('token'));
	}
	

	public function submitPassword(Request $request)
	{
		$this->validate($request, [
			'token' => 'required',
			'password' => 'required|min:6',
		]);

		$check_hash = User::where('email_hash',$request->token)->count();

		if($request->password!=$request->confirmed)
		{ 
			return redirect()->back()->with('error','The password confirmation does not match.');
		}
		else if($check_hash != 0)
		{
			$user = User::where('email_hash',$request->token)->first();

			$id = $user->id;

			User::whereId($id)
			->update([
				'password' => bcrypt($request->password), 
			]);

			return redirect()->back()->with('message','Success, Password successfully changed.');
		} 
		else
		{
			$msg = [
				'error' => 'Token does not exist.',
			];
			return redirect()->back()->with($msg); 
		}
	}
	

	public function showResetpinForm($token)
	{
		return view('auth.secretpin', compact('token'));
	}
	

	public function submitSecretpin(Request $request)
	{
		$this->validate($request, [
			'token' => 'required',
			'secretpin' => 'required|min:6',
		]);

		$check_hash = User::where('email_hash',$request->token)->count();

		if($check_hash != 0)
		{
			$user = User::where('email_hash',$request->token)->first();

			$id = $user->id;

			User::whereId($id)
			->update([
				'secretpin' => $request->secretpin, 
			]);

			return redirect()->back()->with('message','Success, Secret Pin successfully changed.');
		} 
		else
		{
			$msg = [
				'error' => 'Token does not exist.',
			];
			return redirect()->back()->with($msg); 
		}
	}
 
 
 
}
