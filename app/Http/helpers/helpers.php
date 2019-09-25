<?php
 
use App\Avant\LNDAvtClient;
use App\Setting;
use App\PriceCrypto;
use App\WalletAddress;
use App\User;
use App\TransLND;
 
 function test(){
    $lnrest = new LNDAvtClient();
    $conn = $lnrest->getAllChannels();
    return $conn;
 }
///////////////////////////////////////////////////////////////
/// SETTINGS /////////////////////////////////////////////////
/////////////////////////////////////////////////////////////
function settings($value){
    $setting = Setting::first();
    return $setting->$value;
}
 
///////////////////////////////////////////////////////////////
/// SEND EMAIL /////////////////////////////////////////////////
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

 

/////////////////////////////////////////////////////////////////////
///  CONN CRYPTO                     ///////////////////////////////////////
////////////////////////////////////////////////////////////////////
function getconnection($crypto){
    if ($crypto == 'BTC'){
        $crycode = 'bitcoin';
        $conn = bitcoind()->client($crycode)->getBlockchainInfo()->get();
        return $conn;
    }
    elseif($crypto == 'BCH'){
        $crycode = 'bitabc';
        $conn = bitcoind()->client($crycode)->getBlockchainInfo()->get();
        return $conn;
    }
    elseif($crypto == 'DASH'){
        $crycode = 'dashcoin';
        $conn = bitcoind()->client($crycode)->getBlockchainInfo()->get();
        return $conn;
    }
    elseif($crypto == 'DOGE'){
        $crycode = 'dogecoin';
        $conn = bitcoind()->client($crycode)->getBlockchainInfo()->get();
        return $conn;
    }
    elseif($crypto == 'LTC'){
        $crycode = 'litecoin';
        $conn = bitcoind()->client($crycode)->getBlockchainInfo()->get();
        return $conn;
    }
    elseif($crypto == 'LND'){
        $crycode = 'lightning';
        $lnrest = new LNDAvtClient();
        $conn = $lnrest->getInfo();
        return $conn;
    }
    else {
        $msg = array("error"=>"Invalid Crypto");
        return $msg;
    }
   
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
        $crycode = 'litecoin';
        $fee = number_format(bitcoind()->client($crycode)->estimatesmartfee($numberblock)->get()['feerate'], 8, '.', '');
        return $fee;
    }
    elseif($crypto == 'LTC'){
        $crycode = 'litecoin';
        $fee = number_format(bitcoind()->client($crycode)->estimatesmartfee($numberblock)->get()['feerate'], 8, '.', '');
        return $fee;
    }
    elseif ($crypto == 'LND'){
        $crycode = 'bitcoin';
        $fee = number_format(bitcoind()->client($crycode)->estimatesmartfee($numberblock)->get()['feerate'], 8, '.', '');
        return $fee;
    }
    // elseif($crypto == 'LND'){
    //     $crycode = 'lightning';
    //     $lnrest = new LNDAvtClient();
    //     $allchan = $lnrest->getAllChannels();
    //     foreach ($allchan as $chan ) {
    //         foreach ($chan as $c ) {
    //             $feeall = $lnrest->getFee();
    //             foreach ($feeall as $feearr ) {
    //                 $i=0;
    //                 foreach ($feearr as $arr) {
    //                     $chanpoint = $arr['chan_point'];
    //                     $base_fee_msat = number_format($arr['base_fee_msat'], 8, '.', '');
    //                     $fee_rate = number_format($arr['fee_rate']*100000000, 8, '.', '');
    //                     $ch[$i] = array(
    //                         'remote_pubkey'=>$c['remote_pubkey'],
    //                         'channel_point'=>$c['channel_point'],
    //                         'base_fee_msat'=> $base_fee_msat,
    //                         'fee_rate'=> $fee_rate
    //                     );
    //                     $i++;
    //                 }
    //             }
    //         }
    //     }
    //     $fee = $ch;
    //     return $fee;
    // }
    else {
        $msg = array("error"=>"Invalid Crypto");
        return $msg;
    }
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
        WalletAddress::where('label', $label)->where('crypto', 'BTC')->update(['balance' => $wallet_balance]);
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
        if($amt != null) {$balance = $balance;}
        else {$balance = 0;}
        $wallet_balance = (int)number_format($balance*100000000, 8, '.', '');
        WalletAddress::where('label', $label)->where('crypto', $crypto)->update(['balance' => $wallet_balance]);
        return $wallet_balance;
    }
    elseif($crypto == 'DASH'){
        $wallet_balance = bitcoind()->client('dashcoin')->getbalance($label)->get();
        WalletAddress::where('label', $label)->where('crypto', $crypto)->update(['balance' => $wallet_balance]);
        return $wallet_balance;
    }
    elseif($crypto == 'DOGE'){
        $j = 0;
        $balacc[] = bitcoind()->client('dogecoin')->listunspent()->get();
        $balance = 0;
        foreach ($balacc as $acc) {
            $ac[$j] = $acc;
            foreach ($ac as $a) {
                $i = 0;
                foreach ($a as $x) {
                    $labelret[$i] = $x['account'];
                    if( $labelret[$i] == $label){
                        $amt[$i] = number_format($x['amount'], 8, '.', '');
                        $balance += $amt[$i];
                        $i++; 
                    }
                }
            }
            $j++;
        }
        if($amt != null) {$balance = $balance;}
        else {$balance = 0;}
        $wallet_balance = (int)number_format($balance*100000000, 8, '.', '');
        WalletAddress::where('label', $label)->where('crypto', $crypto)->update(['balance' => $wallet_balance]);
        return $wallet_balance;
    }
    elseif($crypto == 'LTC'){
        $wallet_balance = bitcoind()->client('litecoin')->getbalance($label)->get();
        WalletAddress::where('label', $label)->where('crypto', $crypto)->update(['balance' => $wallet_balance]);
        return $wallet_balance;
    }
    elseif($crypto == 'LND'){
        $user = WalletAddress::where('label', $label)->where('crypto', $crypto)->first();
        //$trans = TransLND::where('uid', $user->uid)->where('status', 'success')->latest()->first();
        $trans = TransLND::where('uid', $user->uid)->latest()->first();
        if(!$trans) {
            $wallet_balance = 0;
            WalletAddress::where('label', $label)->where('crypto', $crypto)->update(['balance' => $wallet_balance]);
            return $wallet_balance; 
        }
        $wallet_balance = $trans->after_bal;
        WalletAddress::where('label', $label)->where('crypto', $crypto)->update(['balance' => $wallet_balance]);
        return $wallet_balance;
    }
    else {
        $wallet_balance = null;
        return $wallet_balance;
    }
}
 

