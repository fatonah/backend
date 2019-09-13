<?php

namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator; 
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Images;
use App\Lib\GoogleAuthenticator;
use Denpa\Bitcoin\LaravelClient as BitcoinClient;

use DB;
use App\User;
use App\State;
use App\Currency;
use App\PriceCrypto;
use App\WalletAddress; 
use App\Withdrawal; 
use App\MenuTicket;
use App\Messages; 
use App\Ticket; 
use App\Setting; 
use App\Appver;

class ApiController extends Controller{ 
	
	#################Debug #########################
	public function debug(){
		//BTC//
		$crypto = 'BTC';
		$label = 'usr_bsod666';
		$address = '3K97qigXj6TA1mEiAw47TSs5hdg6caHqcn';
		$txid = 'b1c5dfef6ba6252e0497a904005417098e3fae6857019cfdf705d452f247569b';

		// // //BCH//
		// $crypto = 'BCH';
		// $label = 'usr_bsod666';
		// $address = 'qztrk7m57450h65qffhjrd6ekaams3kas5ecpw6pzz';
		// $txid = '53c0b56f1f46046d328666ba1e86897da8b88df1da259f4b8c3ed49b1fd08114';

		// //DOGE//
		// $crypto = 'DOGE';
		// $label = 'usr_bsod666';
		// $address = 'DKzRr2pUGLVQRe2Csr7Y1znDhGtB1eBZLw';
		// $txid = '989b981221a1cc860d509a8ca3979f46fd222db8ec63a1bdf910ea1f39b94ac4';

		//walletinfo

		//$data = getconnection($crypto);
		//$data = getestimatefee($crypto);  
		$data = getbalance($crypto, $label);
		//$data = getaddress($crypto, $label); 
		//$data = addCrypto($crypto, $label);
		//$data = get_label_crypto($crypto, $address);
		//$data = listransactionall($crypto, $label); 
		//$data = listransaction($crypto, $label);
		//$data = gettransaction_crypto($crypto, $txid);
		//$data = dumpkey($crypto, $label);
		//$data = getbalanceAll($crypto);
		dd($data);    
		$datamsg = response()->json( 
			 $data
		 );
		return $datamsg->content();
	}

	#################State #########################
	public function state(){
		$state = State::all();       
		$datamsg = response()->json( 
			 $state
		 );
		return $datamsg->content();
	}
		
	#################App version#########################
	public function appversion(){
		$appver = Appver::where('id',1)->first();
		$datamsg = response()->json([
			'version' => $appver->version,
			'iosversion' => $appver->ios_version,
			'iosversion2' => $appver->ios_version2
		]);
		return $datamsg->content();
	}
	
	#################SecretPin #########################
	public function send_secretpin(Request $request){
		$user = User::where('id',$request->uid)->where('secretpin',$request->secretpin)->first();	
		if($user)
		{ 
			$tokenORI = apiToken($user->id);
			if($request->tokenAPI==$tokenORI){
				$msg = array("mesej"=>"jaya");
				$datamsg = response()->json([
					'data' => $msg
				]);
				return $datamsg->content();
			}
			else{
				$msg = array("mesej"=>"No Access");
				$datamsg = response()->json([
					'data' => $msg
				]);
				return $datamsg->content();
			}	
		}
		else{
			$msg = array("mesej"=>"Sorry, wrong secret pin.");
			$datamsg = response()->json([
				'data' => $msg
			]);
			return $datamsg->content();
		}
	}
	
	
	#################Login #########################
	public function login(Request $request){
		$userData = '';
		$user = User::where('username',$request->username)->orWhere('email',$request->username)->first();
		if($user){
			if(!Hash::check($request->password, $user->password)){
				$msg = array("mesej"=>"Wrong Password.");
				$datamsg = response()->json([
					'data' => $msg
				]);
				return $datamsg->content();
			}
			elseif($user->status=='block'){
				$msg = array("mesej"=>"Your account was blocked. Please contact with administrator.");
				$datamsg = response()->json([
					'data' => $msg
				]);
				return $datamsg->content();
			}
			elseif($user->email_verify=='0'){
				$msg = array("mesej"=>"Please verify your email.");
				$datamsg = response()->json([
					'data' => $msg
				]);
				return $datamsg->content();
			}
			else{
				$tokenAPI = apiToken($user->id);
				$msg = array(
					"id"=>$user->id,
					"label"=>$user->label,
					"username"=>$user->username,
					"tokenAPI"=>$tokenAPI,
					"mesej"=>"jaya"
				);
				$datamsg = response()->json([
					'data' => $msg
				]);
				return $datamsg->content();
			} 
		}
		else{
			$msg = array("mesej"=>"User does not exist.");
			$datamsg = response()->json([
				'data' => $msg
			]);
			return $datamsg->content();
		}

	} 

