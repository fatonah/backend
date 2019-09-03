<?php
 
use App\Setting;
use App\PriceCrypto;
use App\WalletAddress;
use App\User;
 
 
///////////////////////////////////////////////////////////////
/// SETTINGS /////////////////////////////////////////////////
/////////////////////////////////////////////////////////////
function settings($value){
    $setting = Setting::first();
    return $setting->$value;
}

function send_email_verify($to, $subject, $name, $message, $hash){
  $msgData = array(
    "uname" => $name,
    "msg" => $message,
    "emailhash" => $hash,
    "supportemail" => "supports@dorado.com"
  );
  // dd($to, $msgData);
  Mail::to($to)->send(new verifyMail($msgData));
}

function send_reset_password($to, $subject, $name, $message, $hash){
  $msgData = array(
    "uname" => $name,
    "msg" => $message,
    "passhash" => $hash,
    "supportemail" => "supports@dorado.com"
  );
  // dd($to, $msgData);
  Mail::to($to)->send(new resetPassword($msgData));
}

function send_supports_email($to, $subject, $name, $message){
  $msgData = array(
    "uname" => $name,
    "msg" => $message,
    "supportemail" => "supports@dorado.com"
  );
  // dd($to, $msgData);
  Mail::to($to)->send(new supportMail($msgData));
}

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

function send_email_toadmin($to, $subject,$name,$message,$orderid,$crypto){
    $setting = Setting::first();
    $headers = "From: ".$setting->name." <".$setting->infoemail."> \r\n";
    $headers .= "Reply-To: ".$setting->title." <".$setting->infoemail."> \r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
    $template = $setting->template_sendtoadmin;
    $mm = str_replace("{{name}}",$name,$template);
    $supportemail = str_replace("{{supportemail}}",$setting->supportemail,$mm);
    $url = str_replace("{{url}}",$setting->url.'admin/instant_buy/edit/'.$crypto.'/'.$orderid, $supportemail);
    $message = str_replace("{{message}}",$message,$url);
    $result = mail($to, $subject,$message, $headers);
    return $result;
}

function send_email_touser($to, $subject,$name,$message,$orderid,$crypto){
    $setting = Setting::first();
    $headers = "From: ".$setting->name." <".$setting->infoemail."> \r\n";
    $headers .= "Reply-To: ".$setting->title." <".$setting->infoemail."> \r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
    $template = $setting->template_sendtouser;
    $mm = str_replace("{{name}}",$name,$template);
    $supportemail = str_replace("{{supportemail}}",$setting->supportemail,$mm);
    $url = str_replace("{{url}}",$setting->url.'admin/instant_buy/edit/'.$crypto.'/'.$orderid, $supportemail);
    $message = str_replace("{{message}}",$message,$url);
    $result = mail($to, $subject,$message, $headers);
    return $result;
}

function send_email_ticket($to, $subject, $name, $message,$message2){
    $setting = Setting::first();
    //$img = 'https://colony.pinkexc.com/assets/homepage/images/logo-white.png';
    $headers = "From: ".$setting->name." <".$setting->infoemail."> \r\n";
    $headers .= "Reply-To: ".$setting->title." <".$setting->infoemail."> \r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
    $template = $setting->template_ticket;
    $mm = str_replace("{{name}}",$name,$template);
    $supportemail = str_replace("{{supportemail}}",$setting->supportemail,$mm);
    $url = str_replace("{{url}}",$message2, $supportemail);
    $message = str_replace("{{message}}",$message,$url);
    mail($to, $subject, $message, $headers);
}

 

/////////////////////////////////////////////////////////////////////
///  CONN CRYPTO                     ///////////////////////////////////////
////////////////////////////////////////////////////////////////////
function getconnection($crypto){
    if ($crypto == 'BTC'){$crycode = 'bitcoin';}
    elseif($crypto == 'BCH'){$crycode = 'bitabc';}
    elseif($crypto == 'DASH'){$crycode = 'dashcoin';}
    elseif($crypto == 'DOGE'){$crycode = 'dogecoin';}
    elseif($crypto == 'LTC'){$crycode = 'bitcoin';}
    else {return "invalid crypto";}
    $conn = bitcoind()->client($crycode)->getBlockchainInfo()->get();
    return json_encode($conn, JSON_PRETTY_PRINT);
}

///////////////////////////////////////////////////////////////
/// ESTIMATE NETWORK FEE /////////////////////////////////////////////////
/////////////////////////////////////////////////////////////
function getestimatefee($crypto) {
    $numberblock = 25;
    if ($crypto == 'BTC'){
        $crycode = 'bitcoin';
        $fee = number_format(bitcoind()->client($crycode)->estimatesmartfee($numberblock)->get()['feerate'], 8, '.', '');
        return $fee;
    }
    elseif($crypto == 'BCH'){
        $crycode = 'bitabc';
        $fee = number_format(bitcoind()->client($crycode)->estimatefee()->get(), 8, '.', '');
        return $fee;
    }
    elseif($crypto == 'DASH'){
        $crycode = 'dashcoin';
        $fee = number_format(bitcoind()->client($crycode)->estimatesmartfee($numberblock)->get()['feerate'], 8, '.', '');
        return $fee;
    }
    elseif($crypto == 'DOGE'){
        $crycode = 'dogecoin';
        $fee = number_format(bitcoind()->client($crycode)->estimatesmartfee($numberblock)->get()['feerate'], 8, '.', '');
        return $fee;
    }
    elseif($crypto == 'LTC'){
        $crycode = 'bitcoin';
        $fee = number_format(bitcoind()->client($crycode)->estimatesmartfee($numberblock)->get()['feerate'], 8, '.', '');
        return $fee;
    }
    else {return "invalid crypto";}
}

function getestimatefee_myr($crypto) {
    if ($crypto == 'BTC'){
        $fee = getestimatefee($crypto);
        $data = json_decode(file_get_contents("https://api.coingecko.com/api/v3/coins/markets?vs_currency=myr&ids=bitcoin&sparkline=false"));
        $current_price = $data[0]->current_price;
        $fee_myr = bcdiv(($current_price * $fee)*100,1,0);
        return $fee_myr;
    }
    elseif($crypto == 'BCH'){
        $fee = getestimatefee($crypto);
        $data = json_decode(file_get_contents("https://api.coingecko.com/api/v3/coins/markets?vs_currency=myr&ids=bch&sparkline=false"));
        $current_price = $data[0]->current_price;
        $fee_myr = bcdiv(($current_price * $fee)*100,1,0);
        return $fee_myr;
    }
    elseif($crypto == 'DASH'){
        $fee = getestimatefee($crypto);
        $data = json_decode(file_get_contents("https://api.coingecko.com/api/v3/coins/markets?vs_currency=myr&ids=dash&sparkline=false"));
        $current_price = $data[0]->current_price;
        $fee_myr = bcdiv(($current_price * $fee)*100,1,0);
        return $fee_myr;
    }
    elseif($crypto == 'DOGE'){
        $fee = getestimatefee($crypto);
        $data = json_decode(file_get_contents("https://api.coingecko.com/api/v3/coins/markets?vs_currency=myr&ids=doge&sparkline=false"));
        $current_price = $data[0]->current_price;
        $fee_myr = bcdiv(($current_price * $fee)*100,1,0);
        return $fee_myr;
    }
    elseif($crypto == 'LTC'){
        $fee = getestimatefee($crypto);
        $data = json_decode(file_get_contents("https://api.coingecko.com/api/v3/coins/markets?vs_currency=myr&ids=ltc&sparkline=false"));
        $current_price = $data[0]->current_price;
        $fee_myr = bcdiv(($current_price * $fee)*100,1,0);
        return $fee_myr;
    }
    else {return "invalid crypto";}
}