/////////////////////////////////////////////////////////////////////
///  ADDRESS                      ///////////////////////////////////////
////////////////////////////////////////////////////////////////////
function getaddress($crypto, $label) { 
    if ($crypto == 'BTC'){
        getbalance($crypto, $label);
        $wallet_address = array_keys(bitcoind()->client('bitcoin')->getaddressesbylabel($label)->get())[0];   
        return $wallet_address;
    }
    elseif($crypto == 'BCH') {
        getbalance($crypto, $label);
        $wallet_address = substr(bitcoind()->client('bitabc')->getaddressesbyaccount($label)->get()[0],12);
        return $wallet_address;
    }
    elseif($crypto == 'DASH') {
        getbalance($crypto, $label);
        $wallet_address = bitcoind()->client('dashcoin')->getaddressesbyaccount($label)->get();
        return $wallet_address;
    }
    elseif($crypto == 'DOGE') {
        getbalance($crypto, $label);
        $wallet_address = bitcoind()->client('dogecoin')->getaddressesbyaccount($label)->get()[0];
        return $wallet_address;
    }
    elseif($crypto == 'LTC') {
        getbalance($crypto, $label);
        $wallet_address = bitcoind()->client('litecoin')->getaddressesbyaccount($label)->get();
        return $wallet_address;
    }
    elseif($crypto == 'LND') {
        getbalance($crypto, $label);
        $lnrest = WalletAddress::where('crypto',$crypto)->where('label',$label)->first();
        $wallet_address = $lnrest->address;
        return $wallet_address;
    }
    else {
        $msg = array("error"=>"Invalid Crypto");
        return $msg;
    }
}
 

/////////////////////////////////////////////////////////////////////
///  ADD CRYPTO              ///////////////////////////////////////
////////////////////////////////////////////////////////////////////

function addCrypto($crypto, $label) {
    if ($crypto == 'BTC'){
        bitcoind()->client('bitcoin')->getnewaddress($label)->get();
        $wallet_address = bitcoind()->client('bitcoin')->getnewaddress($label)->get();
        return $wallet_address;
    }
    elseif($crypto == 'BCH') {
        bitcoind()->client('bitabc')->getnewaddress($label)->get();
        $wallet_address = bitcoind()->client('bitabc')->getnewaddress($label)->get();
        return substr($wallet_address,12);
    }
    elseif($crypto == 'DASH') {
        bitcoind()->client('dashcoin')->getnewaddress($label)->get();
        $wallet_address = bitcoind()->client('dashcoin')->getnewaddress($label)->get();
        return $wallet_address;
    }
    elseif($crypto == 'DOGE') {
        bitcoind()->client('dogecoin')->getnewaddress($label)->get();
        $wallet_address = bitcoind()->client('dogecoin')->getnewaddress($label)->get();
        return $wallet_address;
    }
    elseif($crypto == 'LTC') {
        bitcoind()->client('litecoin')->getnewaddress($label)->get();
        $wallet_address = bitcoind()->client('litecoin')->getnewaddress($label)->get();
        return $wallet_address;
    }
    elseif($crypto == 'LND') {
        $lnrest = new LNDAvtClient();
        $walletdet = $lnrest->newAddress();
        $wallet_address = $walletdet['address'];
        return $wallet_address;
    }
    else {
        $msg = array("error"=>"Invalid Crypto");
        return $msg;
    }
}