	#################User registration#########################	 
	public function signup(Request $request) {
		$fullname = $request->fullname;
		$username = $request->username;
		$email = $request->email;
		$password = $request->password;
		$cpassword = $request->cpassword;
		$boxagree = $request->boxagree;
		$secretpin = $request->secretpin;
			  
		$secret_pin2 = preg_match('/^[0-9]{6}$/', $secretpin);
		$pword1 = preg_match("/[a-zA-Z0-9]/", $password); 
		$pword2 = preg_match("/[^\da-zA-Z]/", $password); 
		 
		if(!isValidUsername($username)) { echo '{"data":{"mesej":"Please enter valid username."}}'; }
		elseif(strlen($username)<6) { echo '{"data":{"mesej":"Username must be more than 6 characters."}}'; }
		elseif(strlen($secretpin)!=6) { echo '{"data":{"mesej":"Secret PIN must be 6 digits."}}'; }
		elseif(!$secret_pin2) { echo '{"data":{"mesej":"Secret PIN must be digits only."}}'; }
		elseif(!isValidEmail($email)) { echo '{"data":{"mesej":"Please enter valid email address."}}'; }
		elseif(strlen($password)<8) { echo '{"data":{"mesej":"Password must be more than 8 characters."}}'; }
		elseif(!$pword1) { echo '{"data":{"mesej":"Password must have at least one symbol, one capital letter,one number, one letter."}}'; }
		elseif(!$pword2) { echo '{"data":{"mesej":"Password must have at least one symbol, one capital letter,one number, one letter."}}'; }
		elseif($password !== $cpassword) { echo '{"data":{"mesej":"Password does not match with password for confirmation."}}'; } 
		elseif($boxagree == false) { echo '{"data":{"mesej":"Please click checkbox for agree the term."}}'; } 
		else{  
			$userData = '';
			$mainCount = User::where('username',$username)->orWhere('email',$email)->count();
			$created=time();
			
			if($mainCount==0){
				$ga = new GoogleAuthenticator();	  
				$secret = $ga->createSecret();
		
				/*Inserting user values*/
				$hash = sha1($email);
				$email_msj = 'To activate your account, please click link below.<p><a href="'.settings('url').'verify/email/'.$hash.'"  style="display: inline-block; padding: 11px 30px; margin: 20px 0px 30px; font-size: 15px; color: #fff; background: #4fc3f7; border-radius: 60px; text-decoration:none;">Link Activate</a></p>';
				send_email_basic($email, 'DORADO', settings('infoemail'), 'DORADO Account Verification', $email_msj);
 
				$user = User::create([
					'name' => $fullname,
					'username' => $username,
					'secretpin' => $secretpin,
					'label' => 'usr_'.$username,
					'password' => bcrypt($password),
					'email' => $email,
					'email_hash' => $hash,
					'ip' => \Request::ip(),
					'google_auth_code' => $secret,
				]);

				$userData = User::where('email',$request->email)->first();
				$systemToken = apiToken($userData->id);

				$msg = array(
					"id"=>$userData->id, 
					"name"=>$userData->name,
					 "username"=>$userData->username, 
					 "secretpin"=>$userData->secretpin, 
					 "email"=>$userData->email,
					 "token"=>$systemToken,
					 'display_msj'=>'Registration Successfull!',
					 'mesej'=>'jaya'
				);

				$datamsg = response()->json([
					'data' => $msg
				]);
				return $datamsg->content();
			}
			else {					 
				$msg = array("mesej"=>"This username or email has already been used. Please enter another.");
				$datamsg = response()->json([
					'data' => $msg
				]);
				return $datamsg->content();
			}
		}		 
	}

	#################Change Password####################
	public function change_password(Request $request){
		$uid = $request->uid; 
		$pwordold = $request->pwordold;
		$pwordnew = $request->pwordnew;
		$pwordconfirm = $request->pwordconfirm;
		
		$user = User::where('id',$uid)->first();
		$pword1 = preg_match("/[a-zA-Z0-9]/", $request->pwordnew); 
		$pword2 = preg_match("/[^\da-zA-Z]/", $request->pwordnew);
		
			
		if($pwordnew!=$pwordconfirm){  
			$msg = array("mesej"=>"The password confirmation does not match");
			$datamsg = response()->json([
				'data' => $msg
			]);
		}
		else if(strlen($pwordnew)<8){  
			$msg = array("mesej"=>"Password must be more than 8 characters.");
			$datamsg = response()->json([
				'data' => $msg
			]);
		}
		else if(!$pword1){  
			$msg = array("mesej"=>"Password must have at least one symbol, one capital letter,one number, one letter.");
			$datamsg = response()->json([
				'data' => $msg
			]);
		}
		else if(!$pword2){  
			$msg = array("mesej"=>"Password must have at least one symbol, one capital letter,one number, one letter.");
			$datamsg = response()->json([
				'data' => $msg
			]);
		}
		else if(isset($user)){
			$tokenORI = apiToken($user->id);
			
			if($request->tokenAPI==$tokenORI){
				if(!Hash::check($pwordold, $user->password)){
					$msg = array("mesej"=>"Wrong Old Password");
					$datamsg = response()->json([
						'data' => $msg
					]);
				}
				else{
					User::whereId($uid)
						->update([
							'password' => bcrypt($pwordnew), 
						]);
					$msg = array("mesej"=>"Success, Password successfully changed.");
					$datamsg = response()->json([
						'data' => $msg
					]);
				}
			}
			else{
			 	$msg = array("mesej"=>"No Access");
				$datamsg = response()->json([ 
				'data' => $msg
				]);
			}
			
		}
		else{
			$msg = array("mesej"=>"User does not exist.");
			$datamsg = response()->json([
				'data' => $msg
			]);
		}
		return $datamsg->content();
	}

	#################Reset Password####################
	public function reset_password(Request $request){
		$email = $request->email;   
		$user = User::where('email',$email)->first();
		
		if(isset($user)){
			$hash = $user->email_hash;
			$id = $user->id;
 
			$email_msj = $user->username.'<p> We received a request to reset your DORADO password, please click link below.</p><p><a href="'.settings('url').'password/reset/'.$hash.'"  style="display: inline-block; padding: 11px 30px; margin: 20px 0px 30px; font-size: 15px; color: #fff; background: #4fc3f7; border-radius: 60px; text-decoration:none;">Reset Pasword</a></p>';
		  	send_email_basic($user->email, 'DORADO', settings('infoemail'), 'DORADO Account Reset Password', $email_msj); 
		 
			$msg = array(
				"display_msj"=>"Email with instructions to reset password was sent. Please check your inbox.",
				"mesej"=>"jaya"
			);
			$datamsg = response()->json([
				'data' => $msg
			]);
		}
		else{	
			$msg = array("mesej"=>"No such user with this email address.");
			$datamsg = response()->json([
				'data' => $msg
			]);
		}
		return $datamsg->content();
	}