/////////////////////////////////////////////////////////////////////
///  BALANCE                    ///////////////////////////////////////
////////////////////////////////////////////////////////////////////
function getbalance($crypto, $label) {
    if ($crypto == 'BTC'){
        $addressarr = array_keys(bitcoind()->client('bitcoin')->getaddressesbylabel($label)->get());
        $amt = null;
        foreach ($addressarr as $address) {
            $balacc = bitcoind()->client('bitcoin')->listunspent(1, 9999999, [$address])->get();
            $balance = 0;
            if(in_array('txid', $balacc)){
                $amt[] =  (int)number_format($balacc['amount']*100000000, 8, '.', '');
                foreach ($amt as $a) {$balance += $a;}
            }
            else{
                foreach ($balacc as $acc) {$amt[] = (int)number_format($acc['amount']*100000000, 8, '.', '');}
            }
        }
        $wallet_balance = array_sum($amt);
        return $wallet_balance;
    }
    elseif($crypto == 'BCH'){
        $j = 0;
        $balacc[] = bitcoind()->client('bitabc')->listunspent()->get();
        $balance = 0;
        foreach ($balacc as $acc) {
            $ac[$j] = $acc;
            foreach ($ac as $a) {
                $i = 0;
                foreach ($a as $x) {
                    $labelret[$i] = $x['label'];
                    if( $labelret[$i] == $label){
                        $amt[$i] = number_format($x['amount'], 8, '.', '');
                        $balance += $amt[$i];
                        $i++; 
                    }
                }
            }
            $j++;
        }
       // dd($balacc,$labelret, $balance);
        $wallet_balance = (int)number_format($balance*100000000, 8, '.', '');
        return $wallet_balance;
    }
    elseif($crypto == 'DASH'){
        $wallet_balance = bitcoind()->client('dashcoin')->getbalance($label)->get();
        return $wallet_balance;
    }
    elseif($crypto == 'DOGE'){
        $wallet_balance = bitcoind()->client('dogecoin')->getbalance($label)->get();
        return $wallet_balance;
    }
    elseif($crypto == 'LTC'){
        $wallet_balance = bitcoind()->client('litecoin')->getbalance($label)->get();
        return $wallet_balance;
    }
    elseif ($crypto == 'ETH') {
        // $converter = new \Bezhanov\Ethereum\Converter();
        // $data= json_decode(file_get_contents("https://api.etherscan.io/api?module=account&action=balance&address=".$from."&tag=latest&apikey=91V2W6YN95VHHFBPRT18225VYCKI8FMNXB"));
        // $value = substr(number_format($converter->fromWei($data->result, 'ether'), 18, '.', ''), 0, -10);
        // $updt = WalletAddress::where('crypto','ETH')->where('address', $from)->update([
        //     'available_balance' => $value,
        //     'ethbalupflag' => "UP"
        // ]);
        $converter = new \Bezhanov\Ethereum\Converter();      
        $user = WalletAddress::where('label',$label)->where('crypto',$crypto)->first();
        $wallet_balance = Ethereum::eth_getBalance($user->address,'latest',TRUE);     //eth down  
        //$wallet_balance = 0;  
        $float = number_format($wallet_balance, 0, '','');
        $value = round($converter->fromWei($float, 'ether'),5);
        $updt = WalletAddress::where('label', $label)->where('crypto', 'ETH')
            ->update([
                 'available_balance' => $value
            ]);
        return $value;

    } 
    elseif ($crypto == 'XLM') {
        $wallet_balance = '';
        return $wallet_balance;
    } 
    elseif ($crypto == 'XRP') {
        $uid = WalletAddress::where('label',$label)->where('crypto','XRP')->first();
        //$client = new Client('https://s.altnet.rippletest.net:51234');
        $client = new Client('http://178.128.105.75:5005'); // Error
        $wallet_balance = null;
        $response = $client->send('account_info', [
            'account' => $uid->address
        ]);
        // Set balance if successful.
        if ($response->isSuccess()) {
            $data = $response->getResult();
            $wallet_balance = $data['account_data']['Balance'];
        }

        $updt = WalletAddress::where('label', $label)->where('crypto', 'XRP')
            ->update([
                'available_balance' => $wallet_balance
            ]);
         return $wallet_balance;
    } 
    elseif ($crypto == 'LIFE') {
        $converter = new \Bezhanov\Ethereum\Converter();      
        $user = WalletAddress::where('label',$label)->where('crypto',$crypto)->first();
        $wallet_balance = Ethereum::eth_getBalance($user->address,'latest',TRUE);     //eth down
        //$wallet_balance = '0';    
        //$float = number_format($wallet_balance, 0, '','');
        $pari = new EthereumRPC('blappONE:bR4k82xIvhU7uI13E123n4ng2xIvTepiY417@bapp1.pinkexc.com', 443);    //eth down
        $erc20 = new ERC20($pari);    //eth down
        $token = $erc20->token("0xce61f5e6d1fe5a86e246f68aff956f7757282ef0");    //eth down
        $tokbal = $token->balanceOf($user->address);//"0x12E8962188B533E8FE53509B381dBfB31cc3fAA3");    //eth down
        $value = round($tokbal,8);    //eth down
        //$value = 0;

        $updt = WalletAddress::where('label', $label)->where('crypto', 'LIFE')
            ->update([
                'available_balance' => $value
            ]);
        return $value;

    } 
    else {
        $wallet_balance = null;
        return $wallet_balance;
    }
}

function getbalanceeth($from){
    $converter = new \Bezhanov\Ethereum\Converter();
    $data= json_decode(file_get_contents("https://api.etherscan.io/api?module=account&action=balance&address=".$from."&tag=latest&apikey=91V2W6YN95VHHFBPRT18225VYCKI8FMNXB"));
    $balraw = substr(number_format($converter->fromWei($data->result, 'ether'), 18, '.', ''), 0, -10);
    $balup = WalletAddress::where('crypto','ETH')->where('address', $from)->update([
        'available_balance' => $balraw,
        'ethbalupflag' => "UP"
    ]);
}

function getbalance_myr($crypto, $label) {
    if ($crypto == 'BTC'){
        $wallet_balance = getbalance($crypto,$label)/100000000;
        $data = json_decode(file_get_contents("https://api.coingecko.com/api/v3/coins/markets?vs_currency=myr&ids=bitcoin&sparkline=false"));
        $current_price = $data[0]->current_price;
        $myr_balance = bcdiv(($current_price * $wallet_balance)*100,1,0);
        return $myr_balance;
    }
   elseif($crypto == 'BCH'){
        $wallet_balance = getbalance($crypto,$label)/100000000;
        $data = json_decode(file_get_contents("https://api.coingecko.com/api/v3/coins/markets?vs_currency=myr&ids=bch&sparkline=false"));
        $current_price = $data[0]->current_price;
        $myr_balance = bcdiv(($current_price * $wallet_balance)*100,1,0);
        return $myr_balance;
    }
   elseif($crypto == 'DASH'){
        $wallet_balance = getbalance($crypto,$label)/100000000;
        $data = json_decode(file_get_contents("https://api.coingecko.com/api/v3/coins/markets?vs_currency=myr&ids=dash&sparkline=false"));
        $current_price = $data[0]->current_price;
        $myr_balance = bcdiv(($current_price * $wallet_balance)*100,1,0);
        return $myr_balance;
    }
   elseif($crypto == 'DOGE'){
        $wallet_balance = getbalance($crypto,$label)/100000000;
        $data = json_decode(file_get_contents("https://api.coingecko.com/api/v3/coins/markets?vs_currency=myr&ids=doge&sparkline=false"));
        $current_price = $data[0]->current_price;
        $myr_balance = bcdiv(($current_price * $wallet_balance)*100,1,0);
        return $myr_balance;
    }
   elseif($crypto == 'LTC'){
        $wallet_balance = getbalance($crypto,$label)/100000000;
        $data = json_decode(file_get_contents("https://api.coingecko.com/api/v3/coins/markets?vs_currency=myr&ids=ltc&sparkline=false"));
        $current_price = $data[0]->current_price;
        $myr_balance = bcdiv(($current_price * $wallet_balance)*100,1,0);
        return $myr_balance;
    }
    else {return "invalid crypto";}
}


/////////////////////////////////////////////////////////////////////
///  ADDRESS                      ///////////////////////////////////////
////////////////////////////////////////////////////////////////////
function getaddress($crypto, $label) {
    if ($crypto == 'BTC'){
        $wallet_address = array_keys(bitcoind()->client('bitcoin')->getaddressesbylabel($label)->get());
        return $wallet_address;
    }
   elseif($crypto == 'BCH') {
        $wallet_address = bitcoind()->client('bitabc')->getaddressesbyaccount($label)->get();
        return $wallet_address;
    }
   elseif($crypto == 'DASH') {
        $wallet_address = bitcoind()->client('dashcoin')->getaddressesbyaccount($label)->get();
        return $wallet_address;
    }
   elseif($crypto == 'DOGE') {
        $wallet_address = bitcoind()->client('dogecoin')->getaddressesbyaccount($label)->get();
        return $wallet_address;
    }
   elseif($crypto == 'LTC') {
        $wallet_address = bitcoind()->client('litecoin')->getaddressesbyaccount($label)->get();
        return $wallet_address;
    }
    else {return "invalid crypto";}
}

function addCrypto($crypto, $label) {
    if ($crypto == 'BTC'){
        $wallet_address = bitcoind()->client('bitcoin')->getnewaddress($label)->get();
        return $wallet_address;
    }
   elseif($crypto == 'BCH') {
        $wallet_address = bitcoind()->client('bitabc')->getnewaddress($label)->get();
        return substr($wallet_address,12);
    }
   elseif($crypto == 'DASH') {
        $wallet_address = bitcoind()->client('dashcoin')->getnewaddress($label)->get();
        return $wallet_address;
    }
   elseif($crypto == 'DOGE') {
        $wallet_address = bitcoind()->client('dogecoin')->getnewaddress($label)->get();
        return $wallet_address;
    }
   elseif($crypto == 'LTC') {
        $wallet_address = bitcoind()->client('litecoin')->getnewaddress($label)->get();
        return $wallet_address;
    }
    else {return "invalid crypto";}
}

function get_label_crypto($crypto, $address) {
    if ($crypto == 'BTC'){
        $addrinfo = bitcoind()->client('bitcoin')->getaddressinfo($address)->get();
        if($addrinfo['label'] != null){
            $label = $addrinfo['label'];
            return $label;
        }
        else{return $address;}
    }
   elseif($crypto == 'BCH') {
        $addrinfo = bitcoind()->client('bitabc')->getaddressinfo($address)->get();
        if($addrinfo['account'] != null){
            $label = $addrinfo['account'];
            return $label;
        }
        else{return $address;}
    }
   elseif($crypto == 'DASH') {
        $addrinfo = bitcoind()->client('dashcoin')->getaddressinfo($address)->get();
        if($addrinfo['account'] != null){
            $label = $addrinfo['account'];
            return $label;
        }
        else{return $address;}
    }
   elseif($crypto == 'DOGE') {
        $addrinfo = bitcoind()->client('dogecoin')->getaddressinfo($address)->get();
        if($addrinfo['account'] != null){
            $label = $addrinfo['account'];
            return $label;
        }
        else{return $address;}
    }
   elseif($crypto == 'LTC') {
        $addrinfo = bitcoind()->client('litecoin')->getaddressinfo($address)->get();
        if($addrinfo['account'] != null){
            $label = $addrinfo['account'];
            return $label;
        }
        else{return $address;}
    }
    else {return "invalid crypto";}
}


