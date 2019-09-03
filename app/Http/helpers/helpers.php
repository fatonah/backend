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
  Mail::to($to)->send(new verifyMail($msgData));
}

function send_reset_password($to, $subject, $name, $message, $hash){
  $msgData = array(
    "uname" => $name,
    "msg" => $message,
    "passhash" => $hash,
    "supportemail" => "supports@dorado.com"
  );
  Mail::to($to)->send(new resetPassword($msgData));
}

function send_supports_email($to, $subject, $name, $message){
  $msgData = array(
    "uname" => $name,
    "msg" => $message,
    "supportemail" => "supports@dorado.com"
  );
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
        if($amt != null) {
            $wallet_balance = array_sum($amt);
        }
        else {
            $wallet_balance = 0;
        }
        return $wallet_balance;
    }
    //#======TESTNET==========#
    // if ($crypto == 'BTC'){
    //     $addressarr = array_keys(bitcoind()->client('bitcoin')->getaddressesbylabel($label)->get());
    //     dd($addressarr);
    //     $amt = null;
    //     foreach ($addressarr as $address) {
    //         $balacc = bitcoind()->client('bitcoin')->listunspent(1, 9999999, [$address])->get();
    //         $balance = 0;
    //         if(in_array('txid', $balacc)){
    //             $amt[] =  (int)number_format($balacc['amount']*100000000, 8, '.', '');
    //             foreach ($amt as $a) {$balance += $a;}
    //         }
    //         else{
    //             foreach ($balacc as $acc) {$amt[] = (int)number_format($acc['amount']*100000000, 8, '.', '');}
    //         }
    //     }
    //     $wallet_balance = array_sum($amt);
    //     return $wallet_balance;
    // }
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
    else {
        $wallet_balance = null;
        return $wallet_balance;
    }
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
        $wallet_address = array_keys(bitcoind()->client('bitcoin')->getaddressesbylabel($label)->get())[0];
        return $wallet_address;
    }
   elseif($crypto == 'BCH') {
        $wallet_address = substr(bitcoind()->client('bitabc')->getaddressesbyaccount($label)->get()[0],12);
        return $wallet_address;
    }
   elseif($crypto == 'DASH') {
        $wallet_address = bitcoind()->client('dashcoin')->getaddressesbyaccount($label)->get();
        return $wallet_address;
    }
   elseif($crypto == 'DOGE') {
        $wallet_address = bitcoind()->client('dogecoin')->getaddressesbyaccount($label)->get()[0];
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