function get_label_crypto($crypto, $address) {
    if ($crypto == 'BTC'){
        $addrinfo = bitcoind()->client('bitcoin')->getaddressinfo($address)->get();
        if($addrinfo['labels'] != null){
            $label = $addrinfo['label'];
            return $label;
        }
        else{return null;}
    }
    elseif($crypto == 'BCH') {
        $addrinfo = bitcoind()->client('bitabc')->getaddressinfo($address)->get();
        if($addrinfo['account'] != null){
            $label = $addrinfo['account'];
            return $label;
        }
        else{return null;}
    }
    elseif($crypto == 'DASH') {
        $addrinfo = bitcoind()->client('dashcoin')->getaccount($address)->get();
        if($addrinfo != null){
            $label = $addrinfo;
            return $label;
        }
        else{return null;}
    }
    elseif($crypto == 'DOGE') {
        $addrinfo = bitcoind()->client('dogecoin')->getaccount($address)->get();
        if($addrinfo != null){
            $label = $addrinfo;
            return $label;
        }
        else{return null;}
    }
    elseif($crypto == 'LTC') {
        $addrinfo = bitcoind()->client('litecoin')->getaccount($address)->get();
        if($addrinfo != null){
            $label = $addrinfo;
            return $label;
        }
        else{return null;}
    }
    elseif($crypto == 'LND') {
        $addrinfo = WalletAddress::where('crypto',$crypto)->where('address',$address)->first();
        if($addrinfo != null){
            $label = $addrinfo->label;
            return $label;
        }
        else{return null;}
    }
    else {
        $msg = array("error"=>"Invalid Crypto");
        return $msg;
    }
}


/////////////////////////////////////////////////////////////////////
///  TRANSACTIONS                 ///////////////////////////////////////
////////////////////////////////////////////////////////////////////
function listransactionall($crypto) {
    if ($crypto == 'BTC'){
        $crycode = 'bitcoin';
        //GET all transaction
        $transaction = bitcoind()->client($crycode)->listtransactions()->get();
        if($transaction){return $transaction;}
        else{return null;}
    }
    elseif($crypto == 'BCH'){
        $crycode = 'bitabc';
        //GET all transaction
        $transaction = bitcoind()->client($crycode)->listtransactions()->get();
        if($transaction){return $transaction;}
        else{return null;}
    }
    elseif($crypto == 'DASH'){
        $crycode = 'dashcoin';
        //GET all transaction
        $transaction = bitcoind()->client($crycode)->listtransactions()->get();
        if($transaction){return $transaction;}
        else{return null;}
    }
    elseif($crypto == 'DOGE'){
        $crycode = 'dogecoin';
        //GET all transaction
        $transaction = bitcoind()->client($crycode)->listtransactions()->get();
        if($transaction){return $transaction;}
        else{return null;}
    }
    elseif($crypto == 'LTC'){
        $crycode = 'litecoin';
        //GET all transaction
        $transaction = bitcoind()->client($crycode)->listtransactions()->get();
        if($transaction){return $transaction;}
        else{return null;}
    }
    elseif($crypto == 'LND'){
        $crycode = 'lightning';
        $lnrest = new LNDAvtClient();
        $transaction = $lnrest->getPayments();
        if($transaction){return $transaction;}
        else{return null;}
    }
    else {
        $msg = array("error"=>"Invalid Crypto");
        return $msg;
    }
}