/////////////////////////////////////////////////////////////////////
///  TRANSACTIONS                 ///////////////////////////////////////
////////////////////////////////////////////////////////////////////
function listransactionall($crypto) {
    if ($crypto == 'BTC'){$crycode = 'bitcoin';}
    elseif($crypto == 'BCH'){$crycode = 'bitabc';}
    elseif($crypto == 'DASH'){$crycode = 'dashcoin';}
    elseif($crypto == 'DOGE'){$crycode = 'dogecoin';}
    elseif($crypto == 'LTC'){$crycode = 'bitcoin';}
    else {return "invalid crypto";}
    //GET all transaction
    $transaction = bitcoind()->client($crycode)->listtransactions()->get();
    return $transaction;
}
function listransaction($crypto, $label) {
    if ($crypto == 'BTC'){$crycode = 'bitcoin';}
    elseif($crypto == 'BCH'){$crycode = 'bitabc';}
    elseif($crypto == 'DASH'){$crycode = 'dashcoin';}
    elseif($crypto == 'DOGE'){$crycode = 'dogecoin';}
    elseif($crypto == 'LTC'){$crycode = 'bitcoin';}
    else {return "invalid crypto";}
    //GET all transaction
    $transaction = bitcoind()->client($crycode)->listtransactions($label)->get(); 
    return $transaction;
}

/////////////////////////////////////////////////////////////////////
///  ADMIN GET TRANSACTION             ///////////////////////////////////////
////////////////////////////////////////////////////////////////////
function gettransaction_crypto($crypto, $txid) {
    if ($crypto == 'BTC') {
        $transaction = bitcoind()->client('bitcoin')->gettransaction($txid)->get();
        return $transaction;
    }
    elseif($crypto == 'BCH'){
        $transaction = bitcoind()->client('bitabc')->gettransaction($txid)->get();
        return $transaction;
    }
    elseif($crypto == 'LTC'){
        $transaction = bitcoind()->client('litecoin')->gettransaction($txid)->get();
        return $transaction;
    }
    elseif($crypto == 'DASH'){
        $transaction = bitcoind()->client('dashcoin')->gettransaction($txid)->get();
        return $transaction;
    }
    elseif($crypto == 'DOGE'){
        $transaction = bitcoind()->client('dogecoin')->gettransaction($txid)->get();
        return $transaction;
    }
    else {return "invalid crypto";}
}