	#################Resend email####################
	public function resendemail(Request $request){
		$email = $request->email;
		$username = $request->username;
		$users = User::where('username',$username)->orWhere('email',$email)->first();
		
		if($email == '' && $username==''){
			$msg = array("mesej"=>"Please fill Username or Email");
			$datamsg = response()->json([
				'data' => $msg
			]);		
		}
		else if(isset($users) && $users->email_verify=='0'){
			$hash = $users->email_hash;
			$email_msj = 'Hai {{username}} <p> To activate your account, please click link below.</p><p> <a href="{{url}}"  style="display: inline-block; padding: 11px 30px; margin: 20px 0px 30px; font-size: 15px; color: #fff; background: #4fc3f7; border-radius: 60px; text-decoration:none;">Link Activate</a></p>';
			$replace1 = str_replace("{{username}}",$username,$email_msj); 
			$replace2 = str_replace("{{url}}",settings('url').'verify/email/'.$hash , $replace1);
		 	$email = $users->email;
			send_email_basic($email, 'DORADO', settings('infoemail'), 'DORADO Account Verification', $replace2);
		 
			$msg = array(
				"mesej"=>"jaya",
				"display_msj"=>"The email has been resend to $email .Please verify the email to Activate yout account"
			);
			$datamsg = response()->json([
				'data' => $msg
			]);
		}
		elseif(isset($users) && $users->email_verify=='1'){ 
			$msg = array(
				"mesej"=>"jaya",
				"display_msj"=>"Your email has been verified. You can login now."
			);
			$datamsg = response()->json([
				'data' => $msg
			]);
		}
		else{
			$msg = array("mesej"=>"Email or Username does not exist");
			$datamsg = response()->json([
				'data' => $msg
			]);
		}
		return $datamsg->content();
	}


	#################Private Key #########################
	public function userPrivateKey($crypto,$uid,$tokenAPI){
		$userData = '';
		$user = User::where('id',$uid)->first();
		
		if($user){
			$tokenORI = apiToken($uid);
			if($tokenAPI==$tokenORI){
				$data = dumpkey($crypto, $user->label);   
				$datamsg = response()->json([
					'mesej' => "jaya",
					'info' => $data
				]);
			}
			else{ 
				$datamsg = response()->json([
					'mesej' => 'No Access'
				]);
			}
		}
		else{ 
			$datamsg = response()->json([
				'mesej' => 'User does not exist'
			]);
		}
		return $datamsg->content();		 
	} 

	 
	#################Update Power SecretPin #########################
	public function send_powerpin(Request $request){	
		
		$user = User::where('id',$request->uid)->first();
		
		if($user){  
			$tokenORI = apiToken($request->uid);
			if($request->tokenAPI==$tokenORI){
				if($request->power_pin=='true'){
					$power = '1';
				}else{
					$power = '0';
				}

				$upt = User::findorFail($user->id);
				$upt->power_pin = $power;
				$upt->save();

				$msg = array(
					"display_msj"=>'Successfully update', 
					"mesej"=>"jaya"
				); 
			   $datamsg = response()->json([
				   'data' => $msg
			   ]);
			   return $datamsg->content(); 
			}else{
				$msg = array("mesej"=>"No Access");
				$datamsg = response()->json([
					'data' => $msg
				]);
				return $datamsg->content();
			}
		}else{
			$msg = array("mesej"=>"User does not exist.");
			$datamsg = response()->json([
				'data' => $msg
			]);
			return $datamsg->content();
		}
	}

	 
	#################Update Power 2FA #########################
	public function send_powerauth(Request $request){	
		
		$user = User::where('id',$request->uid)->first();
		
		if($user){  
			$tokenORI = apiToken($request->uid);
			if($request->tokenAPI==$tokenORI){
				if($request->power_auth=='true'){
					$power = '1';
				}else{
					$power = '0';
				}

				$upt = User::findorFail($user->id);
				$upt->power_auth = $power;
				$upt->save();

				$msg = array(
					"display_msj"=>'Successfully update', 
					"mesej"=>"jaya"
				); 
			   $datamsg = response()->json([
				   'data' => $msg
			   ]);
			   return $datamsg->content(); 
			}else{
				$msg = array("mesej"=>"No Access");
				$datamsg = response()->json([
					'data' => $msg
				]);
				return $datamsg->content();
			}
		}else{
			$msg = array("mesej"=>"User does not exist.");
			$datamsg = response()->json([
				'data' => $msg
			]);
			return $datamsg->content();
		}
	}

	 
	#################Update Power FingerPrint #########################
	public function send_powerfp(Request $request){	
		
		$user = User::where('id',$request->uid)->first();
		
		if($user){  
			$tokenORI = apiToken($request->uid);
			if($request->tokenAPI==$tokenORI){
				if($request->power_fp=='true'){
					$power = '1';
				}else{
					$power = '0';
				}

				$upt = User::findorFail($user->id);
				$upt->power_fp = $power;
				$upt->save();

				$msg = array(
					"display_msj"=>'Successfully update', 
					"mesej"=>"jaya"
				); 
			   $datamsg = response()->json([
				   'data' => $msg
			   ]);
			   return $datamsg->content(); 
			}else{
				$msg = array("mesej"=>"No Access");
				$datamsg = response()->json([
					'data' => $msg
				]);
				return $datamsg->content();
			}
		}else{
			$msg = array("mesej"=>"User does not exist.");
			$datamsg = response()->json([
				'data' => $msg
			]);
			return $datamsg->content();
		}
	}

	 
	#################Update Secretpin #########################
	public function edit_secretpin(Request $request){	
		
		$user = User::where('id',$request->uid)->first();
		
		if($user){  
			$tokenORI = apiToken($request->uid);
			if($request->tokenAPI==$tokenORI){ 
				$secret_pin2 = preg_match('/^[0-9]{6}$/', $request->secretpin);

				if(strlen($request->secretpin)!=6) { 
					$msg = array( 
						"mesej"=>"Secret PIN must be 6 digits."
					); 
					$datamsg = response()->json([
						'data' => $msg
					]);  
				}else if(!$secret_pin2) {
					$msg = array( 
						"mesej"=>"Secret PIN must be digits only."
					); 
					$datamsg = response()->json([
						'data' => $msg
					]); 
				}else{ 
					$upt = User::findorFail($user->id);
					$upt->secretpin = $request->secretpin;
					$upt->save();

					$msg = array(
						"display_msj"=>'Successfully update', 
						"mesej"=>"jaya"
					); 
					$datamsg = response()->json([
						'data' => $msg
					]);
				}
			   return $datamsg->content(); 
			}else{
				$msg = array("mesej"=>"No Access");
				$datamsg = response()->json([
					'data' => $msg
				]);
				return $datamsg->content();
			}
		}else{
			$msg = array("mesej"=>"User does not exist.");
			$datamsg = response()->json([
				'data' => $msg
			]);
			return $datamsg->content();
		}
	}