function listransaction($crypto, $label) {
    if ($crypto == 'BTC'){
        $crycode = 'bitcoin';
        //GET label transaction
        $transaction = bitcoind()->client($crycode)->listtransactions($label)->get(); 
        if($transaction){return $transaction;}
        else{return null;}
    }
    elseif($crypto == 'BCH'){
        $crycode = 'bitabc';
        //GET label transaction
        $transaction = bitcoind()->client($crycode)->listtransactions($label)->get(); 
        if($transaction){return $transaction;}
        else{return null;}
    }
    elseif($crypto == 'DASH'){
        $crycode = 'dashcoin';
        //GET label transaction
        $transaction = bitcoind()->client($crycode)->listtransactions($label)->get(); 
        if($transaction){return $transaction;}
        else{return null;}
    }
    elseif($crypto == 'DOGE'){
        $crycode = 'dogecoin';
        //GET label transaction
        $transaction = bitcoind()->client($crycode)->listtransactions($label)->get(); 
        if($transaction){return $transaction;}
        else{return null;}
    }
    elseif($crypto == 'LTC'){
        $crycode = 'litecoin';
        //GET label transaction
        $transaction = bitcoind()->client($crycode)->listtransactions($label)->get(); 
        if($transaction){return $transaction;}
        else{return null;}
    }
    elseif($crypto == 'LND'){
        $crycode = 'lightning';
        //GET label transaction
        $user = WalletAddress::where('label', $label)->first();
        $transaction = TransLND::where('uid',$user->uid)->get(); 
        if($transaction){return $transaction;}
        else{return null;}
    }
    else {
        $msg = array("error"=>"Invalid Crypto");
        return $msg;
    }
    // //GET all transaction
    // $transaction = bitcoind()->client($crycode)->listtransactions($label)->get(); 
    // //$transactionsend = listransactionall($crypto);
    // if($transaction){
    //     // foreach ($transaction as $tx) {
    //     //     if($tx['label'] == $label){$usrtx[] = $tx;}
    //     //     if($tx['account'] == $label){$usrtx[] = $tx;}
    //     // }
    //     // dd($usrtx);
    //     // // return $usrtx;
    //     // dd($transaction);
    //     return $transaction;
    // }
    // else{return null;}
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
    elseif($crypto == 'LND'){
        $lnrest = new LNDAvtClient();
        $invdet = $lnrest->getInvoice($txid);
        $payreq = $invdet['payment_request'];
        $transaction = $lnrest->decodeInvoice($payreq);
        return $transaction;
    }
    else {
        $msg = array("error"=>"Invalid Crypto");
        return $msg;
    }
}


/////////////////////////////////////////////////////////////////////
///  CHECK ADDRESS           ///////////////////////////////////////
////////////////////////////////////////////////////////////////////
function checkAddress($crypto, $address) {
    if ($crypto == 'BTC'){$valid = bitcoind()->client('bitcoin')->validateaddress($address)->get()['isvalid'];}
    elseif($crypto == 'BCH'){$valid = bitcoind()->client('bitabc')->validateaddress($address)->get()['isvalid'];}
    elseif($crypto == 'DASH'){$valid = bitcoind()->client('dashcoin')->validateaddress($address)->get()['isvalid'];}
    elseif($crypto == 'DOGE'){$valid = bitcoind()->client('dogecoin')->validateaddress($address)->get()['isvalid'];}
    elseif($crypto == 'LTC'){$valid = bitcoind()->client('litecoin')->validateaddress($address)->get()['isvalid'];}
    else{$valid = false;}  
    return $valid;
}