/////////////////////////////////////////////////////////////////////
///  PAYMENT / WITHDRAW / SEND                       ///////////////////////////////////////
////////////////////////////////////////////////////////////////////
function sendtoaddressRAW($crypto, $label, $recvaddress, $cryptoamount, $memo, $comm_fee) {
    if ($crypto == 'BTC'){
        $pxfeeaddr = array_keys(bitcoind()->client('bitcoin')->getaddressesbyaccount('usr_doradofees')->get())[0];
        $pxfee = $comm_fee;
        $balance = number_format(getbalance($crypto, $label)/100000000, 8, '.', '');
        $estfee = number_format(bitcoind()->client('bitcoin')->estimatesmartfee(6)->get()['feerate'], 8, '.', '');
        $total =  number_format(($cryptoamount+$estfee+$pxfee), 8, '.', '');
        $addressarr = array_keys(bitcoind()->client('bitcoin')->getaddressesbylabel($label)->get());
        foreach ($addressarr as $address) {
            $j = 0;
            $balacc[] = bitcoind()->client('bitcoin')->listunspent(1, 9999999, [$address])->get();
            $prevtxn[] = null;
            $totalin = 0;
            foreach ($balacc as $acc) {
                $i = 0;
                $ac[$j] = $acc;
                foreach ($acc as $a) {
                    $txid[$i] = $a['txid'];
                    $vout[$i] = $a['vout'];
                    $amt[$i] = $a['amount']; 
                    $scriptPubKey[$i] = $a['scriptPubKey'];
                    $totalin += $amt[$i];
                    $prevtxn[$i] = array(
                        "txid"=>$txid[$i],
                        "vout"=>$vout[$i],
                    );
                    $i++; 
                    if($totalin > $total){break;} 
                }
                $j++;
            }
            $txin = array_filter($prevtxn);
        }
        $change = number_format($totalin-$total, 8, '.', '');
        $changeaddr = array_keys(bitcoind()->client('bitcoin')->getaddressesbylabel($label)->get())[0];
        if($balance >= $total){  
            $createraw = bitcoind()->client('bitcoin')->createrawtransaction(
                $txin,
                array(
                    $recvaddress=>number_format($cryptoamount, 8, '.', ''),
                    $changeaddr=>$change,
                    $pxfeeaddr => $pxfee
                )
            )->get();
            $signing = bitcoind()->client('bitcoin')->signrawtransactionwithwallet($createraw)->get();
            $decode = bitcoind()->client('bitcoin')->decoderawtransaction($signing['hex'])->get();
            //dd("Fee: ".$estfee, "Cost: ".$total, "Input: ".$totalin, "Change: ".$change, "Before Balance: ".$balance, $decode);
            if($signing['complete'] == true){
                $txid = bitcoind()->client('bitcoin')->sendrawtransaction($signing['hex'])->get();
                return $txid;
            }
            else{return "Signing Failed. ".$decode;}
        }
        else{return "Error: insufficient fund. You need at least ".$total." ".$crypto." to perform this transaction";}
    } 
    elseif ($crypto == 'BCH') {
        $pxfeeaddr = substr(bitcoind()->client('bitabc')->getaddressesbyaccount('usr_doradofees')->get(),12);
        $pxfee = $comm_fee;
        $balance = number_format(getbalance($crypto, $label)/100000000, 8, '.', '');
        $estfee = getestimatefee($crypto);
        $total =  number_format(($cryptoamount+$estfee+$pxfee), 8, '.', '');
        $j = 0;
        $balacc[] = bitcoind()->client('bitabc')->listunspent()->get();
        $prevtxn[] = null;
        $totalin = 0;
        foreach ($balacc as $acc) {
            $ac[$j] = $acc;
            foreach ($ac as $a) {
                $i = 0;
                foreach ($a as $x) {
                    if(in_array('label', $x) == $label){
                        $txid[$i] = $x['txid'];
                        $vout[$i] = $x['vout'];
                        $amt[$i] = number_format($x['amount'], 8, '.', '');
                        $totalin += $amt[$i];
                        $prevtxn[$i] = array(
                            "txid"=>$txid[$i],
                            "vout"=>$vout[$i],
                        );
                        $i++;
                        if($totalin > $total){break;} 
                    }
                }
            }
            $j++;
            $txin = array_filter($prevtxn);
        }
        $change = number_format($totalin-$total, 8, '.', '');
        $changeaddr = substr(bitcoind()->client('bitabc')->getaddressesbyaccount($label)->get()[0],12);
        if($balance >= $total){  
            $createraw = bitcoind()->client('bitabc')->createrawtransaction(
                $txin,
                array(
                    $recvaddress=>number_format($cryptoamount, 8, '.', ''),
                    $changeaddr=>$change,
                    $pxfeeaddr => $pxfee
                )
            )->get();
            $signing = bitcoind()->client('bitabc')->signrawtransactionwithwallet($createraw)->get();
            $decode = bitcoind()->client('bitabc')->decoderawtransaction($signing['hex'])->get();
            //dd("Fee: ".$estfee, "Cost: ".$total, "Input: ".$totalin, "Change: ".$change, "Before Balance: ".$balance, $decode);
            if($signing['complete'] == true){
                $txid = bitcoind()->client('bitabc')->sendrawtransaction($signing['hex'])->get();
                return $txid;
            }
            else{return "Signing Failed. ".$decode;}
        }
        else{return "Error: insufficient fund. You need at least ".$total." ".$crypto." to perform this transaction";}
    }
   elseif ($crypto == 'DASH') {
        $pxfeeaddr = "2Mz21u7pztWWjpFdp4wt1pEbeBqoTXMrF59";
        $pxfee = "0.000024";
        $balance = number_format(getbalance($crypto, $label)/100000000, 8, '.', '');
        $estfee = number_format(bitcoind()->client('dashcoin')->estimatesmartfee(6)->get()['feerate'], 8, '.', '');
        $total =  number_format(($cryptoamount+$estfee+$pxfee), 8, '.', '');
        $addressarr = array_keys(bitcoind()->client('dashcoin')->getaddressesbylabel($label)->get());
        foreach ($addressarr as $address) {
            $j = 0;
            $balacc[] = bitcoind()->client('dashcoin')->listunspent(1, 9999999, [$address])->get();
            $prevtxn[] = null;
            $totalin = 0;
            foreach ($balacc as $acc) {
                $i = 0;
                $ac[$j] = $acc;
                foreach ($acc as $a) {
                    $txid[$i] = $a['txid'];
                    $vout[$i] = $a['vout'];
                    $amt[$i] = $a['amount']; 
                    $scriptPubKey[$i] = $a['scriptPubKey'];
                    $totalin += $amt[$i];
                    $prevtxn[$i] = array(
                        "txid"=>$txid[$i],
                        "vout"=>$vout[$i],
                    );
                    $i++; 
                    if($totalin > $total){break;} 
                }
                $j++;
            }
            $txin = array_filter($prevtxn);
        }
        $change = number_format($totalin-$total, 8, '.', '');
        $changeaddr = array_keys(bitcoind()->client('dashcoin')->getaddressesbylabel($label)->get())[0];
        if($balance >= $total){  
            $createraw = bitcoind()->client('dashcoin')->createrawtransaction(
                $txin,
                array(
                    $recvaddress=>number_format($cryptoamount, 8, '.', ''),
                    $changeaddr=>$change,
                    $pxfeeaddr => $pxfee
                )
            )->get();
            $signing = bitcoind()->client('dashcoin')->signrawtransactionwithwallet($createraw)->get();
            $decode = bitcoind()->client('dashcoin')->decoderawtransaction($signing['hex'])->get();
            //dd("Fee: ".$estfee, "Cost: ".$total, "Input: ".$totalin, "Change: ".$change, "Before Balance: ".$balance, $decode);
            if($signing['complete'] == true){
                $txid = bitcoind()->client('dashcoin')->sendrawtransaction($signing['hex'])->get();
                return $txid;
            }
            else{return "Signing Failed. ".$decode;}
        }
        else{return "Error: insufficient fund. You need at least ".$total." ".$crypto." to perform this transaction";}
    }
   elseif ($crypto == 'DOGE') {
        $pxfeeaddr = "2Mz21u7pztWWjpFdp4wt1pEbeBqoTXMrF59";
        $pxfee = "0.000024";
        $balance = number_format(getbalance($crypto, $label)/100000000, 8, '.', '');
        $estfee = number_format(bitcoind()->client('dogecoin')->estimatesmartfee(6)->get()['feerate'], 8, '.', '');
        $total =  number_format(($cryptoamount+$estfee+$pxfee), 8, '.', '');
        $addressarr = array_keys(bitcoind()->client('dogecoin')->getaddressesbylabel($label)->get());
        foreach ($addressarr as $address) {
            $j = 0;
            $balacc[] = bitcoind()->client('dogecoin')->listunspent(1, 9999999, [$address])->get();
            $prevtxn[] = null;
            $totalin = 0;
            foreach ($balacc as $acc) {
                $i = 0;
                $ac[$j] = $acc;
                foreach ($acc as $a) {
                    $txid[$i] = $a['txid'];
                    $vout[$i] = $a['vout'];
                    $amt[$i] = $a['amount']; 
                    $scriptPubKey[$i] = $a['scriptPubKey'];
                    $totalin += $amt[$i];
                    $prevtxn[$i] = array(
                        "txid"=>$txid[$i],
                        "vout"=>$vout[$i],
                    );
                    $i++; 
                    if($totalin > $total){break;} 
                }
                $j++;
            }
            $txin = array_filter($prevtxn);
        }
        $change = number_format($totalin-$total, 8, '.', '');
        $changeaddr = array_keys(bitcoind()->client('dogecoin')->getaddressesbylabel($label)->get())[0];
        if($balance >= $total){  
            $createraw = bitcoind()->client('dogecoin')->createrawtransaction(
                $txin,
                array(
                    $recvaddress=>number_format($cryptoamount, 8, '.', ''),
                    $changeaddr=>$change,
                    $pxfeeaddr => $pxfee
                )
            )->get();
            $signing = bitcoind()->client('dogecoin')->signrawtransactionwithwallet($createraw)->get();
            $decode = bitcoind()->client('dogecoin')->decoderawtransaction($signing['hex'])->get();
            //dd("Fee: ".$estfee, "Cost: ".$total, "Input: ".$totalin, "Change: ".$change, "Before Balance: ".$balance, $decode);
            if($signing['complete'] == true){
                $txid = bitcoind()->client('dogecoin')->sendrawtransaction($signing['hex'])->get();
                return $txid;
            }
            else{return "Signing Failed. ".$decode;}
        }
        else{return "Error: insufficient fund. You need at least ".$total." ".$crypto." to perform this transaction";}
    }
   elseif ($crypto == 'LTC') {
        $pxfeeaddr = "2Mz21u7pztWWjpFdp4wt1pEbeBqoTXMrF59";
        $pxfee = "0.000024";
        $balance = number_format(getbalance($crypto, $label)/100000000, 8, '.', '');
        $estfee = number_format(bitcoind()->client('litecoin')->estimatesmartfee(6)->get()['feerate'], 8, '.', '');
        $total =  number_format(($cryptoamount+$estfee+$pxfee), 8, '.', '');
        $addressarr = array_keys(bitcoind()->client('litecoin')->getaddressesbylabel($label)->get());
        foreach ($addressarr as $address) {
            $j = 0;
            $balacc[] = bitcoind()->client('litecoin')->listunspent(1, 9999999, [$address])->get();
            $prevtxn[] = null;
            $totalin = 0;
            foreach ($balacc as $acc) {
                $i = 0;
                $ac[$j] = $acc;
                foreach ($acc as $a) {
                    $txid[$i] = $a['txid'];
                    $vout[$i] = $a['vout'];
                    $amt[$i] = $a['amount']; 
                    $scriptPubKey[$i] = $a['scriptPubKey'];
                    $totalin += $amt[$i];
                    $prevtxn[$i] = array(
                        "txid"=>$txid[$i],
                        "vout"=>$vout[$i],
                    );
                    $i++; 
                    if($totalin > $total){break;} 
                }
                $j++;
            }
            $txin = array_filter($prevtxn);
        }
        $change = number_format($totalin-$total, 8, '.', '');
        $changeaddr = array_keys(bitcoind()->client('litecoin')->getaddressesbylabel($label)->get())[0];
        if($balance >= $total){  
            $createraw = bitcoind()->client('litecoin')->createrawtransaction(
                $txin,
                array(
                    $recvaddress=>number_format($cryptoamount, 8, '.', ''),
                    $changeaddr=>$change,
                    $pxfeeaddr => $pxfee
                )
            )->get();
            $signing = bitcoind()->client('litecoin')->signrawtransactionwithwallet($createraw)->get();
            $decode = bitcoind()->client('litecoin')->decoderawtransaction($signing['hex'])->get();
            //dd("Fee: ".$estfee, "Cost: ".$total, "Input: ".$totalin, "Change: ".$change, "Before Balance: ".$balance, $decode);
            if($signing['complete'] == true){
                $txid = bitcoind()->client('litecoin')->sendrawtransaction($signing['hex'])->get();
                return $txid;
            }
            else{return "Signing Failed. ".$decode;}
        }
        else{return "Error: insufficient fund. You need at least ".$total." ".$crypto." to perform this transaction";}
    }
    elseif ($crypto == 'ETH') {
        $converter = new \Bezhanov\Ethereum\Converter();
        $user = WalletAddress::where('label',$label)->where('crypto',$crypto)->first();
        $admin = WalletAddress::where('label',$label2)->where('crypto',$crypto)->first();
        $from = $user->address;
        $to = $admin->address;
        $gas = '0x'.dec2hex('100000');
        $gasprice = Gasprice::where('id',1)->first();
        $normal = $gasprice->rapid;
        if($normal == '0' || $normal == ''){$normal = 50;}
        $gasPriceData = $converter->toWei($normal, 'gwei');
        $gasPrice = '0x'.dec2hex($gasPriceData);
        $value = '0x'.dec2hex($converter->toWei($amount, 'ether'));
        $transaction = new EthereumTransaction($from, $to, $value, $gas, $gasPrice);
        $txid =  Ethereum::personal_sendTransaction($transaction,'Pinkexc@22');
        getbalance($crypto, $label);
        getbalance($crypto, $label2);
        if($txid != ''){return $txid;}
        else{return null;}
    } 
    elseif ($crypto == 'XLM') {
        $result = '';
        return $result;
    } 
    elseif ($crypto == 'XRP') {
        //$client = new Client('https://s.altnet.rippletest.net:51234');
        $client = new \FOXRP\Rippled\Client('http://178.128.105.75:5005');
        $amount_conv = strval(floatval($amount)*1000000); //1000000 equivalent to 1XRP
        $tx_type = "Payment";
        $account = WalletAddress::where('label',$label)->where('crypto',$crypto)->first()->address; //rippleuser1 
        $acc_secret = WalletAddress::where('label',$label)->where('crypto',$crypto)->first()->secret;
        $destination = WalletAddress::where('label',$label2)->where('crypto',$crypto)->first()->address; //ripple admin address
        $currency = "XRP";
        //-------------------Payment Submission-----------------------------------
        $txParams = [
          'TransactionType' => $tx_type,
          'Account' => $account,
          'Destination' => $destination,
          'Amount' => $amount_conv,
          'Fee' => '10'
        ];
        $transaction = new \FOXRP\Rippled\Api\Transaction($txParams, $client);
        $responsePay = $transaction->submit(base64_decode($acc_secret));
        if ($responsePay->isSuccess()) {
            $dataSubmit = $responsePay->getResult();
            $txid = $dataSubmit['tx_json']['hash'];
            getbalance($crypto, $label);
            getbalance($crypto, $label2);
            if($txid != ''){ return $txid;}
            else{return null;}
        }    
    }
    elseif ($crypto == 'LIFE') {
        //$pari = new EthereumRPC('blappONE:bR4k82xIvhU7uI13E123n4ng2xIvTepiY417@bapp1.pinkexc.com', 443);
        //$erc20 = new ERC20($pari);
        $contract = "0xce61f5e6D1fE5a86E246F68AFF956f7757282eF0"; // ERC20 contract address
        $user = WalletAddress::where('label',$label)->where('crypto',$crypto)->first()->address; // Sender's Ethereum account
        $admin =  WalletAddress::where('label',$label2)->where('crypto',$crypto)->first()->address; // Recipient's Ethereum account
        //$amountLIFE = strval(floatval($amount)+0.00001);
        $amountLIFE = $amount;
        // Grab instance of ERC20_Token class
        $token = $erc20->token($contract);
        // First argument is admin/recipient of this transfer
        // Second argument is the amount of tokens that will be sent
        $data = $token->encodedTransferData($admin, $amountLIFE);
        $transaction = $pari->personal()->transaction($user, $contract) // from $payer to $contract address
            ->amount("0") // Amount should be ZERO
            ->data($data); // Our encoded ERC20 token transfer data from previous step
        // Send transaction with ETH account passphrase
        $txId = $transaction->send("Pinkexc@22"); // Replace "secret" with actual passphrase of SENDER's ethereum account
        getbalance($crypto, $label);
        getbalance($crypto, $label2);
        if ($txId != '') {
            $id = WalletAddress::where('label',$label)->where('crypto',$crypto)->first()->uid;
            $update_bal1 = life_getbalance($id);
            return $txId;
        }
        else{return null;}
    }  
    else {
        $result = null;
        return $result;
    }
}