	#################User Info #########################
	public function userInfo(Request $request){
		$userData = '';
		$user = User::where('id',$request->uid)->first();
		
		if($user){  
			$tokenORI = apiToken($request->uid);
			if($request->tokenAPI==$tokenORI){
				$city = Currency::where('id',$user->currency)->first();
			 	$msg = array(
			 		"id"=>$user->id,
			 		"name"=>$user->name,
			 		"email"=>$user->email,
			 		"username"=>$user->username,
			 		"secretpin"=>$user->secretpin,
			 		"google_auth_code"=>$user->google_auth_code,
			 		"power_pin"=>$user->power_pin,
			 		"power_auth"=>$user->power_auth,
			 		"power_fp"=>$user->power_fp,
			 		"mesej"=>"jaya"
			 	); 
				$datamsg = response()->json([
					'data' => $msg
				]);
				return $datamsg->content(); 
			}
			else{
				$msg = array("mesej"=>"No Access");
				$datamsg = response()->json([
					'data' => $msg
				]);
				return $datamsg->content();
			}	
		}
		else{
			$msg = array("mesej"=>"User does not exist.");
			$datamsg = response()->json([
				'data' => $msg
			]);
			return $datamsg->content();
		}
	}  
		 
	#################Dashboard#########################
	public function dashboard($userid,$tokenAPI){
		 
		$jumMYR = 0; $bilCrypto = 0;
		$user = User::where('id',$userid)->first(); 
		if($user){
			$tokenORI = apiToken($userid);
			if($tokenAPI==$tokenORI){
				$priceapi = PriceCrypto::where('appear','1')->get();
				$currency = Currency::where('id',$user->currency)->first();
				 
				foreach($priceapi as $row){
					$wallet = WalletAddress::where('uid',$user->id)->where('crypto',$row['crypto'])->first();
					if($wallet){  
						$json_string = settings('url_gecko').'simple/price?ids='.$row["id_gecko"].'&vs_currencies='.strtolower($currency->code);
						$jsondata = file_get_contents($json_string);
						$obj = json_decode($jsondata, TRUE); 
					
						$price = $obj[$row["id_gecko"]][strtolower($currency->code)];
								
						$jumCrypto = str_replace("\n","",getbalance($row['crypto'],$user->label)/100000000); 
					
						if($jumCrypto<=0){ $totalCrypto = 0; }else{ $totalCrypto = $jumCrypto; } 	
						
						$myrCrypto = round($totalCrypto * $price,'2');  
						$addressCrypto = getaddress($row['crypto'], $user->label);  

						$feesCrypto = number_format(getestimatefee($row['crypto']) + settings('commission_withdraw')/$price, 8, '.', ''); 

						$results[] = array('price' => $price, 'imgCrypto' => $row['url_img'], 'nameCrypto' => $row['name'], 'crypto' => $row['crypto'], 'balance' => $totalCrypto, 'myrBalance' => $myrCrypto, 'addressCrypto' => $addressCrypto, 'feesCrypto' => $feesCrypto);	
						$jumMYR = $jumMYR + $myrCrypto;
						$bilCrypto++;
					}
					$totalMYR = number_format($jumMYR,'2'); 
				} 
			 
				$datamsg = response()->json([
					'totalMYR' => $totalMYR,
					'bilCrypto' => $bilCrypto,
					'currency' => $currency->code,
					'info' => $results,
					'uid' => $user->id,
					'email' => $user->email,
					'username' => $user->username,
					'fullname' => $user->name,
					'label' => $user->label,
					"power_pin"=>$user->power_pin,
					"power_auth"=>$user->power_auth,
					"power_fp"=>$user->power_fp,
					'mesej' => 'jaya',
				]);
				
			}
			else{
				$datamsg = response()->json([ 
				'mesej' => 'No Access',
				]);
			}
		}
		else{
			$datamsg = response()->json([ 
			'mesej' => 'User does not exist',
			]);
		}
		return $datamsg->content();
	}
		 