/////////////////////////////////////////////////////////////////////
///  PAYMENT / WITHDRAW / SEND                       ///////////////////////////////////////
////////////////////////////////////////////////////////////////////
function sendtoaddressRAW($crypto, $label, $recvaddress, $cryptoamount, $memo, $comm_fee) {
    if ($crypto == 'BTC'){ 
        $pxfeeaddr = array_keys(bitcoind()->client('bitcoin')->getaddressesbylabel('usr_doradofees')->get())[0];
        $pxfee = $comm_fee;
        $balance = number_format(getbalance($crypto, $label)/100000000, 8, '.', '');
        $estfee = getestimatefee($crypto);
        $total =  number_format(($cryptoamount+$estfee+$pxfee), 8, '.', '');
        $j = 0;
        $balacc[] = bitcoind()->client('bitcoin')->listunspent()->get();
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
           //  dd("Fee: ".$estfee, "Cost: ".$total, "Input: ".$totalin, "Change: ".$change, "Before Balance: ".$balance, $decode, $createraw, $signing);
            if($signing['complete'] == true){
                $txid = bitcoind()->client('bitcoin')->sendrawtransaction($signing['hex'])->get();
                getbalance($crypto, $label);
                return $txid;
            }
           else{
                $msg = array('error'=>"Signing Failed. ".$decode);
                return $msg;
            }
        }
        else{
            $msg = array('error'=>"Insufficient fund. You need at least ".$total." ".$crypto." to perform this transaction");
            return $msg;
        }
   
    } 
    elseif ($crypto == 'BCH') {
        $pxfeeaddr = substr(bitcoind()->client('bitabc')->getaddressesbyaccount('usr_doradofees')->get()[0],12);
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
        $changeaddr = bitcoind()->client('bitabc')->getaddressesbyaccount($label)->get()[0];
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
                getbalance($crypto, $label);
                return $txid;
            }
            else{
                $msg = array('error'=>"Signing Failed. ".$decode);
                return $msg;
            }
        }
        else{
            $msg = array('error'=>"Insufficient fund. You need at least ".$total." ".$crypto." to perform this transaction");
            return $msg;
        }
    }
    elseif ($crypto == 'DOGE') {
        $pxfeeaddr = bitcoind()->client('dogecoin')->getaddressesbyaccount('usr_doradofees')->get()[0];
        $pxfee = $comm_fee;
        $balance = number_format(getbalance($crypto, $label)/100000000, 8, '.', '');
        $estfee = getestimatefee($crypto);
        $total =  number_format(($cryptoamount+$estfee+$pxfee), 8, '.', '');
        $j = 0;
        $balacc[] = bitcoind()->client('dogecoin')->listunspent()->get();
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
        $changeaddr = bitcoind()->client('dogecoin')->getaddressesbyaccount($label)->get()[0];
        if($balance >= $total){  
            $createraw = bitcoind()->client('dogecoin')->createrawtransaction(
                $txin,
                array(
                    $recvaddress=>number_format($cryptoamount, 8, '.', ''),
                    $changeaddr=>$change,
                    $pxfeeaddr => $pxfee
                )
            )->get();
            $signing = bitcoind()->client('dogecoin')->signrawtransaction($createraw)->get();
            $decode = bitcoind()->client('dogecoin')->decoderawtransaction($signing['hex'])->get();
            //dd("Fee: ".$estfee, "Cost: ".$total, "Input: ".$totalin, "Change: ".$change, "Before Balance: ".$balance, $decode);
            if($signing['complete'] == true){
                $txid = bitcoind()->client('dogecoin')->sendrawtransaction($signing['hex'])->get();
                getbalance($crypto, $label);
                return $txid;
            }
            else{
                $msg = array('error'=>"Signing Failed. ".$decode);
                return $msg;
            }
        }
        else{
            $msg = array('error'=>"Insufficient fund. You need at least ".$total." ".$crypto." to perform this transaction");
            return $msg;
        }
    }
    elseif ($crypto == 'DASH') {
        $pxfeeaddr = bitcoind()->client('dashcoin')->getaddressesbyaccount('usr_doradofees')->get()[0];
        $pxfee = $comm_fee;
        $balance = number_format(getbalance($crypto, $label)/100000000, 8, '.', '');
        $estfee = getestimatefee($crypto);
        $total =  number_format(($cryptoamount+$estfee+$pxfee), 8, '.', '');
        $addressarr = bitcoind()->client('dashcoin')->getaddressesbylabel($label)->get();
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
                getbalance($crypto, $label);
                return $txid;
            }
            else{
                $msg = array('error'=>"Signing Failed. ".$decode);
                return $msg;
            }
        }
        else{
            $msg = array('error'=>"Insufficient fund. You need at least ".$total." ".$crypto." to perform this transaction");
            return $msg;
        }
    }
    elseif ($crypto == 'LTC') {
        $pxfeeaddr = bitcoind()->client('litecoin')->getaddressesbyaccount('usr_doradofees')->get()[0];
        $pxfee = $comm_fee;
        $balance = number_format(getbalance($crypto, $label)/100000000, 8, '.', '');
        $estfee = getestimatefee($crypto);
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
                getbalance($crypto, $label);
                return $txid;
            }
            else{
                $msg = array('error'=>"Signing Failed. ".$decode);
                return $msg;
            }
        }
        else{
            $msg = array('error'=>"Insufficient fund. You need at least ".$total." ".$crypto." to perform this transaction");
            return $msg;
        }
    }
    else {
        $result = null;
        return $result;
    }
}