function sendtomanyaddress($crypto, $sendlabel, $recvaddress, $cryptoamount, $memo) {
    if ($crypto == 'BTC') {
        $pxfeeaddr = "2Mz21u7pztWWjpFdp4wt1pEbeBqoTXMrF59";
        $pxfee = "0.000024";
        $bal = getbalance($crypto, $sendlabel);
        $estfee = number_format(bitcoind()->client('bitcoin')->estimatesmartfee(6)->get()['feerate'], 8, '.', '');
        $txcost =  number_format(($cryptoamount+$estfee+$pxfee)*100000000, 0, '.', '');
        if ($bal >= $txcost){
            $txid = bitcoind()->client('bitcoin')->sendmany("",
                array(
                    $recvaddress => $cryptoamount,
                    $pxfeeaddr => $pxfee
                )
            )->get();
            return $txid;
        }
        else{return "Insufficient balance. You need at least ".($txcost/'10000000')." ".$crypto." to perform this transaction";}
    }
   elseif ($crypto == 'BCH') {
        $pxfeeaddr = substr(bitcoind()->client('bitabc')->getaddressesbyaccount('usr_doradofees')->get(),12);;
        $pxfee = "0.000024";
        $bal = getbalance($crypto, $sendlabel);
        $estfee = number_format(bitcoind()->client('bitabc')->estimatesmartfee(6)->get()['feerate'], 8, '.', '');
        $txcost =  number_format(($cryptoamount+$estfee+$pxfee)*100000000, 0, '.', '');
        if ($bal >= $txcost){
            $txid = bitcoind()->client('bitabc')->sendmany("",
                array(
                    $recvaddress => $cryptoamount,
                    $pxfeeaddr => $pxfee
                )
            )->get();
            return $txid;
        }
        else{return "Insufficient balance. You need at least ".($txcost/'10000000')." ".$crypto." to perform this transaction";}
    }
   elseif ($crypto == 'DASH') {
        $pxfeeaddr = "2Mz21u7pztWWjpFdp4wt1pEbeBqoTXMrF59";
        $pxfee = "0.000024";
        $bal = getbalance($crypto, $sendlabel);
        $estfee = number_format(bitcoind()->client('dashcoin')->estimatesmartfee(6)->get()['feerate'], 8, '.', '');
        $txcost =  number_format(($cryptoamount+$estfee+$pxfee)*100000000, 0, '.', '');
        if ($bal >= $txcost){
            $txid = bitcoind()->client('dashcoin')->sendmany("",
                array(
                    $recvaddress => $cryptoamount,
                    $pxfeeaddr => $pxfee
                )
            )->get();
            return $txid;
        }
        else{return "Insufficient balance. You need at least ".($txcost/'10000000')." ".$crypto." to perform this transaction";}
    }
   elseif ($crypto == 'DOGE') {
        $pxfeeaddr = "2Mz21u7pztWWjpFdp4wt1pEbeBqoTXMrF59";
        $pxfee = "0.000024";
        $bal = getbalance($crypto, $sendlabel);
        $estfee = number_format(bitcoind()->client('dogecoin')->estimatesmartfee(6)->get()['feerate'], 8, '.', '');
        $txcost =  number_format(($cryptoamount+$estfee+$pxfee)*100000000, 0, '.', '');
        if ($bal >= $txcost){
            $txid = bitcoind()->client('dogecoin')->sendmany("",
                array(
                    $recvaddress => $cryptoamount,
                    $pxfeeaddr => $pxfee
                )
            )->get();
            return $txid;
        }
        else{return "Insufficient balance. You need at least ".($txcost/'10000000')." ".$crypto." to perform this transaction";}
    }
   elseif ($crypto == 'LTC') {
        $pxfeeaddr = "2Mz21u7pztWWjpFdp4wt1pEbeBqoTXMrF59";
        $pxfee = "0.000024";
        $bal = getbalance($crypto, $sendlabel);
        $estfee = number_format(bitcoind()->client('litecoin')->estimatesmartfee(6)->get()['feerate'], 8, '.', '');
        $txcost =  number_format(($cryptoamount+$estfee+$pxfee)*100000000, 0, '.', '');
        if ($bal >= $txcost){
            $txid = bitcoind()->client('litecoin')->sendmany("",
                array(
                    $recvaddress => $cryptoamount,
                    $pxfeeaddr => $pxfee
                )
            )->get();
            return $txid;
        }
        else{return "Insufficient balance. You need at least ".($txcost/'10000000')." ".$crypto." to perform this transaction";}
    }
    else {return "invalid crypto";}
}


/////////////////////////////////////////////////////////////////////
/// DUMP PRIVATE KEY             ///////////////////////////////////////
////////////////////////////////////////////////////////////////////
function dumpkey($crypto, $label){
    if ($crypto == 'BTC'){$crycode = 'bitcoin';}
    elseif($crypto == 'BCH'){$crycode = 'bitabc';}
    elseif($crypto == 'DASH'){$crycode = 'dashcoin';}
    elseif($crypto == 'DOGE'){$crycode = 'dogecoin';}
    elseif($crypto == 'LTC'){$crycode = 'bitcoin';}
    else {return "invalid crypto";}

    $addressarr = array_keys(bitcoind()->client($crycode)->getaddressesbylabel($label)->get());
    foreach ($addressarr as $addr){
        $priv = bitcoind()->client($crycode)->dumpprivkey($addr)->get();
        $data[] = array(
            "address"=>$addr,
            "key"=>$priv
        );
    }
    return $data;
}


/////////////////////////////////////////////////////////////////////
///  DEC2HEX                ///////////////////////////////////////
////////////////////////////////////////////////////////////////////
function dec2hex($number){
    $hexvalues = array('0','1','2','3','4','5','6','7','8','9','a','b','c','d','e','f');
    $hexval = '';
    while($number != '0'){
        $hexval = $hexvalues[bcmod($number,'16')].$hexval;
        $number = bcdiv($number,'16',0);
    }
    return $hexval;
}