	#################Dashboard#########################
	public function dash_view($crypto,$userid,$tokenAPI){
		 
		$jumMYR = 0; $bilCrypto = 0;
		$user = User::where('id',$userid)->first(); 
		if($user){
			$tokenORI = apiToken($userid);
			if($tokenAPI==$tokenORI){
				$priceapi = PriceCrypto::where('crypto',$crypto)->first();
				$currency = Currency::where('id',$user->currency)->first();
				   
						$json_string = settings('url_gecko').'simple/price?ids='.$priceapi->id_gecko.'&vs_currencies='.strtolower($currency->code);
						$jsondata = file_get_contents($json_string);
						$obj = json_decode($jsondata, TRUE); 
					
						$price = $obj[$priceapi->id_gecko][strtolower($currency->code)];
								
						$jumCrypto = str_replace("\n","",getbalance($priceapi->crypto,$user->label)/100000000); 
					
						if($jumCrypto<=0){ $totalCrypto = 0; }else{ $totalCrypto = $jumCrypto; } 	
						
						$myrCrypto = round($totalCrypto * $price,'2');  
						$addressCrypto = getaddress($priceapi->crypto, $user->label);   
						$feesCrypto = number_format(getestimatefee($priceapi->crypto) + settings('commission_withdraw')/$price, 8, '.', ''); 
			 
				$datamsg = response()->json([  
					'currency' => $currency->code,
					'price' => $priceapi->price,
					'address' => $addressCrypto,
					'imgCrypto' => $priceapi->url_img,
					'nameCrypto' => $priceapi->name,
					'feesCrypto' => $feesCrypto,
					'balance' => $totalCrypto,
					'myrBalance' => $myrCrypto,
					'uid' => $user->id,
					'email' => $user->email,
					'username' => $user->username,
					'fullname' => $user->name,
					'label' => $user->label,
					"power_pin"=>$user->power_pin,
					"power_auth"=>$user->power_auth,
					"power_fp"=>$user->power_fp,
					'mesej' => 'jaya',
				]);
				
			}
			else{
				$datamsg = response()->json([ 
				'mesej' => 'No Access',
				]);
			}
		}
		else{
			$datamsg = response()->json([ 
			'mesej' => 'User does not exist',
			]);
		}
		return $datamsg->content();
	}
		 