function sendtomanyaddress($crypto, $sendlabel, $recvaddress, $cryptoamount, $memo, $comm_fee) {
    if ($crypto == 'BTC') {
        $pxfeeaddr = array_keys(bitcoind()->client('bitcoin')->getaddressesbylabel('usr_doradofees')->get())[0];
        $pxfee = $comm_fee;
        $bal = getbalance($crypto, $sendlabel);
        $estfee = getestimatefee($crypto);
        $txcost =  number_format(($cryptoamount+$estfee+$pxfee)*100000000, 0, '.', '');
        if ($bal >= $txcost){
            $txid = bitcoind()->client('bitcoin')->sendmany("",
                array(
                    $recvaddress => $cryptoamount,
                    $pxfeeaddr => $pxfee
                )
            )->get();
            getbalance($crypto, $sendlabel);
            return $txid;
        }
        else{
            $msg = array('error'=>"Insufficient fund. You need at least ".($txcost/'10000000')." ".$crypto." to perform this transaction");
            return $msg;
        }
    }
   elseif ($crypto == 'BCH') {
        $pxfeeaddr = substr(bitcoind()->client('bitabc')->getaddressesbyaccount('usr_doradofees')->get()[0],12);
        $pxfee = $comm_fee;
        $bal = getbalance($crypto, $sendlabel);
        $estfee = getestimatefee($crypto);
        $txcost =  number_format(($cryptoamount+$estfee+$pxfee)*100000000, 0, '.', '');
  
        if ($bal >= $txcost){
            $txid = bitcoind()->client('bitabc')->sendmany("",
                array(
                    $recvaddress => $cryptoamount,
                    $pxfeeaddr => $pxfee
                )
            )->get();
            getbalance($crypto, $sendlabel);
            return $txid;
        }
        else{
            $msg = array('error'=>"Insufficient fund. You need at least ".($txcost/'10000000')." ".$crypto." to perform this transaction");
            return $msg;
        }
    }
   elseif ($crypto == 'DOGE') {
        $pxfeeaddr = bitcoind()->client('dogecoin')->getaddressesbyaccount('usr_doradofees')->get()[0];
        $pxfee = $comm_fee;
        $bal = getbalance($crypto, $sendlabel);
        $estfee = getestimatefee($crypto);
        $txcost =  number_format(($cryptoamount+$estfee+$pxfee)*100000000, 0, '.', '');

        if ($bal >= $txcost){
            $txid = bitcoind()->client('dogecoin')->sendmany("",
                array(
                    $recvaddress => $cryptoamount,
                    $pxfeeaddr => $pxfee
                )
            )->get();
            getbalance($crypto, $sendlabel);
            return $txid;
        }
        else{
            $msg = array('error'=>"Insufficient fund. You need at least ".($txcost/'10000000')." ".$crypto." to perform this transaction");
            return $msg;
        }
    }
    elseif ($crypto == 'DASH') {
        $pxfeeaddr = bitcoind()->client('dashcoin')->getaddressesbyaccount('usr_doradofees')->get()[0];
        $pxfee = $comm_fee;
        $bal = getbalance($crypto, $sendlabel);
        $estfee = getestimatefee($crypto);
        $txcost =  number_format(($cryptoamount+$estfee+$pxfee)*100000000, 0, '.', '');
        if ($bal >= $txcost){
            $txid = bitcoind()->client('dashcoin')->sendmany("",
                array(
                    $recvaddress => $cryptoamount,
                    $pxfeeaddr => $pxfee
                )
            )->get();
            getbalance($crypto, $sendlabel);
            return $txid;
        }
        else{
            $msg = array('error'=>"Insufficient fund. You need at least ".($txcost/'10000000')." ".$crypto." to perform this transaction");
            return $msg;
        }
    }
    elseif ($crypto == 'LTC') {
        $pxfeeaddr = bitcoind()->client('litecoin')->getaddressesbyaccount('usr_doradofees')->get()[0];
        $pxfee = $comm_fee;
        $bal = getbalance($crypto, $sendlabel);
        $estfee = getestimatefee($crypto);
        $txcost =  number_format(($cryptoamount+$estfee+$pxfee)*100000000, 0, '.', '');
        if ($bal >= $txcost){
            $txid = bitcoind()->client('litecoin')->sendmany("",
                array(
                    $recvaddress => $cryptoamount,
                    $pxfeeaddr => $pxfee
                )
            )->get();
            getbalance($crypto, $sendlabel);
            return $txid;
        }
        else{
            $msg = array('error'=>"Insufficient fund. You need at least ".($txcost/'10000000')." ".$crypto." to perform this transaction");
            return $msg;
        }
    }
    else {
        $msg = array("error"=>"Invalid Crypto");
        return $msg;
    }
}


/////////////////////////////////////////////////////////////////////
/// DUMP PRIVATE KEY             ///////////////////////////////////////
////////////////////////////////////////////////////////////////////
function dumpkey($crypto, $label){
    if ($crypto == 'BTC'){
        $crycode = 'bitcoin';
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
    elseif($crypto == 'BCH'){
        $crycode = 'bitabc';
        $addressarr = bitcoind()->client($crycode)->getaddressesbyaccount($label)->get();
        foreach ($addressarr as $addr){
            $priv = bitcoind()->client($crycode)->dumpprivkey($addr)->get();
            $data[] = array(
                "address"=>$addr,
                "key"=>$priv
            );
        }
        return $data;
    }
    elseif($crypto == 'DASH'){
        $crycode = 'dashcoin';
        $addressarr = bitcoind()->client($crycode)->getaddressesbyaccount($label)->get();
        foreach ($addressarr as $addr){
            $priv = bitcoind()->client($crycode)->dumpprivkey($addr)->get();
            $data[] = array(
                "address"=>$addr,
                "key"=>$priv
            );
        }
        return $data;
    }
    elseif($crypto == 'DOGE'){
        $crycode = 'dogecoin';
        $addressarr = bitcoind()->client($crycode)->getaddressesbyaccount($label)->get();
        foreach ($addressarr as $addr){
            $priv = bitcoind()->client($crycode)->dumpprivkey($addr)->get();
            $data[] = array(
                "address"=>$addr,
                "key"=>$priv
            );
        }
        return $data;
    }
    elseif($crypto == 'LTC'){
        $crycode = 'litecoin';
        $addressarr = bitcoind()->client($crycode)->getaddressesbyaccount($label)->get();
        foreach ($addressarr as $addr){
            $priv = bitcoind()->client($crycode)->dumpprivkey($addr)->get();
            $data[] = array(
                "address"=>$addr,
                "key"=>$priv
            );
        }
        return $data;
    }
    else {
        $msg = array("error"=>"Invalid Crypto");
        return $msg;
    }
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
///  WITHDRAWAL WITHOUT OWNER FEES        ///////////////////////////////////////
////////////////////////////////////////////////////////////////////
function withdrawal_admin_crypto($crypto, $sendlabel, $recvaddress, $cryptoamount, $memo) {
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
            else{
                $msg = array('error'=>"Signing Failed. ".$decode);
                return $msg;
            }
        }
        else{
            $msg = array('error'=>"Insufficient fund. You need at least ".$total." ".$crypto." to perform this transaction");
            return $msg;
        }
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
            else{
                $msg = array('error'=>"Signing Failed. ".$decode);
                return $msg;
            }
        }
        else{
            $msg = array('error'=>"Insufficient fund. You need at least ".$total." ".$crypto." to perform this transaction");
            return $msg;
        }
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
            else{
                $msg = array('error'=>"Signing Failed. ".$decode);
                return $msg;
            }
        }
        else{
            $msg = array('error'=>"Insufficient fund. You need at least ".$total." ".$crypto." to perform this transaction");
            return $msg;
        }
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
           else{
                $msg = array('error'=>"Signing Failed. ".$decode);
                return $msg;
            }
        }
        else{
            $msg = array('error'=>"Insufficient fund. You need at least ".$total." ".$crypto." to perform this transaction");
            return $msg;
        }
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
            else{
                $msg = array('error'=>"Signing Failed. ".$decode);
                return $msg;
            }
        }
        else{
            $msg = array('error'=>"Insufficient fund. You need at least ".$total." ".$crypto." to perform this transaction");
            return $msg;
        }
    }
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
    elseif ($crypto == 'LND') {
        $lnrest = new LNDAvtClient();
        $wallet_balance = $lnrest->getWalletBalance();
        return $wallet_balance;
    }
    else {
        $wallet_balance = null;
        return $wallet_balance;
    }
    
}

