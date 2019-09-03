<?php
 
use App\Setting;
use App\PriceCrypto;
use App\WalletAddress;
use App\User;
 
 
  function settings($value){
	  $setting = Setting::first();
	  return $setting->$value;
  }  

////////////////API FUNCTION//////////////////

   function apiToken($session_uid)
    {
        $key=md5('Dorado2019'.$session_uid);
        return hash('sha256', $key);
    }

    function isValidUsername($str) {
        return preg_match('/^[a-zA-Z0-9-_]+$/',$str);
    }

    function isValidEmail($str) {
        return filter_var($str, FILTER_VALIDATE_EMAIL);
    }
 

/////////////////////////////////////////////////////////////////////
///  SET URL CRYPTO                     ///////////////////////////////////////
////////////////////////////////////////////////////////////////////

function getinfo($crypto) {
 
    $URL_INFO = PriceCrypto::where('crypto', $crypto)->first();
    $URL_IP = $URL_INFO->ip_getinfo;
    return $URL_IP;
} 

/////////////////////////////////////////////////////////////////////
///  ADD CRYPTO                  ///////////////////////////////////////
////////////////////////////////////////////////////////////////////


function addCrypto($crypto, $label) {

    if ($crypto == 'BTC' || $crypto == 'BCH' || $crypto == 'DOGE') {
    //bch 
        $post_ad = [
            'id' => 2,
            'label' => $label
        ];

        $ch_ad = curl_init(getinfo($crypto));
        curl_setopt($ch_ad, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch_ad, CURLOPT_POSTFIELDS, $post_ad);
        $add_crypto = curl_exec($ch_ad);
		
		$wallet_address2 = str_replace("\n", '', $add_crypto);
	$wallet_address3 = str_replace("bitcoincash:", '', $wallet_address2);
	$wallet_address = str_replace("dogecoin:", '', $wallet_address3);
		
    } else {
        $wallet_address = null;
    }
	
    return $wallet_address;
}

/////////////////////////////////////////////////////////////////////
/// DUMP PRIVATE KEY             ///////////////////////////////////////
////////////////////////////////////////////////////////////////////

function dumpkey($label,$crypto)
{
	$post = [
    'id' => 22,
    'label' => $label
    ];

    $ch = curl_init(getinfo($crypto));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    // execute!
    $balance = curl_exec($ch);
   
    $data = json_decode($balance);
	
	return $data;
	
}

/////////////////////////////////////////////////////////////////////
///  GET ADDDRESS             ///////////////////////////////////////
////////////////////////////////////////////////////////////////////

function getaddress($crypto, $label) { 
  
    $userid = WalletAddress::where('label', $label)->where('crypto',$crypto)->first();
	
	if($userid){
    //GET ADDRESS 
    $post = [
        'id' => 3,
        'label' => $label
    ];

    $ch = curl_init(getinfo($crypto));
	 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    $Waddress = curl_exec($ch);
 
    curl_close($ch);
	$wallet_address2 = str_replace("\n", '', $Waddress);
	$wallet_address3 = str_replace("bitcoincash:", '', $wallet_address2);
	$wallet_address = str_replace("dogecoin:", '', $wallet_address3);
 
    //UPDATE ADDRESS INTO TABLE WALLETADDRESS
	 
		WalletAddress::where('uid',$userid->uid)->where('crypto',$crypto)
        ->update([
            'address' => $wallet_address,
		]);	
   
	}else{
		$wallet_address = null;
	}

    return $wallet_address;

}



/////////////////////////////////////////////////////////////////////
///  TRANSACTION ALL CRYPTO                  ///////////////////////////////////////
////////////////////////////////////////////////////////////////////


function getransactionAll($crypto) {

    if ($crypto == 'BTC' || $crypto == 'BCH' || $crypto == 'DOGE') {
 
        //GET all transaction
    $post = [
        'id' => 8
    ];

    $ch = curl_init(getinfo($crypto));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    $bit_trans = curl_exec($ch);
    $transaction = json_decode($bit_trans);  
 
    } else {
        $transaction = null;
    }
	
       return $transaction;
	 
}


/////////////////////////////////////////////////////////////////////
///  GET TRANSACTION             ///////////////////////////////////////
////////////////////////////////////////////////////////////////////

function gettransaction_crypto($crypto, $txid) { 
  
        $post = [
            'id' => 19,
            'txid' => $txid
        ];

        $ch = curl_init(getinfo($crypto));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

         $data = curl_exec($ch);
		$transaction = json_decode($data);
        curl_close($ch);
        return $transaction;

}