	#################Dashboard Older#########################
	public function dashboardOLD($userid,$tokenAPI){
		  
		$user = User::where('id',$userid)->first(); 
		if($user){
			$tokenORI = apiToken($userid);
			if($tokenAPI==$tokenORI){ 
				$priceBTC = PriceCrypto::where('crypto','BTC')->first();
				$priceBCH = PriceCrypto::where('crypto','BCH')->first();
				$priceDOGE = PriceCrypto::where('crypto','DOGE')->first();
		
				$jumBTC = str_replace("\n","",getbalance('BTC',$user->label)/100000000);
				$jumBCH = str_replace("\n","",getbalance('BCH',$user->label)/100000000);
				$jumDOGE = str_replace("\n","",getbalance('DOGE',$user->label)/100000000);
			  
				if($jumBTC<=0){ $totalBTC = 0; }else{ $totalBTC = $jumBTC; }
				if($jumBCH<=0){ $totalBCH = 0; }else{ $totalBCH = $jumBCH; }
				if($jumDOGE<=0){ $totalDOGE = 0; }else{ $totalDOGE = $jumDOGE; }		
				 
				$myrBTC = round($totalBTC * $priceBTC->price,'2');
				$myrBCH = round($totalBCH * $priceBCH->price,'2');
				$myrDOGE = round($totalDOGE * $priceDOGE->price,'2'); 
				$totalMYR = number_format($myrBTC + $myrBCH + $myrDOGE,'2');
 
				$addressBTC = getaddress('BTC', $user->label); 
				$addressBCH = getaddress('BCH', $user->label);
				$addressDOGE = getaddress('DOGE', $user->label);

				$feesBTC = number_format(getestimatefee('BTC') + settings('commission_withdraw')/$priceBTC->price, 8, '.', '');
				$feesBCH = number_format(getestimatefee('BCH') + settings('commission_withdraw')/$priceBCH->price, 8, '.', '');
				$feesDOGE = number_format(getestimatefee('DOGE') + settings('commission_withdraw')/$priceDOGE->price, 8, '.', '');
				
				$datamsg = response()->json([
					'totalMYR' => $totalMYR,
					'priceBTC' => number_format($priceBTC->price,'2'),
					'priceDOGE' => number_format($priceDOGE->price,'2'),
					'priceBCH' => number_format($priceBCH->price,'2'),
					'totalBTC' => number_format($totalBTC,'8'),
					'totalBCH' => number_format($totalBCH,'8'),
					'totalDOGE' => number_format($totalDOGE,'8'),
					'myrBTC' => number_format($myrBTC,'2'),
					'myrBCH' => number_format($myrBCH,'2'),
					'myrDOGE' => number_format($myrDOGE,'2'),
					'addressBTC' => $addressBTC,
					'addressBCH' => $addressBCH,
					'addressDOGE' => $addressDOGE,
					'feesBTC' => $feesBTC,
					'feesBCH' => $feesBCH,
					'feesDOGE' => $feesDOGE,
					'uid' => $user->id,
					'email' => $user->email,
					'username' => $user->username,
					'fullname' => $user->name,
					'label' => $user->label,
					"power_pin"=>$user->power_pin,
					"power_auth"=>$user->power_auth,
					"power_fp"=>$user->power_fp,
					'mesej' => 'jaya',
				]);
				
			}
			else{
				$datamsg = response()->json([ 
				'mesej' => 'No Access',
				]);
			}
		}
		else{
			$datamsg = response()->json([ 
			'mesej' => 'User does not exist',
			]);
		}
		return $datamsg->content();
	}
	
	
	#################Update Currency #########################
	public function update_currency(Request $request){  
		$currency = Currency::all();
		$user = User::where('id',$request->uid)->first();
        
        if($user){
			$tokenORI = apiToken($user->id);		  
			if($request->tokenAPI==$tokenORI){
				if($currency){ 
					$upt = User::findorFail($user->id);
					$upt->currency = $request->currency;
					$upt->save();
					
					$datamsg = response()->json([ 
						'mesej' => 'jaya',
						'info' => $currency,
						'currency' => $upt->currency,
						'display_msj' => 'Successfully update!',
					]);   
				}else{
					$datamsg = response()->json([ 
						'mesej' => 'Currency failed',
						'info' => null,
						'currency' => $user->currency,
						]);
				}
			}else{
				$datamsg = response()->json([ 
				'mesej' => 'No Access',
				'info' => null,
				'currency' => $user->currency,
				]);	
			}
        }else{
            $datamsg = response()->json([ 
				'mesej' => 'User dooes not exist',
				'info' => null,
				'currency' => '',
				]);
		}
		
		return $datamsg->content();
	}
	
	
	#################Currency #########################
	public function getcurrency($uid,$tokenAPI){  
		$currency = Currency::all();
		$user = User::where('id',$uid)->first();
        
        if($user){
			$tokenORI = apiToken($user->id);		  
			if($tokenAPI==$tokenORI){
				if($currency){  
					$datamsg = response()->json([ 
						'mesej' => 'jaya',
						'info' => $currency,
						'currency' => $user->currency,
					]);   
				}else{
					$datamsg = response()->json([ 
						'mesej' => 'Currency failed',
						'info' => null,
						'currency' => $user->currency,
						]);
				}
			}else{
				$datamsg = response()->json([ 
				'mesej' => 'No Access',
				'info' => null,
				'currency' => $user->currency,
				]);	
			}
        }else{
            $datamsg = response()->json([ 
				'mesej' => 'User dooes not exist',
				'info' => null,
				'currency' => '',
				]);
		}
		
		return $datamsg->content();
	}
	
	
	#################Crypto #########################
	public function getcrypto($uid,$tokenAPI){  
		 
		$user = User::where('id',$uid)->first();
        
        if($user){
			$tokenORI = apiToken($user->id);		  
			if($tokenAPI==$tokenORI){
				$priceapi = PriceCrypto::where('appear','1')->get();
				 
				foreach($priceapi as $row){
					$wallet = WalletAddress::where('uid',$user->id)->where('crypto',$row['crypto'])->first();
					if(!$wallet){ 
					$results[] = array('crypto' =>$row['crypto'],'name' => strtoupper($row['name']));	
					}
				}
				$json2 = json_encode($results);
				$json = json_decode($json2);
				   
					$datamsg = response()->json([ 
						'mesej' => 'jaya',
						'info' => $json,
					]);   
			}else{
				$datamsg = response()->json([ 
				'mesej' => 'No Access',
				'info' => null,
				]);	
			}
        }else{
            $datamsg = response()->json([ 
				'mesej' => 'User dooes not exist',
				'info' => null,
				]);
		}
		
		return $datamsg->content();
	}

	 
	#################Add Crypto #########################
	public function create_asset(Request $request){	
		
		$user = User::where('id',$request->uid)->first();
		
		if($user){  
			$tokenORI = apiToken($request->uid);
			if($request->tokenAPI==$tokenORI){
				$wallet = WalletAddress::where('label',$user->label)->where('crypto',$request->crypto)->first();
				if(!$wallet){
				$crypto = $request->crypto; 
				$address = addCrypto($crypto, $user->label);
			
				$wallAddress = new WalletAddress;
				$wallAddress->uid = $user->id;
				$wallAddress->label = $user->label;
				$wallAddress->address = $address; 
				$wallAddress->balance = '0.00000000';
				$wallAddress->crypto = $crypto;
				$wallAddress->save();
				
				getbalance($crypto, $user->label);
				getaddress($crypto, $user->label);
				}

				$msg = array(
					"display_msj"=>'Successfully Create', 
					"mesej"=>"jaya"
				); 
			   $datamsg = response()->json([
				   'data' => $msg
			   ]);
			   return $datamsg->content(); 
			}else{
				$msg = array("mesej"=>"No Access");
				$datamsg = response()->json([
					'data' => $msg
				]);
				return $datamsg->content();
			}
		}else{
			$msg = array("mesej"=>"User does not exist.");
			$datamsg = response()->json([
				'data' => $msg
			]);
			return $datamsg->content();
		}
	}
	
	
	#################Remarks Transaction #########################
	public function remark_trans($txid,$crypto,$uid,$tokenAPI){  
		$withdraw = Withdrawal::where('txid',$txid)->where('crypto',$crypto)->first();
		$user = User::where('id',$uid)->first();
        
        if($user){
			$tokenORI = apiToken($user->id);		  
			if($tokenAPI==$tokenORI){
				if($withdraw){ 
					$datamsg = response()->json([ 
						'mesej' => 'jaya',
						'remarks' => $withdraw->remarks,
					]);   
				}else{
					$datamsg = response()->json([ 
						'mesej' => 'Withdraw info does not exist',
						'remarks' => '',
						]);
				}
			}else{
				$datamsg = response()->json([ 
				'mesej' => 'No Access',
				'remarks' => '',
				]);	
			}
        }else{
            $datamsg = response()->json([ 
				'mesej' => 'User dooes not exist',
				'remarks' => '',
				]);
		}
		
		return $datamsg->content();
	}
	
	
	#################Transaction #########################
	public function transaction($crypto,$usr_crypto,$tokenAPI){ 
		$trans = listransaction($crypto,$usr_crypto);
		$user = User::where('label',$usr_crypto)->first();
        
        if($user){
		$tokenORI = apiToken($user->id);		  
		if($tokenAPI==$tokenORI){
			$datamsg = response()->json([ 
				'mesej' => 'jaya',
				'info' => $trans,
			]);
		}
		else{
			$datamsg = response()->json([ 
			'mesej' => 'No Access',
			]);	
		}
            
        }else{
            $datamsg = response()->json([ 
				'mesej' => 'User dooes not exist',
				]);
        }
        
		return $datamsg->content();
	}
	
	
	#################Max Crypto #########################
	public function maxCrypto($crypto,$uid){
		$priceApi = PriceCrypto::where('crypto',$crypto)->first();		
		$user = User::where('id',$uid)->first(); 
		$currency = Currency::where('id',$user->currency)->first();
				   
		$json_string = settings('url_gecko').'simple/price?ids='.$priceApi->id_gecko.'&vs_currencies='.strtolower($currency->code);
		$jsondata = file_get_contents($json_string);
		$obj = json_decode($jsondata, TRUE); 
		$price = $obj[$priceApi->id_gecko][strtolower($currency->code)];
		
			$comm_fee = number_format(settings('commission_withdraw')/$price, 8, '.', '');
			$net_fee = getestimatefee($crypto);
		
		if($user){
			$userbalance = number_format(getbalance($crypto, $user->label)/100000000, 8, '.', '');
			$fee = number_format($comm_fee+$net_fee, 8, '.', '');
			$maxDraw =  number_format($userbalance - $fee, 8, '.', ''); 

			if($maxDraw<=0){ $maxWithdraw =0; }else{ $maxWithdraw =$maxDraw; }

			$priceWithdraw = $maxWithdraw*$price;
			$datamsg = response()->json([
				"totalMyr"=>number_format($priceWithdraw,'2'),
				"totalCrypto"=>$maxWithdraw
			]);
		}
		else{
			$datamsg = response()->json([ 
				'mesej' => 'User dooes not exist',
			]);
		}
		return $datamsg->content();	
	}
	