/////////////////////////////////////////////////////////////////////
///  FUND LIGHTNING WALLET         ///////////////////////////////////////
////////////////////////////////////////////////////////////////////
// function fundlightning001($label, $inv){
//     $txid = sendtoaddressRAW('BTC', $label, $recvaddress, $cryptoamount, $memo, $comm_fee)
//     $lnrest = new LNDAvtClient();
//     $userdet = WalletAddress::where('label', $label)->where('crypto', 'LND')->first();
//     $balance = $userdet->balance;
//     $paymentdet = $lnrest->decodeInvoice($inv);
//     if($balance >= $paymentdet['num_satoshis']){
//         $res = $lnrest->sendPayment($inv);
//         if(array_key_exists("payment_error", $res)){return $res['payment_error'];}
//         if(array_key_exists("error", $res)){return $res['error'];}
//         return $res['payment_hash'];
//     }
//     else{return "error: insuffucient balance";}




// }

/////////////////////////////////////////////////////////////////////
///  REFUND LIGHTNING WALLET         ///////////////////////////////////////
////////////////////////////////////////////////////////////////////
function refundlightning001($label, $sendall, $amount, $recepient){
    $lnrest = new LNDAvtClient();
    $userdet = WalletAddress::where('label', $label)->where('crypto', 'LND')->first();
    $balance = number_format($userdet->balance, 8, '.', '');
    if($balance >= $amount){
        $sendchain = $lnrest->sendOnChain($sendall, $amount, $recepient);
        return $sendchain;
    }
    else{
        $msg = array('error'=>" Insuffucient fund");
        return $msg;
    }
}

/////////////////////////////////////////////////////////////////////
///  DECODE INVOICE LIGHTNING WALLET         ///////////////////////////////////////
////////////////////////////////////////////////////////////////////
function getInvoiceDet($inv){
    $lnrest = new LNDAvtClient();
    $paymentdet = $lnrest->decodeInvoice($inv);
    return $paymentdet;
}

/////////////////////////////////////////////////////////////////////
///  SEND LIGHTNING PAYMENT         ///////////////////////////////////////
////////////////////////////////////////////////////////////////////
function paymentlightning003($label, $inv){
    $lnrest = new LNDAvtClient();
    $userdet = WalletAddress::where('label', $label)->where('crypto', 'LND')->first();
    $balance = $userdet->balance;
    $paymentdet = getInvoiceDet($inv);
    if($balance >= $paymentdet['num_satoshis']){
        $res = $lnrest->sendPayment($inv);
        return $res;
    }
    else{
        $msg = array('error'=>" Insuffucient fund");
        return $msg;
    }
}

/////////////////////////////////////////////////////////////////////
///  RECEIVE LIGHTNING PAYMENT         ///////////////////////////////////////
////////////////////////////////////////////////////////////////////
function receivelightning001($label, $amount, $memo, $expiryRaw){
    if(!$memo){$memo='lightninginv_'.Carbon::now();}
    if(!$expiryRaw){$expiryRaw=1;}
    $userdet = WalletAddress::where('label', $label)->where('crypto', 'BTC')->first();
    $falladdr= $userdet->address;
    $expiry = strval($expiryRaw*3600);
    $lnrest = new LNDAvtClient();
    $invdet = $lnrest->addInvoice($amount, $memo, $expiry, $falladdr);
    return $invdet; 
}

