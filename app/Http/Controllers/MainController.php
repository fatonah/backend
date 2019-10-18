<?php

namespace App\Http\Controllers;

use App\User;
use App\WalletAddress;
use Illuminate\Http\Request;

class MainController extends Controller
{ 

	#################Activate email####################

	public function activEmail($hash) {
		$users = User::where('email_hash', $hash)->first();
		if($users){ 
		
			$user = User::findorFail($users->id);
			$user->email_verify = '1';
			$user->save();
			  
			if(is_null($users->mnemonic) || $users->mnemonic == ''){
				$upt = User::findorFail($user->id);
				$upt->mnemonic = genseed($users->id);
				$upt->save(); 
			}

			$mesej = "Congratulations!! Successfully Active your account...";
			return view('verifyEmail',compact('mesej'));
			
		}
		else{
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

		if($sql_email==0) {
			$msg = [
				'error' => 'We cannot find a user with that e-mail address.',
			];
			return redirect()->back()->with($msg);
		}
		else {
			$user = User::where('email',$email)->first();
			$hash = $user->email_hash;
			$id = $user->id;
			$email_msj = $user->username.'<p> We received a request to reset your DORADO password, please click link below.</p><p><a href="'.settings('url').'password/reset/'.$hash.'" style="display: inline-block; padding: 11px 30px; margin: 20px 0px 30px; font-size: 15px; color: #fff; background: #4fc3f7; border-radius: 60px; text-decoration:none;">Reset Pasword</a></p>';
			send_email_basic002($user->email, 'DORADO Account Reset Password', $user->username, $email_msj);
			$msg = [
				'message' => 'Email with instructions to reset password was sent. Please check your inbox.',
			];
			return redirect()->back()->with($msg);
		}
	}
	

	public function showResetForm($token) {
		return view('auth.passwords.reset', compact('token'));
	}
	

	public function submitPassword(Request $request) {
		$this->validate($request, [
			'token' => 'required',
			'password' => 'required|min:8',
			'confirmed' => 'required|min:8',
		]);

		$check_hash = User::where('email_hash',$request->token)->count();
		$pword1 = preg_match("/[a-zA-Z0-9]/", $request->password); 
		$pword2 = preg_match("/[^\da-zA-Z]/", $request->password);
			
		if($request->password!=$request->confirmed)
		{ 
			return redirect()->back()->with('error','The password confirmation does not match.');
		}
		elseif(!$pword1) 
		{ 
			return redirect()->back()->with('error','Password must have at least one symbol, one capital letter,one number, one letter.'); 
		}
		elseif(!$pword2) 
		{ 
			return redirect()->back()->with('error','Password must have at least one symbol, one capital letter,one number, one letter.'); 
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
	

	public function showResetpinForm($token) {
		return view('auth.secretpin', compact('token'));
	}
	

	public function submitSecretpin(Request $request) {
		$this->validate($request, [
			'token' => 'required',
			'secretpin' => 'required',
		]);

		$check_hash = User::where('email_hash',$request->token)->count();
		$secret_pin2 = preg_match('/^[0-9]{6}$/', $request->secretpin);

		if($check_hash != 0) {
			if(strlen($request->secretpin)!=6) { 
				return redirect()->back()->with('error','Secret PIN must be 6 digits.');
			}
			else if(!$secret_pin2) {
				return redirect()->back()->with('error','Secret PIN must be digits only.');
			}
			else{
				$user = User::where('email_hash',$request->token)->first();

				$id = $user->id;

				User::whereId($id)
				->update([
					'secretpin' => $request->secretpin, 
				]);

				return redirect()->back()->with('message','Secret Pin successfully changed.');
			}
		} 
		else {
			$msg = [
				'error' => 'Token does not exist.',
			];
			return redirect()->back()->with($msg); 
		}
	}
}