	//	
	#################Send Crypto #########################
	public function sendCrypto(Request $request){ 
	 
		//dd(getbalance('BTC', 'usr_doradofees'));
		$crypto = $request->crypto;
		$amount = $request->amountcrypto;
		$label = $request->sendfrom; 
		$recipient = $request->sendto;
		$remarks = $request->remarks;
		$secretpin = $request->secretpin;
		//$admin_label = 'usr_admin';
	 
		$useruid = User::where('label',$label)->first();   
		$priceApi = PriceCrypto::where('crypto',$crypto)->first(); 	 
		$currency = Currency::where('id',$useruid->currency)->first();
				   
		$json_string = settings('url_gecko').'simple/price?ids='.$priceApi->id_gecko.'&vs_currencies='.strtolower($currency->code);
		$jsondata = file_get_contents($json_string);
		$obj = json_decode($jsondata, TRUE); 
		$price = $obj[$priceApi->id_gecko][strtolower($currency->code)];
		 
		$amountset = 0.01;
		$minwithdraw = number_format($amountset/$price, 8, '.', '');
		  
		if($amount<=$minwithdraw){
			$m = 'Minimum withdraw must more than '.$minwithdraw;
		 $msg = array("mesej"=>$m);
			$datamsg = response()->json([
				'data' => $msg
			]);
			
		 return $datamsg->content();
			
		}else if(!isset($useruid)){
		 	$msg = array("mesej"=>"Id Sender does not exist!");
			$datamsg = response()->json([
				'data' => $msg
			]);
		 	return $datamsg->content();
		 }
		 else if(isset($useruid) && $useruid->secretpin!=$secretpin){
		 	$msg = array("mesej"=>"Wrong Secret Pin!");
			$datamsg = response()->json([
				'data' => $msg
			]);
		 	return $datamsg->content();	 
		 }
		 else{
			$wuserF = WalletAddress::where('uid',$useruid->id)->where('crypto',$crypto)->first();
			if(!isset($wuserF)){
				$m = $crypto.' for user does not exist!';
			 	$msg = array("mesej"=>$m);
				$datamsg = response()->json([
					'data' => $msg
				]);
			 	return $datamsg->content();
			 }
		 }			 
		 
		$wuserF = getaddress($crypto,$label);
		  
			$comm_fee = number_format(settings('commission_withdraw')/$price, 8, '.', '');
			$net_fee = getestimatefee($crypto);
	 
		$fee = number_format($comm_fee+$net_fee, 8, '.', '');
		$userbalance = number_format(getbalance($crypto, $label)/100000000, 8, '.', '');
		$getuserlabel = get_label_crypto($crypto, $recipient); 
		$totalfunds = number_format($amount + $fee, 8, '.', '');
		$after_bal =  number_format($userbalance - $totalfunds, 8, '.', ''); 
		
		/*
		$checkwalladdr=  $recipient[0];
		 
		if($checkwalladdr == '3' || $checkwalladdr == '1' || $checkwalladdr == '0' || $checkwalladdr == 'd' || $checkwalladdr == 'D' || $checkwalladdr == 'q' || $checkwalladdr == 'X' || $checkwalladdr == 'L'){	 
		$dashwall = 1;
		}else{
		$dashwall =0;
		}
		*/
		
		$tokenORI = apiToken($useruid->id); 
		if($request->tokenAPI==$tokenORI){
			
			if($userbalance < $totalfunds) {   
				$msg = array("mesej"=>"Sorry, you do not have enough funds for withdrawal!");
				$datamsg = response()->json([
					'data' => $msg
				]);
				return $datamsg->content();
			}
			else{
				$crypto_txid = sendtoaddressRAW($crypto, $label, $recipient, $amount, 'withdraw', $comm_fee);  
				//move_crypto_comment($crypto, $label, $admin_label, $comm_fee, 'fees');
				$myr_amount = $amount*$price;
					 
				if($crypto_txid==''){ //failed withdraw
					$withdraw = new Withdrawal;
					$withdraw->uid = $useruid->id;
					$withdraw->status = 'failed';
					$withdraw->amount= $amount; 
					$withdraw->before_bal = $userbalance;
					$withdraw->after_bal = $after_bal; 
					$withdraw->recipient = $recipient;
					$withdraw->netfee = $net_fee; 
					$withdraw->walletfee = $comm_fee; 
					$withdraw->txid = $crypto_txid;
					$withdraw->crypto = $crypto;
					$withdraw->remarks = $remarks;
					$withdraw->rate = round($price,2);
					$withdraw->myr_amount = round($myr_amount,2);
					$withdraw->type = 'external';
					$withdraw->save();
					  
					$msg = array("mesej"=>"Failed widthdraw!");
					$datamsg = response()->json([
						'data' => $msg
					]);
					return $datamsg->content();
				}
				else{ //success withdraw
					$withdraw = new Withdrawal;
					$withdraw->uid = $useruid->id;
					$withdraw->status = 'success';
					$withdraw->amount= $amount; 
					$withdraw->before_bal = $userbalance;
					$withdraw->after_bal = $after_bal;
					$withdraw->recipient = $recipient;
					$withdraw->netfee = $net_fee; 
					$withdraw->walletfee = $comm_fee; 
					$withdraw->txid = $crypto_txid;
					$withdraw->crypto = $crypto;
					$withdraw->remarks = $remarks;
					$withdraw->rate = round($price,2);
					$withdraw->myr_amount = round($myr_amount,2);
					$withdraw->type = 'external';
					$withdraw->save();
						 
					$msg = array(
						"mesej"=>"jaya",
						"display_msj"=>'Successfully withdraw. Amount '.$amount .' '.$crypto .' was sent to '.$recipient
					);
					$datamsg = response()->json([
						'data' => $msg
					]);
					return $datamsg->content(); 
				}
					
			/*	}else{  // internal wallet
					if($dashwall == 1){
					$userR = User::where('label',$getuserlabel)->first();
					
					$crypto_txid = move_crypto_comment($crypto, $label, $getuserlabel, $amount, 'withdraw'); 
				  
					//success withdraw
			 
					$withdraw = new Withdrawal;
					$withdraw->uid = $useruid->id;
					$withdraw->status = 'success';
					$withdraw->amount= $amount; 
					$withdraw->before_bal = $userbalance;
					$withdraw->after_bal = round(getbalance($crypto, $label),8);
					$withdraw->recipient_id = $userR->id;
					$withdraw->recipient = $recipient;
					$withdraw->netfee = 0; 
					$withdraw->walletfee = 0; 
					$withdraw->txid = $crypto_txid;
					$withdraw->crypto = $crypto;
					$withdraw->remarks = $remarks;
					$withdraw->type = 'internal';
					$withdraw->save();
						 
					$msg = array("mesej"=>"jaya","display_msj"=>'Successfully widthdraw. Amount '.$amount .' '.$crypto .' was sent to '.$recipient.' TXID : '. $crypto_txid);
					$datamsg = response()->json([
						'data' => $msg
					]);
					
					return $datamsg->content(); 
					
					}else{
						 
					$msg = array("mesej"=>"Please enter valid address!");
					$datamsg = response()->json([
						'data' => $msg
					]);
					
					return $datamsg->content();
					
					}
					
				}
				*/
				
			}// end send /move crypto	
		}
		else{
			$msg = array("mesej"=>"No Access");
			$datamsg = response()->json([
				'data' => $msg
			]);	
			return $datamsg->content();
		}	 
	}
	 
	#################Convert#########################
	public function convert(Request $request){	
		$priceApi = PriceCrypto::where('crypto',$request->crypto)->first();		
		$user = User::where('id',$request->uid)->first(); 
		$currency = Currency::where('id',$user->currency)->first();
				   
		$json_string = settings('url_gecko').'simple/price?ids='.$priceApi->id_gecko.'&vs_currencies='.strtolower($currency->code);
		$jsondata = file_get_contents($json_string);
		$obj = json_decode($jsondata, TRUE); 
		$priceCrypto = $obj[$priceApi->id_gecko][strtolower($currency->code)];
		  
		if($request->type=='crypto'){ 
			$jum = round($request->nilai*$priceCrypto,2);  
		}
		else{
			$jum = round($request->nilai/$priceCrypto,8);
		}
		
		$msg = array("mesej"=>"jaya","display_msj"=>$jum);
		$datamsg = response()->json([ 
			'data' => $msg
		]); 
		return $datamsg->content();	
	}
}  // tag