/////////////////////////////////////////////////////////////////////
///  MOVE TO FEES WALLET                 ///////////////////////////////////////
////////////////////////////////////////////////////////////////////
function withdrawal_fees_crypto($crypto, $sendlabel, $recvaddress, $cryptoamount, $memo) {
    if ($crypto == 'BTC'){
        $balance = number_format(getbalance($crypto, $sendlabel)/100000000, 8, '.', '');
        $estfee = number_format(bitcoind()->client('bitcoin')->estimatesmartfee(6)->get()['feerate'], 8, '.', '');
        $total =  number_format(($cryptoamount+$estfee), 8, '.', '');
        $addressarr = array_keys(bitcoind()->client('bitcoin')->getaddressesbylabel($sendlabel)->get());
        foreach ($addressarr as $address) {
            $j = 0;
            $balacc[] = bitcoind()->client('bitcoin')->listunspent(1, 9999999, [$address])->get();
            $prevtxn[] = null;
            $totalin = 0;
            foreach ($balacc as $acc) {
                $i = 0;
                $ac[$j] = $acc;
                foreach ($acc as $a) {
                    $txid[$i] = $a['txid'];
                    $vout[$i] = $a['vout'];
                    $amt[$i] = $a['amount']; 
                    $scriptPubKey[$i] = $a['scriptPubKey'];
                    $totalin += $amt[$i];
                    $prevtxn[$i] = array(
                        "txid"=>$txid[$i],
                        "vout"=>$vout[$i],
                    );
                    $i++; 
                    if($totalin > $total){break;} 
                }
                $j++;
            }
            $txin = array_filter($prevtxn);
        }
        $change = number_format($totalin-$total, 8, '.', '');
        $changeaddr = array_keys(bitcoind()->client('bitcoin')->getaddressesbylabel($sendlabel)->get())[0];
        if($balance >= $total){  
            $createraw = bitcoind()->client('bitcoin')->createrawtransaction(
                $txin,
                array(
                    $recvaddress=>number_format($cryptoamount, 8, '.', ''),
                    $changeaddr=>$change
                )
            )->get();
            $signing = bitcoind()->client('bitcoin')->signrawtransactionwithwallet($createraw)->get();
            $decode = bitcoind()->client('bitcoin')->decoderawtransaction($signing['hex'])->get();
            //dd("Fee: ".$estfee, "Cost: ".$total, "Input: ".$totalin, "Change: ".$change, "Before Balance: ".$balance, $decode);
            if($signing['complete'] == true){
                $txid = bitcoind()->client('bitcoin')->sendrawtransaction($signing['hex'])->get();
                return $txid;
            }
            else{return "Signing Failed. ".$decode;}
        }
        else{return "Error: insufficient fund. You need at least ".$total." ".$crypto." to perform this transaction";}
    } 
    elseif ($crypto == 'BCH') {
        $balance = number_format(getbalance($crypto, $sendlabel)/100000000, 8, '.', '');
        $estfee = number_format(bitcoind()->client('bitabc')->estimatesmartfee(6)->get()['feerate'], 8, '.', '');
        $total =  number_format(($cryptoamount+$estfee), 8, '.', '');
        $addressarr = array_keys(bitcoind()->client('bitabc')->getaddressesbylabel($sendlabel)->get());
        foreach ($addressarr as $address) {
            $j = 0;
            $balacc[] = bitcoind()->client('bitabc')->listunspent(1, 9999999, [$address])->get();
            $prevtxn[] = null;
            $totalin = 0;
            foreach ($balacc as $acc) {
                $i = 0;
                $ac[$j] = $acc;
                foreach ($acc as $a) {
                    $txid[$i] = $a['txid'];
                    $vout[$i] = $a['vout'];
                    $amt[$i] = $a['amount']; 
                    $scriptPubKey[$i] = $a['scriptPubKey'];
                    $totalin += $amt[$i];
                    $prevtxn[$i] = array(
                        "txid"=>$txid[$i],
                        "vout"=>$vout[$i],
                    );
                    $i++; 
                    if($totalin > $total){break;} 
                }
                $j++;
            }
            $txin = array_filter($prevtxn);
        }
        $change = number_format($totalin-$total, 8, '.', '');
        $changeaddr = array_keys(bitcoind()->client('bitabc')->getaddressesbylabel($sendlabel)->get())[0];
        if($balance >= $total){  
            $createraw = bitcoind()->client('bitabc')->createrawtransaction(
                $txin,
                array(
                    $recvaddress=>number_format($cryptoamount, 8, '.', ''),
                    $changeaddr=>$change
                )
            )->get();
            $signing = bitcoind()->client('bitabc')->signrawtransactionwithwallet($createraw)->get();
            $decode = bitcoind()->client('bitabc')->decoderawtransaction($signing['hex'])->get();
            //dd("Fee: ".$estfee, "Cost: ".$total, "Input: ".$totalin, "Change: ".$change, "Before Balance: ".$balance, $decode);
            if($signing['complete'] == true){
                $txid = bitcoind()->client('bitabc')->sendrawtransaction($signing['hex'])->get();
                return $txid;
            }
            else{return "Signing Failed. ".$decode;}
        }
        else{return "Error: insufficient fund. You need at least ".$total." ".$crypto." to perform this transaction";}
    }
    elseif ($crypto == 'DASH') {
        $balance = number_format(getbalance($crypto, $sendlabel)/100000000, 8, '.', '');
        $estfee = number_format(bitcoind()->client('dashcoin')->estimatesmartfee(6)->get()['feerate'], 8, '.', '');
        $total =  number_format(($cryptoamount+$estfee), 8, '.', '');
        $addressarr = array_keys(bitcoind()->client('dashcoin')->getaddressesbylabel($sendlabel)->get());
        foreach ($addressarr as $address) {
            $j = 0;
            $balacc[] = bitcoind()->client('dashcoin')->listunspent(1, 9999999, [$address])->get();
            $prevtxn[] = null;
            $totalin = 0;
            foreach ($balacc as $acc) {
                $i = 0;
                $ac[$j] = $acc;
                foreach ($acc as $a) {
                    $txid[$i] = $a['txid'];
                    $vout[$i] = $a['vout'];
                    $amt[$i] = $a['amount']; 
                    $scriptPubKey[$i] = $a['scriptPubKey'];
                    $totalin += $amt[$i];
                    $prevtxn[$i] = array(
                        "txid"=>$txid[$i],
                        "vout"=>$vout[$i],
                    );
                    $i++; 
                    if($totalin > $total){break;} 
                }
                $j++;
            }
            $txin = array_filter($prevtxn);
        }
        $change = number_format($totalin-$total, 8, '.', '');
        $changeaddr = array_keys(bitcoind()->client('dashcoin')->getaddressesbylabel($sendlabel)->get())[0];
        if($balance >= $total){  
            $createraw = bitcoind()->client('dashcoin')->createrawtransaction(
                $txin,
                array(
                    $recvaddress=>number_format($cryptoamount, 8, '.', ''),
                    $changeaddr=>$change
                )
            )->get();
            $signing = bitcoind()->client('dashcoin')->signrawtransactionwithwallet($createraw)->get();
            $decode = bitcoind()->client('dashcoin')->decoderawtransaction($signing['hex'])->get();
            //dd("Fee: ".$estfee, "Cost: ".$total, "Input: ".$totalin, "Change: ".$change, "Before Balance: ".$balance, $decode);
            if($signing['complete'] == true){
                $txid = bitcoind()->client('dashcoin')->sendrawtransaction($signing['hex'])->get();
                return $txid;
            }
            else{return "Signing Failed. ".$decode;}
        }
        else{return "Error: insufficient fund. You need at least ".$total." ".$crypto." to perform this transaction";}
    }
    elseif ($crypto == 'DOGE') {
        $balance = number_format(getbalance($crypto, $sendlabel)/100000000, 8, '.', '');
        $estfee = number_format(bitcoind()->client('dogecoin')->estimatesmartfee(6)->get()['feerate'], 8, '.', '');
        $total =  number_format(($cryptoamount+$estfee), 8, '.', '');
        $addressarr = array_keys(bitcoind()->client('dogecoin')->getaddressesbylabel($sendlabel)->get());
        foreach ($addressarr as $address) {
            $j = 0;
            $balacc[] = bitcoind()->client('dogecoin')->listunspent(1, 9999999, [$address])->get();
            $prevtxn[] = null;
            $totalin = 0;
            foreach ($balacc as $acc) {
                $i = 0;
                $ac[$j] = $acc;
                foreach ($acc as $a) {
                    $txid[$i] = $a['txid'];
                    $vout[$i] = $a['vout'];
                    $amt[$i] = $a['amount']; 
                    $scriptPubKey[$i] = $a['scriptPubKey'];
                    $totalin += $amt[$i];
                    $prevtxn[$i] = array(
                        "txid"=>$txid[$i],
                        "vout"=>$vout[$i],
                    );
                    $i++; 
                    if($totalin > $total){break;} 
                }
                $j++;
            }
            $txin = array_filter($prevtxn);
        }
        $change = number_format($totalin-$total, 8, '.', '');
        $changeaddr = array_keys(bitcoind()->client('dogecoin')->getaddressesbylabel($sendlabel)->get())[0];
        if($balance >= $total){  
            $createraw = bitcoind()->client('dogecoin')->createrawtransaction(
                $txin,
                array(
                    $recvaddress=>number_format($cryptoamount, 8, '.', ''),
                    $changeaddr=>$change
                )
            )->get();
            $signing = bitcoind()->client('dogecoin')->signrawtransactionwithwallet($createraw)->get();
            $decode = bitcoind()->client('dogecoin')->decoderawtransaction($signing['hex'])->get();
            //dd("Fee: ".$estfee, "Cost: ".$total, "Input: ".$totalin, "Change: ".$change, "Before Balance: ".$balance, $decode);
            if($signing['complete'] == true){
                $txid = bitcoind()->client('dogecoin')->sendrawtransaction($signing['hex'])->get();
                return $txid;
            }
            else{return "Signing Failed. ".$decode;}
        }
        else{return "Error: insufficient fund. You need at least ".$total." ".$crypto." to perform this transaction";}
    }
    elseif ($crypto == 'LTC') {
        $balance = number_format(getbalance($crypto, $sendlabel)/100000000, 8, '.', '');
        $estfee = number_format(bitcoind()->client('litecoin')->estimatesmartfee(6)->get()['feerate'], 8, '.', '');
        $total =  number_format(($cryptoamount+$estfee), 8, '.', '');
        $addressarr = array_keys(bitcoind()->client('litecoin')->getaddressesbylabel($sendlabel)->get());
        foreach ($addressarr as $address) {
            $j = 0;
            $balacc[] = bitcoind()->client('litecoin')->listunspent(1, 9999999, [$address])->get();
            $prevtxn[] = null;
            $totalin = 0;
            foreach ($balacc as $acc) {
                $i = 0;
                $ac[$j] = $acc;
                foreach ($acc as $a) {
                    $txid[$i] = $a['txid'];
                    $vout[$i] = $a['vout'];
                    $amt[$i] = $a['amount']; 
                    $scriptPubKey[$i] = $a['scriptPubKey'];
                    $totalin += $amt[$i];
                    $prevtxn[$i] = array(
                        "txid"=>$txid[$i],
                        "vout"=>$vout[$i],
                    );
                    $i++; 
                    if($totalin > $total){break;} 
                }
                $j++;
            }
            $txin = array_filter($prevtxn);
        }
        $change = number_format($totalin-$total, 8, '.', '');
        $changeaddr = array_keys(bitcoind()->client('litecoin')->getaddressesbylabel($sendlabel)->get())[0];
        if($balance >= $total){  
            $createraw = bitcoind()->client('litecoin')->createrawtransaction(
                $txin,
                array(
                    $recvaddress=>number_format($cryptoamount, 8, '.', ''),
                    $changeaddr=>$change
                )
            )->get();
            $signing = bitcoind()->client('litecoin')->signrawtransactionwithwallet($createraw)->get();
            $decode = bitcoind()->client('litecoin')->decoderawtransaction($signing['hex'])->get();
            //dd("Fee: ".$estfee, "Cost: ".$total, "Input: ".$totalin, "Change: ".$change, "Before Balance: ".$balance, $decode);
            if($signing['complete'] == true){
                $txid = bitcoind()->client('litecoin')->sendrawtransaction($signing['hex'])->get();
                return $txid;
            }
            else{return "Signing Failed. ".$decode;}
        }
        else{return "Error: insufficient fund. You need at least ".$total." ".$crypto." to perform this transaction";}
    }
    elseif ($crypto == 'XLM') {$add_crypto = '';}
    elseif ($crypto == 'XRP') {$add_crypto = '';} 
    elseif ($crypto == 'ETH') {
        $converter = new \Bezhanov\Ethereum\Converter();
        $user = WalletAddress::where('label',$label)->where('crypto',$crypto)->first();
        $admin = WalletAddress::where('label','usr_pinkexc_fees')->where('crypto',$crypto)->first();
        $from = $user->address;
        $to = $admin->address;
        $gas = '0x'.dec2hex('100000');
        $gasPrice = '0x'.dec2hex('5000000000');
        $value = '0x'.dec2hex($converter->toWei($amount, 'ether'));
        $transaction = new EthereumTransaction($from, $to, $value, $gas, $gasPrice);
        $txid =  Ethereum::personal_sendTransaction($transaction,'Pinkexc@22');

        if($txid != ''){return true;}
        else{return null;}
    } 
    elseif ($crypto == 'LIFE') {$add_crypto = '';} 
    else {
        $add_crypto = null;
        return $add_crypto;
    }
}