/////////////////////////////////////////////////////////////////////
///  TRANSACTION CRYPTO            ///////////////////////////////////////
////////////////////////////////////////////////////////////////////

function getransaction($crypto,$label) {
    
	if ($crypto == 'BTC' || $crypto == 'BCH' || $crypto == 'DOGE') {
    //GET user transaction
    $post = [
        'id' => 11,
        'label' => $label
    ];

    $ch = curl_init(getinfo($crypto));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    $bit_trans = curl_exec($ch); 
    $info_transaction = json_decode($bit_trans);  
 
	} else {
        $info_transaction = null;
    }
	
       return $info_transaction;

}


/////////////////////////////////////////////////////////////////////
/// GET BALANCE             ///////////////////////////////////////
////////////////////////////////////////////////////////////////////

function getbalance($crypto, $label) {
	
	$users = User::where('label',$label)->first();
	
    if ($crypto == 'BTC' || $crypto == 'BCH' || $crypto == 'DOGE') {
		
		if($users){
        //GET BALANCE
        $post = [
            'id' => 4,
            'label' => $label
        ];

        $ch = curl_init(getinfo($crypto));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
 
        $balance = curl_exec($ch);
		$wallet_balance = str_replace("\n", '', $balance);

        curl_close($ch);
		
    //UPDATE ADDRESS INTO TABLE WALLETADDRESS
		WalletAddress::where('uid',$users->id)->where('crypto',$crypto)
        ->update([ 
            'balance' => $wallet_balance,
		]);	
		
		}else{
			$wallet_balance = null;
		}
		
    } else {
        $wallet_balance = null;
    }
	
        return $wallet_balance;
	
}

/////////////////////////////////////////////////////////////////////
///  GET LABEL BY ADDRESS                  ///////////////////////////////////////
////////////////////////////////////////////////////////////////////
 
function get_label_crypto($crypto, $address) {
	
    if ($crypto == 'BTC' || $crypto == 'BCH' || $crypto == 'DOGE') {


        $post = [
            'id' => 14,
            'address' => $address
        ];


        $ch = curl_init(getinfo($crypto));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);


        $label = curl_exec($ch);

        curl_close($ch);

	$label = str_replace("\n", '', $label);
 
    }  else {
        $label = null;
    }
	
        return $label;
}

///////////////////////////////////////////////////////////////
/// EMAIL /////////////////////////////////////////////////
/////////////////////////////////////////////////////////////

function send_email_basic($to, $from_name, $from_email, $subject, $message){
   
  $headers = "From: ".$from_name." <".$from_email."> \r\n"; 
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
    
  $template1 = settings('template_email');
  $template2 = str_replace("{{title}}",settings('title'),$template1);
  $template3 = str_replace("{{logo}}",asset('asset/assets/images/logo.png'),$template2); 
  $template4 = str_replace("{{logotext}}",asset('asset/assets/images/logo-text.png'),$template3); 
  $template = str_replace("{{message}}",$message,$template4);
 
  mail($to, $subject, $template, $headers);

}


////////////////////////////////////////////////////////////////////
////////////////////////////move with comment////////////////////////
//////////////////////////////////////////////////////////////////////
function move_crypto_comment($crypto, $label, $label2, $amount, $comment) {
	
    if ($crypto == 'BTC' || $crypto == 'BCH' || $crypto == 'DOGE') {
		
        $post = [
            'id' => 20,
            'label' => $label,
            'label2' => $label2,
            'amount' => $amount,
	    'comment' => $comment
        ];


        $ch = curl_init(getinfo($crypto));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);


        $result = curl_exec($ch);
 
	 getbalance($crypto, $label);
	 getaddress($crypto, $label);
	 getbalance($crypto, $label2);
	 getaddress($crypto, $label2);

        curl_close($ch);

    } else {
        $result = null; 
    }
	
        return $result;
		
}

///////////////////////////////////////////////////////////
///////////sendfrom with command//////////////////////////
/////////////////////////////////////////////////////
function send_crypto_comment($crypto, $label, $address, $amount,$comment) {


    if ($crypto == 'BTC' || $crypto == 'BCH' || $crypto == 'DOGE') {

        $post = [
            'id' => 21,
            'label' => $label,
            'address' => $address,
            'amount' => $amount,
 	    'comment' => $comment
        ];


        $ch = curl_init(getinfo($crypto));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);


        $txid = curl_exec($ch);
		 
		getbalance($crypto, $label);
		getaddress($crypto, $label);

        curl_close($ch);
 
	 } else {
		$txid = null;
	 }

		return $txid;
		
}


 
 