/////////////////////////////////////////////////////////////////////
///  LIST ALL CHANNEL LIGHTNING WALLET         ///////////////////////////////////////
////////////////////////////////////////////////////////////////////
function listchannel($crypto, $label){
    $lnrest = new LNDAvtClient();
    $allchan = $lnrest->getAllChannels();
    $pendchan = $lnrest->getPendingChannels();
    $closedchan = $lnrest->getChanClosed();

    $user = WalletAddress::where('label', $label)->first();
    $transaction = TransLND::where('uid',$user->uid)->where('status','success')->get();

    if(!$transaction){
        $msg = array('error'=>"No Transaction Found for Channel");
        return $msg;
    }

    foreach ($transaction as $trans ) {$trans_txid[] = $trans['txid'];} 

    //active channel match
    foreach ($allchan as $achan ) {
        foreach ($achan as $ach ) {
            $achan_txid[] = explode(":",$ach['channel_point'])[0];
            if(array_intersect($trans_txid,$achan_txid)){$opmatch[] = $ach;}
            else{$opmatch=null;}  
        }
    }
    //dd($opmatch, $achan_txid, $trans_txid);

    // //pending channel match
    foreach ($pendchan as $pchan ) {
        foreach ($pchan as $pch ) {
            $pchan_txid[] = explode(":",$pch['channel']['channel_point'])[0];
            if(array_intersect($trans_txid,$pchan_txid)){$pdmatch[] = $pch;}
            else{$pdmatch=null;} 
        }
    }
    //dd($pdmatch, $pchan_txid, $trans_txid);

    //closed channel match
    foreach ($closedchan as $cchan ) {
        foreach ($cchan as $cch ) {
            $cchan_txid[] = $cch['closing_tx_hash'];
            if(array_intersect($trans_txid,$cchan_txid)){$clmatch[] = $cch;}
            else{$clmatch=null;}
        }
    }
    //dd($clmatch, $cchan_txid, $trans_txid);

    $channel = array(
        'active_channels' => $opmatch, 
        'pending_channels' => $pdmatch, 
        'closed_channels' => $clmatch
    );
    dd($channel);
    return $channel;
}

/////////////////////////////////////////////////////////////////////
///  OPEN LIGHTNING PAYMENT CHANNEL         ///////////////////////////////////////
////////////////////////////////////////////////////////////////////
function openchanlightning001($peers, $localsat, $pushsat){
    $lnrest = new LNDAvtClient();
    $peerspub = explode("@",$peers)[0];
    $balance = $lnrest->getWalletBalance();
    $connpeers = $lnrest->connectPeers($peers);
    $allpeers = $lnrest->getPeers();
    foreach ($allpeers as $peer) {
        foreach ($peer as $p) {
            if($p['pub_key'] == $peerspub){
                $allchan = $lnrest->getAllChannels();
                foreach ($allchan as $chan) {
                    $i = 0;
                    foreach ($chan as $c) {
                        $remotepub[$i] = $c['remote_pubkey'];
                        $i++;
                    }
                }
            }
            else{
                $msg = array('error'=>"Peer not found");
                return $msg;
            }
        }
    }
 
        if(!in_array($peerspub, $remotepub, true)){
            $chantxid = $lnrest->openChannel($peerspub, $localsat, $pushsat);
            return $chantxid;
        }
        else{
            $msg = array('error'=>"Channel already established with this node");
            return $msg;
        }
 
}

/////////////////////////////////////////////////////////////////////
///  CLOSE LIGHTNING PAYMENT CHANNEL         ///////////////////////////////////////
////////////////////////////////////////////////////////////////////
function closechanlightning001($chanpoint){
        $lnrest = new LNDAvtClient();
        $allchan = $lnrest->getAllChannels();
        foreach ($allchan as $chan) {
            $i = 0;
            foreach ($chan as $c) {
                $remotechanpoint[$i] = $c['channel_point'];
                $i++;
            }
        }
        if(in_array($chanpoint, $remotechanpoint, true)){
            $cchantxid = $lnrest->closeChannel($chanpoint, 1);
            if(!empty($cchantxid)) {
                return $cchantxid;
            }
            else{
                $msg = array('error'=>"No Data");
                return $msg;
            } 
        }
        else{
            $msg = array('error'=>"Channel not existed on this node");
            return $msg;
        }
    }

/////////////////////////////////////////////////////////////////////
///  API FUNCTION          ///////////////////////////////////////
////////////////////////////////////////////////////////////////////
function apiToken($session_uid){
    $key=md5('Dorado2019'.$session_uid);
    return hash('sha256', $key);
} 