/////////////////////////////////////////////////////////////////////
///  GET BALANCE ALL          ///////////////////////////////////////
////////////////////////////////////////////////////////////////////
function getbalanceAll($crypto) {
    if ($crypto == 'BTC') {
        $wallet_balance = bitcoind()->client('bitcoin')->getbalance()->get();
        return $wallet_balance;
    }
    elseif ($crypto == 'BCH') {
        $wallet_balance = bitcoind()->client('bitabc')->getbalance()->get();
        return $wallet_balance;
    }
    elseif ($crypto == 'LTC') {
        $wallet_balance = bitcoind()->client('litecoin')->getbalance()->get();
        return $wallet_balance;
    }
    elseif ($crypto == 'DASH') {
        $wallet_balance = bitcoind()->client('dashcoin')->getbalance()->get();
        return $wallet_balance;
    }
    elseif ($crypto == 'DOGE') {
        $wallet_balance = bitcoind()->client('dogecoin')->getbalance()->get();
        return $wallet_balance;
    }
    else {
        $wallet_balance = null;
        return $wallet_balance;
    }
    
}


//////////////////////////////////////////
/////NEW//////////////////
//////////////////////////////////////////
function walletinfo($uid,$value){
    $verify = WalletAddress::where('uid',$uid)->first();
    return $verify->$value;
}

function verificationinfo($uid,$value){
    $verify = Verification::where('uid',$uid)->first();
    return $verify->$value;
}

function formatBytes($bytes, $precision = 2) { 
    if ($bytes > pow(1024,3)) return round($bytes / pow(1024,3), $precision)."GB";
    elseif ($bytes > pow(1024,2)) return round($bytes / pow(1024,2), $precision)."MB";
    elseif ($bytes > 1024) return round($bytes / 1024, $precision)."KB";
    else return ($bytes)."B";
} 

function idinfo($uid,$value){
    $verify = User::where('id',$uid)->first();
    return $verify->$value;
}


/////////////////////////////////////////////////////////////////////
///  API FUNCTION          ///////////////////////////////////////
////////////////////////////////////////////////////////////////////
function apiToken($session_uid){
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
///  ETH GAS PRICE DATA          ///////////////////////////////////////
////////////////////////////////////////////////////////////////////
function gaspriceData(){
    $gasprice = Gasprice::where('id',1)->first();
    $normal1 = $gasprice->average;
    $normal = $normal1+5;
    $fast1 =$gasprice->rapid;
    $fast = $fast1+10;
    $datamsg = response()->json([
        'normal' => $normal,
        'fast' => $fast
    ]);
    return $datamsg->content();
}



function withdraw_xrp($id,$amount,$crypto,$destination,$destination_tag,$fee){
    //$client = new Client('https://s.altnet.rippletest.net:51234');
    $client = new \FOXRP\Rippled\Client('http://178.128.105.75:5005');
    $destination_tag = intval($destination_tag);
    $amount_conv = strval(floatval($amount)*1000000); //1000000 equivalent to 1XRP
    $tx_type = "Payment";
    $account = WalletAddress::where('uid',$id)->where('crypto',$crypto)->first()->address; //rippleuser1 address
    $paytofee = WalletAddress::where('uid','888')->where('crypto',$crypto)->first()->address; //ripplebase address
    $acc_secret = WalletAddress::where('uid',$id)->where('crypto',$crypto)->first()->secret;
    $currency = "XRP";
    //$client = new \FOXRP\Rippled\Client('http://178.128.105.75:5005');
    //$amount = '0.00001';
    //$amount_conv = strval(floatval($amount)*1000000); //1000000 equivalent to 1XRP
    //$tx_type = "Payment";
    //$account = "rwWWo43cE8BrZPwbmKVqsZ5UzAsFAhbFx"; //"rL2KALodNX65eSfQ72rKKwYZZsKq6gLCFF"; //rippleuser1 address
    //$paytofee = "rfDXhw5kDXLZKS6FUyPCixh244KBMqzbvV"; //ripplebase address
    //$acc_secret = "ssUxydcKhNL9sFv28EmoonunKjVkq"; //"shvhNvh23T1xJkfrNjrVzbpwwjJSz";
    //$destination = "rL2KALodNX65eSfQ72rKKwYZZsKq6gLCFF"; //rippleuser1 address
    //$currency = "XRP";
    //$destination_tag = "852456";
    //-------------------Payment Submission-----------------------------------
    if($destination_tag != ''){
        $txParams = [
            'TransactionType' => $tx_type,
            'Account' => $account,
            'Destination' => $destination,
            'DestinationTag' => $destination_tag,
            'Amount' => $amount_conv,
            'Fee' => '10'
        ];
    }
    else{
         $txParams = [
           'TransactionType' => $tx_type,
           'Account' => $account,
           'Destination' => $destination,
           //'DestinationTag' => $destination_tag,
           'Amount' => $amount_conv,
           'Fee' => '10'
         ];
    }
    $transaction = new \FOXRP\Rippled\Api\Transaction($txParams, $client);
    //dd($transaction);
    //if(xrp_getbalance($id) > '20'){
    $responsePay = $transaction->submit(base64_decode($acc_secret));
    //dd($responsePay->isSuccess());
    if ($responsePay->isSuccess()) {
        $dataSubmit = $responsePay->getResult();
        $txid = $dataSubmit['tx_json']['hash'];
        $resStat = $dataSubmit['engine_result'];
        $resAcc = $dataSubmit['tx_json']['Account'];
        $resVal = $dataSubmit['tx_json']['Amount'];
        $resType = $dataSubmit['tx_json']['TransactionType'];
        $resDes = $dataSubmit['tx_json']['Destination'];
        $resFee = $dataSubmit['tx_json']['Fee'];
        //-------------------Fee Submission-----------------------------------
        $feeamount =  "1";//strval(floatval($fee)/1000000);
        //dd($feeamount);
        $feeParams = [
            'TransactionType' => $tx_type,
            'Account' => $account,
            'Destination' => $paytofee,
            'Amount' => $feeamount,
            'Fee' => '10'
        ];
        $transaction = new \FOXRP\Rippled\Api\Transaction($feeParams , $client);
        //dd($transaction);
        $responseFee = $transaction->submit(base64_decode($acc_secret));
        //dd($responseFee );
        if ($responseFee->isSuccess()) {
            $dataSubmit_fee = $responseFee->getResult();
            //dd($dataSubmit_fee);
            $resHash_fee = $dataSubmit_fee['tx_json']['hash'];
            $resStat_fee = $dataSubmit_fee['engine_result'];
            $resAcc_fee = $dataSubmit_fee['tx_json']['Account'];
            $resVal_fee = $dataSubmit_fee['tx_json']['Amount'];
            $resType_fee = $dataSubmit_fee['tx_json']['TransactionType'];
            $resDes_fee = $dataSubmit_fee['tx_json']['Destination'];
            $resFee_fee = $dataSubmit_fee['tx_json']['Fee'];
          }
          $datamsg = response()->json([
            'txid' => $txid
          ]);
          return $datamsg->content();
        }
        else {dd("Insufficient Balance");}       
}


/////////////////////////////////////////////////////////////////////
///  ANYPAY                  ///////////////////////////////////////
////////////////////////////////////////////////////////////////////
function anypaybalance(){
    $account_details = accountdetail();
    //dd($account_details);
    $data = array("command"=>1,"account_details"=>$account_details);
    $data_string = json_encode($data);
    $response = anypaycurl($data_string);
    if(isset($response->Response[0]->response_desc)){
        return $response->Response[0]->response_desc;
    }
    else{return $response->Response[0]->balance;}
}

function anypaytopup($operator_code,$order_req_id,$reload_number,$amount){
    $account_details = accountdetail();
    //dd($account_details);
    $data = array(
        "command"=>2,
        "account_details"=>$account_details,
        "operator_code"=>$operator_code,
        "order_req_id"=>$order_req_id,
        "reload_number"=>$reload_number,
        "amount"=>$amount
    );
    $data_string = json_encode($data);
    $response = anypaycurl($data_string);
    if($response->Response[0]->response_desc=='InProcess'){return $response->Response[0];}
    else{return $response->Response[0]->response_desc;}
}

function anypaytopupstatus($order_req_id){
    $account_details = accountdetail();
    //dd($account_details);
    $data = array("command"=>3,"account_details"=>$account_details,"order_req_id"=>$order_req_id);
    $data_string = json_encode($data);
    $response = anypaycurl($data_string);
    if($response->Response[0]->response_desc=='Success'){return $response->Response[0];}
    elseif($response->Response[0]->response_desc=='Failed'){return $response->Response[0];}
    else{return $response->Response[0]->response_desc;}
}

function anypayrecharge($operator_code,$order_req_id,$amount){
    $account_details = accountdetail();
    //dd($account_details);
    $data = array("command"=>4,"account_details"=>$account_details,"operator_code"=>$operator_code,
    "order_req_id"=>$order_req_id,
    "amount"=>$amount);
    $data_string = json_encode($data);
    $response = anypaycurl($data_string);

    if($response->Response[0]->response_desc=='Success'){return $response->Response[0];}
    else{return $response->Response[0];}
}

function accountdetail(){
    $loginid = '01120511577';
    $password = 'UaR34vvE$oMe';
    $account_details = $loginid.'|'.$password;
    $encrypted_account = base64_encode($account_details);
    return $encrypted_account;
}

function anypaycurl($data_string){
    $ch = curl_init('https://api.anypay.my/user/api/v4/request');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data_string))
    );
    $result = curl_exec($ch);
    $result = json_decode($result); 
    return $result;
}

function anypay_transaction($id,$label,$crypto,$crypto_balance,$myr_amount,$reload_number,$operator_code,$platform){
    $curr_price = PriceApi::where('crypto',$crypto)->first()->price;
    $crypto_amount = round($myr_amount/$curr_price,5);
    $crypto_commission = $operator_code["commission"] * $crypto_amount;
    $after_balance = $crypto_balance - $crypto_amount;
    $move = move_crypto_comment($crypto, $label,'usr_jompay', $crypto_amount,'sell');
    if($crypto == 'ETH' && $move == null){$hash = "error";}
    elseif($crypto =='ETH'){$hash = $move;}
    else{$hash = '1';}
    $anypay_trans = Anypaytrans::create([
        'uid'=>$id,
        'before_bal'=>round($crypto_balance,8), 
        'myr_amount'=>$myr_amount,  
        'crypto_amount'=>$crypto_amount,
        'after_bal'=>round($after_balance,8),
        'curr_price'=>round($curr_price,5), 
        'crypto'=>$crypto,
        'txid'=>$hash,
        'crypto_release'=>'1',  
        'commission'=>$operator_code["commission"],
        'crypto_commission'=>round($crypto_commission,8),
        'reload_number'=>$reload_number,
        'platform'=>$platform,
        'operator_name'=>$operator_code["name"],
    ]);
    $order_req_id = refforbuy($platform, $crypto, $anypay_trans->id);
    if($hash == "error"){return "error";}
    else{$response = anypaytopup($operator_code["code"],$order_req_id,$reload_number,$myr_amount);}
    if($response->response_desc=='InProcess'){
        $anypay_update = Anypaytrans::where('id',$anypay_trans->id)->update([
            'order_req_id'=>$order_req_id,
            'txnid'=>$response->txnid,
            'status'=>'success'
        ]);
        //dd($response);
        $end = $response;
    }
    else{
        $response_status=$response->response_status;
        $response_desc=$response->response_desc;
        $anypay_update = Anypaytrans::where('id',$anypay_trans->id)->update([
            'order_req_id'=>$order_req_id,
            'status'=>'failed',
            'response_status'=>$response_status,
            'response_desc'=>$response_desc
        ]);
        $end = 'error';
    }
    return $end;
}

function anypay_code($operator_name){
    $operator_list = Anypayop::where('name',$operator_name)->first();
    if(isset($operator_list)){
        $operator_code = array(
            'code'=>$operator_list->code,
            'commission'=>$operator_list->commission,
            'name'=>$operator_list->name
        );
    }
    else{$operator_code = 'error';}
    return $operator_code;
}

function correct_number($reload_number){
  $reload_number = str_replace("+6","",$reload_number);
  $reload_number = str_replace(" ","",$reload_number);
  $reload_number = str_replace("-","",$reload_number);
  return $reload_number;
}


///////////////////////////////////////////////////////////////
/// ADDITIONAL FUNCTION /////////////////////////////////////////////////
/////////////////////////////////////////////////////////////
##========ETH TX BULK=======##
function transactions_etharr($address) {
    $json_url = "https://api.etherscan.io/api?module=account&action=txlist&address=$address&startblock=0&endblock=99999999&sort=asc&apikey=91V2W6YN95VHHFBPRT18225VYCKI8FMNXB";
    $json = file_get_contents($json_url);
    $data = json_decode($json);
    return $data->result;
}

##========SEND BULK=======##
function ethbulk($sender,$amount){
    $converter = new \Bezhanov\Ethereum\Converter();
    $from = $sender;
    $to = "0x0fb4761988aac63d87f629a38bec7c3d6ad078a8";
    $dustdat = WalletAddress::where('crypto', 'ETH')->where('address',$sender)->first();
    $gas = '0x186a0';
    $gasPrice = '0x2540be400';
    $value = $amount;
    $spend_eth = substr(number_format($converter->fromWei(hexdec($value), 'ether'), 18, '.', ''), 0, -10);
    $transaction = new EthereumTransaction($from, $to, $value, $gas, $gasPrice);
    //dd($transaction); 
    $txid = Ethereum::personal_sendTransaction($transaction,'Pinkexc@22');
    if($txid != null){
        $eth_dep = ETHDeplete::create([
            'uid' => $dustdat->uid,
            'username' => $dustdat->label,
            'datetime' => Carbon::now(),
            'from_address' => $from,
            'to_address' => $to,
            'category' => 'OUT',
            'amount' => $spend_eth,
            'txid' => $txid,
            'confirmation' => '',
            'status' => 'INITIATED'
        ]);
    }
    else{
        $eth_dep = ETHDeplete::create([
            'uid' => $dustdat->uid,
            'username' => $dustdat->label,
            'datetime' => Carbon::now(),
            'from_address' => $from,
            'to_address' => $to,
            'category' => 'OUT',
            'amount' => $spend_eth,
            'txid' => $txid,
            'confirmation' => '',
            'status' => 'FAILED'
        ]);
    }
    $balup = getbalanceeth($from);
    return $txid;
}

##========SEND LABEL=======##
function send_eth_label($sender,$receiver,$amount){
    $converter = new \Bezhanov\Ethereum\Converter();
    $user = WalletAddress::where('label',$sender)->where('crypto','ETH')->first();
    $from = $user->address;
    $to = WalletAddress::where('label',$receiver)->where('crypto','ETH')->first()->address;
    $gas = '0x'.dec2hex('100000');
    $gasPrice = '0x'.dec2hex('5000000000');
    $value = '0x'.dec2hex($converter->toWei($amount, 'ether'));
    $transaction = new EthereumTransaction($from, $to, $value, $gas, $gasPrice);
    //if(Auth::id()=="29285"){
    //dd($transaction);
    $bal1 = getbalance('ETH', $sender);
    //return Ethereum::personal_sendTransaction($transaction,'P-HY,mUr)PfGQ9NW/BNs:+q3>)YLb+Q8uz"gq;(!*Avd*EQd');
    return Ethereum::personal_sendTransaction($transaction,'Pinkexc@22');
}

##========REF NUMBER FOR BUY=======##
function refforbuy($platform, $crypto, $buyid){
    if($platform == 'mobile'){$code='M';}
    else{$code='W';}
    if($crypto == 'BTC'){$refnum = $code.'COLB' . $buyid;}
    elseif($crypto == 'BCH'){$refnum = $code.'COLBC' . $buyid;}
    elseif($crypto == 'LTC'){$refnum = $code.'COLL' . $buyid;}
    elseif($crypto == 'DASH'){$refnum = $code.'COLDH' . $buyid;}
    elseif($crypto == 'DOGE'){$refnum = $code.'COLD' . $buyid;}
    elseif($crypto == 'XLM'){$refnum = $code.'COLX' . $buyid;}
    elseif($crypto == 'ETH'){$refnum = $code.'COLE' . $buyid;}
    elseif($crypto == 'XRP'){$refnum = $code.'COLR' . $buyid;}
    return $refnum;
}

##========XLM SEND TO ADMIN=======##
function xlmtoadmin($crypto_amount,$memo){
    $account_id = StellarInfo::where('id',1)->first()->account_id;
    //$memo = '2;'.time();
    $num = $crypto_amount;
    $seed_id = StellarInfo::where('id',2)->first()->seed_id;
    $server = Server::publicNet();
    $sourceKeypair = Keypair::newFromSeed($seed_id);
    $destinationAccountId = $account_id;
    $destinationAccount = $server->getAccount($destinationAccountId);
    $transaction = \ZuluCrypto\StellarSdk\Server::publicNet()
        ->buildTransaction($sourceKeypair->getPublicKey())
        ->addOperation(PaymentOp::newNativePayment($destinationAccountId, $num))
        ->setTextMemo($memo);  
        
    $response = $transaction->submit($sourceKeypair->getSecret());
    return $response;
}

##========XLM SEND=======##
function xlmsend2($crypto_amount,$memo,$account_id,$account_type){
    //$account_id = StellarInfo::where('id',1)->first()->account_id;
    //$memo = '2;'.time();
    $num = $crypto_amount;
    if($account_type=='admin'){$seed_id = StellarInfo::where('id',1)->first()->seed_id;}
    elseif($account_type=='user'){$seed_id = StellarInfo::where('id',2)->first()->seed_id;}
    elseif($account_type=='coinvata'){$seed_id = StellarInfo::where('id',3)->first()->seed_id;}
    else{return 'error';}
    $server = Server::publicNet();
    $sourceKeypair = Keypair::newFromSeed($seed_id);
    $destinationAccountId = $account_id;
    $destinationAccount = $server->getAccount($destinationAccountId);
    $transaction = \ZuluCrypto\StellarSdk\Server::publicNet()
        ->buildTransaction($sourceKeypair->getPublicKey())
        ->addOperation(PaymentOp::newNativePayment($destinationAccountId, $num))
        ->setTextMemo($memo);  
        
    $response = $transaction->submit($sourceKeypair->getSecret());
    return $response;
}
