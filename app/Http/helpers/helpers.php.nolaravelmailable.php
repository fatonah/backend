<?php
 
use App\Avant\LNDAvtClient;
use App\Setting;
use App\PriceCrypto;
use App\WalletAddress;
use App\User;
use App\Withdrawal;
use App\TransLND;
use App\TransUser;
 
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
/// BLOCKHASH BY BLOCKID /////////////////////////////////////////////////
/////////////////////////////////////////////////////////////
function getblockhash($crypto, $blockid) {
    if ($crypto == 'BTC'){
        $crycode = 'bitcoin';
        $blockhash = bitcoind()->client($crycode)->getblockhash($blockid)->get();
        return $blockhash;
    }
    elseif($crypto == 'BCH'){
        $crycode = 'bitabc';
        $blockhash = bitcoind()->client($crycode)->getblockhash($blockid)->get();
        return $blockhash;
    }
    elseif($crypto == 'DOGE'){
        $crycode = 'dogecoin';
        $blockhash = bitcoind()->client($crycode)->getblockhash($blockid)->get();
        return $blockhash;
    }
    elseif ($crypto == 'LND'){
        $crycode = 'bitcoin';
        $blockhash = bitcoind()->client($crycode)->getblockhash($blockid)->get();
        return $blockhash;
    }
    else {
        $msg = array("error"=>"Invalid Crypto");
        return $msg;
    }

}



///////////////////////////////////////////////////////////////
/// BLOCKTIME BY BLOCKHASH /////////////////////////////////////////////////
/////////////////////////////////////////////////////////////
function getblockdet($crypto, $blockhash) {
    if ($crypto == 'BTC'){
        $crycode = 'bitcoin';
        $blockhash = bitcoind()->client($crycode)->getblock($blockhash)->get();
        return $blockhash;
    }
    elseif($crypto == 'BCH'){
        $crycode = 'bitabc';
        $blockhash = bitcoind()->client($crycode)->getblock($blockhash)->get();
        return $blockhash;
    }
    elseif($crypto == 'DOGE'){
        $crycode = 'dogecoin';
        $blockhash = bitcoind()->client($crycode)->getblock($blockhash)->get();
        return $blockhash;
    }
    elseif ($crypto == 'LND'){
        $crycode = 'bitcoin';
        $blockhash = bitcoind()->client($crycode)->getblock($blockhash)->get();
        return $blockhash;
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
        $balacc[] = bitcoind()->client('bitcoin')->listunspent()->get();
        $amt = null;
        $balance = 0;
        foreach ($balacc as $acc) {
            foreach ($acc as $a) {
                if($a['label'] == $label){$amt[] = number_format($a['amount'], 8, '.', '');}
            }
        }
        if($amt != null) {$balance = array_sum($amt);}
        else {$balance = 0;}

        // $addressarr = array_keys(bitcoind()->client('bitcoin')->getaddressesbylabel($label)->get());
        // $amt = null;
        // foreach ($addressarr as $address) {
        //     $balacc = bitcoind()->client('bitcoin')->listunspent(1, 9999999, [$address])->get();
        //     $balance = 0;
        //     if(in_array('txid', $balacc)){
        //         $amt[] =  (int)number_format($balacc['amount']*100000000, 8, '.', '');
        //         foreach ($amt as $a) {$balance += $a;}
        //     }
        //     else{
        //         foreach ($balacc as $acc) {$amt[] = (int)number_format($acc['amount']*100000000, 8, '.', '');}
        //     }
        // }
        // if($amt != null) {
        //     $wallet_balance = array_sum($amt);
        // }
        // else {
        //     $wallet_balance = 0;
        // }

        $wallet_balance = (int)number_format($balance*100000000, 8, '.', '');
        WalletAddress::where('label', $label)->where('crypto', 'BTC')->update(['balance' => $wallet_balance]);
        return $wallet_balance;
    }
    elseif($crypto == 'BCH'){
        $balacc[] = bitcoind()->client('bitabc')->listunspent()->get();
        $amt = null;
        $balance = 0;
        foreach ($balacc as $acc) {
            foreach ($acc as $a) {
                if($a['label'] == $label){$amt[] = number_format($a['amount'], 8, '.', '');}
            }
        }
        if($amt != null) {$balance = array_sum($amt);}
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
        $balacc[] = bitcoind()->client('dogecoin')->listunspent()->get();
        $amt = null;
        $balance = 0;
        foreach ($balacc as $acc) {
            foreach ($acc as $a) {
                if($a['account'] == $label){$amt[] = number_format($a['amount'], 8, '.', '');}
            }
        }
        if($amt != null) {$balance = array_sum($amt);}
        else {$balance = 0;}

        $wallet_balance = (int)number_format($balance*100000000, 8, '.', '');
        WalletAddress::where('label', $label)->where('crypto', $crypto)->update(['balance' => $wallet_balance]);
        return $wallet_balance;
    }
    elseif($crypto == 'LTC'){
        $balacc[] = bitcoind()->client('litecoin')->listunspent()->get();
        $amt = null;
        $balance = 0;
        foreach ($balacc as $acc) {
            foreach ($acc as $a) {
                if($a['account'] == $label){$amt[] = number_format($a['amount'], 8, '.', '');}
            }
        }
        if($amt != null) {$balance = array_sum($amt);}
        else {$balance = 0;}

        $wallet_balance = (int)number_format($balance*100000000, 8, '.', '');
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
        $transaction = bitcoind()->client($crycode)->listtransactions("*", 10000, 0)->get();
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
        // $lnrest = new LNDAvtClient();
        // $transaction = $lnrest->getPayments();
        // if($transaction){return $transaction;}
        // else{return null;}
        $transaction = TransLND::all(); 
        if($transaction){return $transaction;}
        else{return null;}
    }
    else {
        $msg = array("error"=>"Invalid Crypto");
        return $msg;
    }
}


function listransaction($crypto, $label, $idcurrency, $id_gecko) {
    if ($crypto == 'BTC'){
        $crycode = 'bitcoin';
        $user = WalletAddress::where('label', $label)->first();
        $userid = $user->uid; 
        //GET label transaction
        $transaction = TransUser::where('crypto', $crypto)->where('uid', $userid)->orderBy('txdate','asc')->get();
        $transaction_count = TransUser::where('crypto', $crypto)->where('uid', $userid)->orderBy('txdate','asc')->count();

        if($transaction_count>0){ 
            if(!isset($transaction[0]['time'])){
                $userdetfromuid = WalletAddress::where('uid', $transaction['uid'])->where('crypto', $crypto)->first();

                if(array_key_exists('time',$transaction)){
                    $starT = $transaction['time'] - 10000;
                    $endT = $transaction['time'] + 10000;                
                }else{
                    $starT = $transaction['timereceived'] - 10000;
                    $endT = $transaction['timereceived'] + 10000;
                }

                $json_string = settings('url_gecko').'coins/'.$id_gecko.'/market_chart/range?vs_currency='.$idcurrency.'&from='.$starT.'&to='.$endT;
                $jsondata = file_get_contents($json_string);
                $obj = json_decode($jsondata, TRUE); 
                $priceA = $obj['prices'];
                
                for($i=0;$i<count($priceA);$i++){
                    $price[] = $priceA[$i][0];
                }
                
                foreach ($price as $i) {
                    if(array_key_exists('time',$transaction)){
                    $smallest[$i] = abs($i - $transaction['time']);
                    }else{
                    $smallest[$i] = abs($i - $transaction['timereceived']);    
                    }
                }

                asort($smallest); 
                $ids = array_search(key($smallest),$price);
                $totaldis = disply_convert($crypto,$userdetfromuid->value_display,$transaction['amount']);

                $info = array(
                    'price_lock' => number_format($priceA[$ids][1], 2, '.', ''),
                    'totaldis' => number_format($totaldis, 8, '.', '').' '.$userdetfromuid->value_display,
                    'tran' => $transaction,
                );
            }
            else{
                foreach($transaction as $key => $trans){ 
                    if(array_key_exists('time',$trans)){
                        $starT = $trans['time'] - 10000;
                        $endT = $trans['time'] + 10000;                
                    }
                    else{
                        $starT = $trans['timereceived'] - 10000;
                        $endT = $trans['timereceived'] + 10000;
                    }

                    $json_string = settings('url_gecko').'coins/'.$id_gecko.'/market_chart/range?vs_currency='.$idcurrency.'&from='.$starT.'&to='.$endT;
                    $jsondata = file_get_contents($json_string);
                    $obj = json_decode($jsondata, TRUE); 
                    $priceA = $obj['prices'];

                    for($i=0;$i<count($priceA);$i++){
                        $price[] = $priceA[$i][0];
                    }

                    foreach ($price as $i) {
                        if(array_key_exists('time',$trans)){
                            $smallest[$i] = abs($i - $trans['time']);
                        }
                        else{
                            $smallest[$i] = abs($i - $trans['timereceived']);    
                        }
                    }

                    asort($smallest); 
                    $ids = array_search(key($smallest),$price);

                    $userdetfromuid = WalletAddress::where('uid', $trans['uid'])->where('crypto', $crypto)->first();
                    $initlabel = $userdetfromuid->label;
                
                    $totaldis = disply_convert($crypto,$userdetfromuid->value_display,$trans['amount']);

                    $info[] = array(
                        'price_lock' => number_format($priceA[0][1], 2, '.', ''),
                        'totaldis' => number_format($totaldis, 8, '.', '').' '.$userdetfromuid->value_display,
                        'tran' => array(
                            'account' => $initlabel,
                            'address' =>  $trans['recipient'],
                            'category' =>  $trans['category'],
                            'amount' => floatval($trans['amount']),
                            'label' =>  $trans['recipient_id'],
                            'vout' => 3,
                            'confirmations' =>  intval($trans['confirmation']),
                            'blockhash' => '0000000000000000005fca13b9f9fe8a5763730f15cc41182f0ea4bf90789564',
                            'blockindex' => 89,
                            'blocktime' => 1567400116,
                            'txid' => $trans['txid'],
                            'walletconflicts' => [],
                            'time' =>  intval($trans['time']),
                            'timereceived' =>  intval($trans['timereceived'])
                        ),
                    );
                
                }
            }
            return $info;
        }
        else{return null;}
        // $transaction = bitcoind()->client($crycode)->listtransactions($label)->get(); 
        // if($transaction){
        //     if(!isset($transaction[0]['time'])){
        //         if(array_key_exists('time',$transaction)){
        //             $starT = $transaction['time'] - 10000;
        //             $endT = $transaction['time'] + 10000;                
        //         }else{
        //             $starT = $transaction['timereceived'] - 10000;
        //             $endT = $transaction['timereceived'] + 10000;
        //         }

        //         $json_string = settings('url_gecko').'coins/'.$id_gecko.'/market_chart/range?vs_currency='.$idcurrency.'&from='.$starT.'&to='.$endT;
        //         $jsondata = file_get_contents($json_string);
        //         $obj = json_decode($jsondata, TRUE); 
        //         $priceA = $obj['prices'];
        //         for($i=0;$i<count($priceA);$i++){
        //             $price[] = $priceA[$i][0];
        //         }
        //         foreach ($price as $i) {
        //             if(array_key_exists('time',$transaction)){
        //             $smallest[$i] = abs($i - $transaction['time']);
        //             }else{
        //             $smallest[$i] = abs($i - $transaction['timereceived']);    
        //             }
        //         }
        //         asort($smallest); 
        //         $ids = array_search(key($smallest),$price);
            
        //         $info = array(
        //             'price_lock' => number_format($priceA[$ids][1], 2, '.', ''),
        //             'tran' => $transaction,
        //         );
        //     }else{
        //         foreach($transaction as $trans){
        //         if(array_key_exists('time',$trans)){
        //             $starT = $trans['time'] - 10000;
        //             $endT = $trans['time'] + 10000;                
        //         }else{
        //             $starT = $trans['timereceived'] - 10000;
        //             $endT = $trans['timereceived'] + 10000;
        //         }

        //         $json_string = settings('url_gecko').'coins/'.$id_gecko.'/market_chart/range?vs_currency='.$idcurrency.'&from='.$starT.'&to='.$endT;
        //         $jsondata = file_get_contents($json_string);
        //         $obj = json_decode($jsondata, TRUE); 
        //         $priceA = $obj['prices'];
        //         for($i=0;$i<count($priceA);$i++){
        //             $price[] = $priceA[$i][0];
        //         }
        //         foreach ($price as $i) {
        //             if(array_key_exists('time',$trans)){
        //             $smallest[$i] = abs($i - $trans['time']);
        //             }else{
        //             $smallest[$i] = abs($i - $trans['timereceived']);    
        //             }
        //         }
        //         asort($smallest); 
        //         $ids = array_search(key($smallest),$price);
               
        //         $info[] = array(
        //             'price_lock' => number_format($priceA[$ids][1], 2, '.', ''),
        //             'tran' => $trans,
        //         );
        //         } 
        //     }
        //      //return $transaction;
        //      return $info;
        // }
        // else{return null;} 
    }elseif($crypto == 'BCH'){
        $crycode = 'bitabc';
        //GET label transaction
        $transaction = bitcoind()->client($crycode)->listtransactions($label)->get(); 
       // return $transaction['time'];
        if($transaction){ 
           if(!isset($transaction[0]['time'])){
                if(array_key_exists('time',$transaction)){
                    $starT = $transaction['time'] - 10000;
                    $endT = $transaction['time'] + 10000;                
                }else{
                    $starT = $transaction['timereceived'] - 10000;
                    $endT = $transaction['timereceived'] + 10000;
                }

                $json_string = settings('url_gecko').'coins/'.$id_gecko.'/market_chart/range?vs_currency='.$idcurrency.'&from='.$starT.'&to='.$endT;
                $jsondata = file_get_contents($json_string);
                $obj = json_decode($jsondata, TRUE); 
                $priceA = $obj['prices'];
                for($i=0;$i<count($priceA);$i++){
                    $price[] = $priceA[$i][0];
                }
                foreach ($price as $i) {
                    if(array_key_exists('time',$transaction)){
                    $smallest[$i] = abs($i - $transaction['time']);
                    }else{
                    $smallest[$i] = abs($i - $transaction['timereceived']);    
                    }
                }
                asort($smallest); 
                $ids = array_search(key($smallest),$price);

                $userdetfromuid = WalletAddress::where('label', $label)->where('crypto', $crypto)->first();
                $totaldis = disply_convert($crypto,$userdetfromuid->value_display,$transaction['amount']);
            
                $info = array(
                    'price_lock' => number_format($priceA[$ids][1], 2, '.', ''),
                    'totaldis' => number_format($totaldis, 8, '.', '').' '.$userdetfromuid->value_display,
                    'tran' => $transaction,
                );
            }
            else{
                foreach($transaction as $key => $trans){ 
                if(array_key_exists('time',$trans)){
                    $starT = $trans['time'] - 10000;
                    $endT = $trans['time'] + 10000;                
                }else{
                    $starT = $trans['timereceived'] - 10000;
                    $endT = $trans['timereceived'] + 10000;
                }

                $json_string = settings('url_gecko').'coins/'.$id_gecko.'/market_chart/range?vs_currency='.$idcurrency.'&from='.$starT.'&to='.$endT;
                $jsondata = file_get_contents($json_string);
                $obj = json_decode($jsondata, TRUE); 
                $priceA = $obj['prices'];
                for($i=0;$i<count($priceA);$i++){
                    $price[] = $priceA[$i][0];
                }
                foreach ($price as $i) {
                    if(array_key_exists('time',$trans)){
                    $smallest[$i] = abs($i - $trans['time']);
                    }else{
                    $smallest[$i] = abs($i - $trans['timereceived']);    
                    }
                }
                asort($smallest); 
                $ids = array_search(key($smallest),$price);
                
                $userdetfromuid = WalletAddress::where('label', $label)->where('crypto', $crypto)->first();
                $totaldis = disply_convert($crypto,$userdetfromuid->value_display,$trans['amount']);
            
                $info[] = array(
                    'price_lock' => number_format($priceA[$ids][1], 2, '.', ''),
                    'totaldis' => number_format($totaldis, 8, '.', '').' '.$userdetfromuid->value_display,
                    'tran' => $trans,
                );
                
                }
            }
            //dd($ids, $smallest, $priceA);
            //return $transaction;
            return $info;
        }
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
        if($transaction){
            if(!isset($transaction[0]['time'])){
                if(array_key_exists('time',$transaction)){
                    $starT = $transaction['time'] - 10000;
                    $endT = $transaction['time'] + 10000;                
                }else{
                    $starT = $transaction['timereceived'] - 10000;
                    $endT = $transaction['timereceived'] + 10000;
                }

                $json_string = settings('url_gecko').'coins/'.$id_gecko.'/market_chart/range?vs_currency='.$idcurrency.'&from='.$starT.'&to='.$endT;
                $jsondata = file_get_contents($json_string);
                $obj = json_decode($jsondata, TRUE); 
                $priceA = $obj['prices'];
                for($i=0;$i<count($priceA);$i++){
                    $price[] = $priceA[$i][0];
                }
                foreach ($price as $i) {
                    if(array_key_exists('time',$transaction)){
                    $smallest[$i] = abs($i - $transaction['time']);
                    }else{
                    $smallest[$i] = abs($i - $transaction['timereceived']);    
                    }
                }
                asort($smallest); 
                $ids = array_search(key($smallest),$price);

                $userdetfromuid = WalletAddress::where('label', $label)->where('crypto', $crypto)->first();
                $totaldis = disply_convert($crypto,$userdetfromuid->value_display,$transaction['amount']);
            
                $info = array(
                    'price_lock' => number_format($priceA[$ids][1], 2, '.', ''),
                    'totaldis' => number_format($totaldis, 8, '.', '').' '.$userdetfromuid->value_display,
                    'tran' => $transaction,
                );
            }else{
                foreach($transaction as $trans){
                    if(array_key_exists('time',$trans)){
                        $starT = $trans['time'] - 10000;
                        $endT = $trans['time'] + 10000;                
                    }else{
                        $starT = $trans['timereceived'] - 10000;
                        $endT = $trans['timereceived'] + 10000;
                    }

                    $json_string = settings('url_gecko').'coins/'.$id_gecko.'/market_chart/range?vs_currency='.$idcurrency.'&from='.$starT.'&to='.$endT;
                    $jsondata = file_get_contents($json_string);
                    $obj = json_decode($jsondata, TRUE); 
                    $priceA = $obj['prices'];
                    for($i=0;$i<count($priceA);$i++){
                        $price[] = $priceA[$i][0];
                    }
                    foreach ($price as $i) {
                        if(array_key_exists('time',$trans)){
                        $smallest[$i] = abs($i - $trans['time']);
                        }else{
                        $smallest[$i] = abs($i - $trans['timereceived']);    
                        }
                    }
                    asort($smallest); 
                    $ids = array_search(key($smallest),$price);

                    $userdetfromuid = WalletAddress::where('label', $label)->where('crypto', $crypto)->first();
                    $totaldis = disply_convert($crypto,$userdetfromuid->value_display,$trans['amount']);
                
                    $info[] = array(
                        'price_lock' => number_format($priceA[$ids][1], 2, '.', ''),
                        'totaldis' => number_format($totaldis, 8, '.', '').' '.$userdetfromuid->value_display,
                        'tran' => $trans,
                    );
                }
            }
             //return $transaction;
             return $info;
        }
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
        $pxfeeaddr = WalletAddress::where('crypto', $crypto)->where('label', 'usr_doradofees')->first()->address;
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
        if(array_keys(bitcoind()->client('bitcoin')->getaddressesbylabel($label)->get())[1]){
            $changeaddr = array_keys(bitcoind()->client('bitcoin')->getaddressesbylabel($label)->get())[1];
        }
        else{
            $changeaddr = bitcoind()->client('bitcoin')->getnewaddress($label)->get();
        }
        //dd("Fee: ".$estfee, "Cost: ".$total, "Input: ".$totalin, "Change: ".$change, "Before Balance: ".$balance);
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
            //dd("Fee: ".$estfee, "Cost: ".$total, "Input: ".$totalin, "Change: ".$change, "Before Balance: ".$balance, $decode, $createraw, $signing);
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
        $pxfeeaddr = WalletAddress::where('crypto', $crypto)->where('label', 'usr_doradofees')->first()->address;;
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
        if(substr(bitcoind()->client('bitabc')->getaddressesbyaccount($label)->get()[1],12)){
            $changeaddr = substr(bitcoind()->client('bitabc')->getaddressesbyaccount($label)->get()[1],12);
        }
        else{
            $changeaddr = substr(bitcoind()->client('bitabc')->getnewaddress($label)->get(),12);
        }
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
        $pxfeeaddr = WalletAddress::where('crypto', $crypto)->where('label', 'usr_doradofees')->first()->address;;
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
        if(bitcoind()->client('dogecoin')->getaddressesbyaccount($label)->get()[1]){
            $changeaddr = bitcoind()->client('dogecoin')->getaddressesbyaccount($label)->get()[1];
        }
        else{
            $changeaddr = bitcoind()->client('dogecoin')->getnewaddress($label)->get();
        }
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
        $pxfeeaddr = WalletAddress::where('crypto', $crypto)->where('label', 'usr_doradofees')->first()->address;;
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
        if(bitcoind()->client('dashcoin')->getaddressesbyaccount($label)->get()[1]){
            $changeaddr = bitcoind()->client('dashcoin')->getaddressesbyaccount($label)->get()[1];
        }
        else{
            $changeaddr = bitcoind()->client('dashcoin')->getnewaddress($label)->get();
        }
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
        $pxfeeaddr = WalletAddress::where('crypto', $crypto)->where('label', 'usr_doradofees')->first()->address;;
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
        if(bitcoind()->client('litecoin')->getaddressesbyaccount($label)->get()[1]){
            $changeaddr = bitcoind()->client('litecoin')->getaddressesbyaccount($label)->get()[1];
        }
        else{
            $changeaddr = bitcoind()->client('litecoin')->getnewaddress($label)->get();
        }
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
////////////////////////////////////////////////////////////////////
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
        dd($lnrest->getinfo());
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
function fundlightning001($crypto, $label, $recvaddress, $cryptoamount, $memo, $comm_fee){ 
    $crypto == 'BTC';
    $pxfeeaddr = WalletAddress::where('crypto', $crypto)->where('label', 'usr_doradofees')->first()->address;
    $pxfee = $comm_fee;
    $balance = number_format(getbalance($crypto, $label)/100000000, 8, '.', '');
    $estfee = getestimatefee($crypto);
    $total =  number_format(($cryptoamount/100000000)+$estfee+$pxfee, 8, '.', '');
    
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
    $changeaddr = WalletAddress::where('crypto', $crypto)->where('label', $label)->first()->address;

    if($balance >= $total){   
        $createraw = bitcoind()->client('bitcoin')->createrawtransaction(
            $txin,
            array(
                $recvaddress=>number_format($cryptoamount/100000000, 8, '.', ''),
                $changeaddr=>$change,
                $pxfeeaddr => $pxfee
            )
        )->get();  
        $signing = bitcoind()->client('bitcoin')->signrawtransactionwithwallet($createraw)->get();
        $decode = bitcoind()->client('bitcoin')->decoderawtransaction($signing['hex'])->get();
        //dd("Fee: ".$estfee, "Cost: ".$total, "Input: ".$totalin, "Change: ".$change, "Before Balance: ".$balance, $decode, $createraw, $signing);
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

    // $userdet = WalletAddress::where('label', $label)->where('crypto', 'LND')->first();
    // $recvaddress = $userdet->address;
    // $balance = getbalance('BTC', $label);
    // $cryptoamount = number_format($amount/100000000, 8, '.', '');
    // dd( 
    //     $recipient,
    //     $balance,
    //     $cryptoamount,
    //     $amount,
    //     $balance >= $cryptoamount,
    //     $remarks,
    //     $comm_fee
    // );
    
    // if($balance >= $amount){$txid = sendtoaddressRAW('BTC', $label, $recipient, $cryptoamount, $remarks, $comm_fee);}
    // else{return "error: insuffucient balance";}
}

/////////////////////////////////////////////////////////////////////
///  REFUND LIGHTNING WALLET         ///////////////////////////////////////
////////////////////////////////////////////////////////////////////
function refundlightning001($label, $amount, $recepient){
    $lnrest = new LNDAvtClient();
    $userdet = WalletAddress::where('label', $label)->where('crypto', 'LND')->first();
    $balance = number_format($userdet->balance, 8, '.', '');
    $fee = number_format(getestimatefee('LND')*100000000, 8, '.', '');
    $totalfunds = number_format($amount + $fee, 8, '.', '');
    $sendall = false;

    if($balance >= $totalfunds){
        $sendchain = $lnrest->sendOnChain($sendall, $amount, $recepient);
        return $sendchain;
    }
    else{
        $msg = array('error'=>"Insuffucient fund");
        return $msg;
    }
}

/////////////////////////////////////////////////////////////////////
///  LIST PAYMENTS LIGHTNING WALLET         ///////////////////////////////////////
////////////////////////////////////////////////////////////////////
function getLightningPayment(){
    $lnrest = new LNDAvtClient();
    $paymentdet = $lnrest->getPayments();
    return $paymentdet;
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
///  DECODE TXID LIGHTNING WALLET         ///////////////////////////////////////
////////////////////////////////////////////////////////////////////
function getLightningTXDet($txid){
    $lnrest = new LNDAvtClient();
    $alltx = $lnrest->getTransactions();;
    foreach ($alltx as $tx) {
        foreach ($tx as $t) {
            if($t['tx_hash'] == $txid){return $t;}
        }
    }
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
        $msg = array('error'=>"Insuffucient fund");
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
    //$pendchan = $lnrest->getPendingChannels();
    $closedchan = $lnrest->getChanClosed();

    $user = WalletAddress::where('label', $label)->first();
    $transaction = TransLND::where('uid',$user->uid)->where('status','success')->get();
    $match = array();
    if(!$transaction){
        $msg = array('error'=>"No Transaction Found for Channel");
        return $msg;
    }
    foreach ($transaction as $trans ) {$trans_txid[] = $trans['txid'];} 
    //active channel match
    foreach ($allchan as $achan ) {
        foreach ($achan as $ach ) {
            $achan_txid[] = explode(":",$ach['channel_point'])[0];
            if(array_intersect($trans_txid,$achan_txid)){$match[] = $ach;}
        }
    }
    
    //pending channel match
    // foreach ($pendchan as $pchan ) {
    //     foreach ($pchan as $pch ) {
    //         // $pchan_txid[] = explode(":",$pch['channel_point'])[0];
    //         // if(array_intersect($trans_txid,$pchan_txid)){$match[] = $pch;}
    //         // else{$match=null;} 
    //     }
    // }
   
    //closed channel match
    foreach ($closedchan as $cchan ) {
        foreach ($cchan as $cch ) {
            $cchan_txid[] = $cch['closing_tx_hash'];
            if(array_intersect($trans_txid,$cchan_txid)){$match[] = $cch;}
            //else{$match=null;}
        }
    }
    return $match;
}

/////////////////////////////////////////////////////////////////////
///  LIST ALL CHANNEL LIGHTNING WALLET         ///////////////////////////////////////
////////////////////////////////////////////////////////////////////
function listClosedChannel(){
    $lnrest = new LNDAvtClient();
    $closedchan = $lnrest->getChanClosed();
    return $closedchan;
}

/////////////////////////////////////////////////////////////////////
///  OPEN LIGHTNING PAYMENT CHANNEL         ///////////////////////////////////////
////////////////////////////////////////////////////////////////////
function openchanlightning001($peers, $localsat, $pushsat){
    $lnrest = new LNDAvtClient();
    $peerspub = explode("@",$peers)[0];
    $balance = $lnrest->getWalletBalance();
    $connpeers = $lnrest->connectPeers($peers);
    //$allpeers = $lnrest->getPeers();

    // foreach ($allpeers as $peer) {
    //     foreach ($peer as $p) {
    //         if($p['pub_key'] == $peerspub){
                $allchan = $lnrest->getAllChannels();
                foreach ($allchan as $chan) {
                    $i = 0;
                    foreach ($chan as $c) {
                        $remotepub[$i] = $c['remote_pubkey'];
                        $i++;
                    }
                }
    //         }
    //         // else{
    //         //     $msg = array('error'=>"Peer not found");
    //         //     return $msg;
    //         // }
    //     }
    // }
    //dd($remotepub, $c['remote_pubkey'], $c, $chan, $allchan);
 
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
        $cchantxid = $lnrest->closeChannel($chanpoint);
        return $cchantxid; 
    }
    else{
        $msg = array('error'=>"Channel not existed on this node");
        return $msg;
    }
}

/////////////////////////////////////////////////////////////////////
///  GETBALANCE LIGHTNING                      ///////////////////////////////////////
////////////////////////////////////////////////////////////////////
function getbalance_lndbtc($label) {
    $user = WalletAddress::where('label', $label)->where('crypto', 'LND')->first();
    $transactionbtc = TransLND::where('uid',$user->uid)->latest()->first();
    if($transactionbtc->count() == 0) {$lndbtc_bal = 0.00000000;}
    else{$lndbtc_bal = $transactionbtc->after_bal;}
    
    $upbal = WalletAddress::where('label', $label)->where('crypto', 'LND')->update([
        'balance' => $lndbtc_bal
    ]);
    return $lndbtc_bal;
}


function getbalance_lndlnd($label) {
    $user = WalletAddress::where('label', $label)->where('crypto', 'LND')->first();
    $lnrest = new LNDAvtClient();
    $allchan = $lnrest->getAllChannels();
    $transaction = TransLND::where('uid',$user->uid)->where('category','open')->where('status','success')->get();
    if($transaction->count() == 0){$lndlnd_bal = 0.00000000;}
    else{
        foreach ($transaction as $trans ) {$trans_txid[] = $trans['txid'];} 
        foreach ($allchan as $achan ) {
            foreach ($achan as $ach ) {
                $achan_txid[] = explode(":",$ach['channel_point'])[0];
                if(array_intersect($trans_txid,$achan_txid)){$match[] = $ach;}
                else{$match=null;}  
            }
        }
        $lndlnd_bal = 0;
        foreach ($match as $m ) {
            $lndlnd_bal += number_format($m['local_balance'], 8, '.', '');
        }
    }
        
    $upbal = WalletAddress::where('label', $label)->where('crypto', 'LND')->update([
        'lightning_balance' => $lndlnd_bal
    ]);
    return number_format($lndlnd_bal, 8, '.', '');
}


/////////////////////////////////////////////////////////////////////
///  GET ON CHAIN TXN         ///////////////////////////////////////
////////////////////////////////////////////////////////////////////
function onchain_trans(){
    $lnrest = new LNDAvtClient();
    $transaction = $lnrest->getTransactions();
    if(!$transaction){return null; }
    else{return $transaction;}
}

/////////////////////////////////////////////////////////////////////
///  API FUNCTION          ///////////////////////////////////////
////////////////////////////////////////////////////////////////////
function apiToken($session_uid){
    $key=md5('Dorado2019'.$session_uid);
    return hash('sha256', $key);
}

/////////////////////////////////////////////////////////////////////
///  MNEMONIC FUNCTION          ///////////////////////////////////////
////////////////////////////////////////////////////////////////////
function genseed($session_uid){
    $entr = apiToken($session_uid);
    $entr_arr = str_split($entr);
    $hexarr = array(
        '0' => '0',
        '1' => '1',
        '2' => '2',
        '3' => '3',
        '4' => '4',
        '5' => '5',
        '6' => '6',
        '7' => '7',
        '8' => '8',
        '9' => '9',
        '10' => 'a',
        '11' => 'b',
        '12' => 'c',
        '13' => 'd',
        '14' => 'e',
        '15' => 'f',
    );
    $wordlist = array("abandon","ability","able","about","above","absent","absorb","abstract","absurd","abuse","access","accident","account","accuse","achieve","acid","acoustic","acquire","across","act","action","actor","actress","actual","adapt","add","addict","address","adjust","admit","adult","advance","advice","aerobic","affair","afford","afraid","again","age","agent","agree","ahead","aim","air","airport","aisle","alarm","album","alcohol","alert","alien","all","alley","allow","almost","alone","alpha","already","also","alter","always","amateur","amazing","among","amount","amused","analyst","anchor","ancient","anger","angle","angry","animal","ankle","announce","annual","another","answer","antenna","antique","anxiety","any","apart","apology","appear","apple","approve","april","arch","arctic","area","arena","argue","arm","armed","armor","army","around","arrange","arrest","arrive","arrow","art","artefact","artist","artwork","ask","aspect","assault","asset","assist","assume","asthma","athlete","atom","attack","attend","attitude","attract","auction","audit","august","aunt","author","auto","autumn","average","avocado","avoid","awake","aware","away","awesome","awful","awkward","axis","baby","bachelor","bacon","badge","bag","balance","balcony","ball","bamboo","banana","banner","bar","barely","bargain","barrel","base","basic","basket","battle","beach","bean","beauty","because","become","beef","before","begin","behave","behind","believe","below","belt","bench","benefit","best","betray","better","between","beyond","bicycle","bid","bike","bind","biology","bird","birth","bitter","black","blade","blame","blanket","blast","bleak","bless","blind","blood","blossom","blouse","blue","blur","blush","board","boat","body","boil","bomb","bone","bonus","book","boost","border","boring","borrow","boss","bottom","bounce","box","boy","bracket","brain","brand","brass","brave","bread","breeze","brick","bridge","brief","bright","bring","brisk","broccoli","broken","bronze","broom","brother","brown","brush","bubble","buddy","budget","buffalo","build","bulb","bulk","bullet","bundle","bunker","burden","burger","burst","bus","business","busy","butter","buyer","buzz","cabbage","cabin","cable","cactus","cage","cake","call","calm","camera","camp","can","canal","cancel","candy","cannon","canoe","canvas","canyon","capable","capital","captain","car","carbon","card","cargo","carpet","carry","cart","case","cash","casino","castle","casual","cat","catalog","catch","category","cattle","caught","cause","caution","cave","ceiling","celery","cement","census","century","cereal","certain","chair","chalk","champion","change","chaos","chapter","charge","chase","chat","cheap","check","cheese","chef","cherry","chest","chicken","chief","child","chimney","choice","choose","chronic","chuckle","chunk","churn","cigar","cinnamon","circle","citizen","city","civil","claim","clap","clarify","claw","clay","clean","clerk","clever","click","client","cliff","climb","clinic","clip","clock","clog","close","cloth","cloud","clown","club","clump","cluster","clutch","coach","coast","coconut","code","coffee","coil","coin","collect","color","column","combine","come","comfort","comic","common","company","concert","conduct","confirm","congress","connect","consider","control","convince","cook","cool","copper","copy","coral","core","corn","correct","cost","cotton","couch","country","couple","course","cousin","cover","coyote","crack","cradle","craft","cram","crane","crash","crater","crawl","crazy","cream","credit","creek","crew","cricket","crime","crisp","critic","crop","cross","crouch","crowd","crucial","cruel","cruise","crumble","crunch","crush","cry","crystal","cube","culture","cup","cupboard","curious","current","curtain","curve","cushion","custom","cute","cycle","dad","damage","damp","dance","danger","daring","dash","daughter","dawn","day","deal","debate","debris","decade","december","decide","decline","decorate","decrease","deer","defense","define","defy","degree","delay","deliver","demand","demise","denial","dentist","deny","depart","depend","deposit","depth","deputy","derive","describe","desert","design","desk","despair","destroy","detail","detect","develop","device","devote","diagram","dial","diamond","diary","dice","diesel","diet","differ","digital","dignity","dilemma","dinner","dinosaur","direct","dirt","disagree","discover","disease","dish","dismiss","disorder","display","distance","divert","divide","divorce","dizzy","doctor","document","dog","doll","dolphin","domain","donate","donkey","donor","door","dose","double","dove","draft","dragon","drama","drastic","draw","dream","dress","drift","drill","drink","drip","drive","drop","drum","dry","duck","dumb","dune","during","dust","dutch","duty","dwarf","dynamic","eager","eagle","early","earn","earth","easily","east","easy","echo","ecology","economy","edge","edit","educate","effort","egg","eight","either","elbow","elder","electric","elegant","element","elephant","elevator","elite","else","embark","embody","embrace","emerge","emotion","employ","empower","empty","enable","enact","end","endless","endorse","enemy","energy","enforce","engage","engine","enhance","enjoy","enlist","enough","enrich","enroll","ensure","enter","entire","entry","envelope","episode","equal","equip","era","erase","erode","erosion","error","erupt","escape","essay","essence","estate","eternal","ethics","evidence","evil","evoke","evolve","exact","example","excess","exchange","excite","exclude","excuse","execute","exercise","exhaust","exhibit","exile","exist","exit","exotic","expand","expect","expire","explain","expose","express","extend","extra","eye","eyebrow","fabric","face","faculty","fade","faint","faith","fall","false","fame","family","famous","fan","fancy","fantasy","farm","fashion","fat","fatal","father","fatigue","fault","favorite","feature","february","federal","fee","feed","feel","female","fence","festival","fetch","fever","few","fiber","fiction","field","figure","file","film","filter","final","find","fine","finger","finish","fire","firm","first","fiscal","fish","fit","fitness","fix","flag","flame","flash","flat","flavor","flee","flight","flip","float","flock","floor","flower","fluid","flush","fly","foam","focus","fog","foil","fold","follow","food","foot","force","forest","forget","fork","fortune","forum","forward","fossil","foster","found","fox","fragile","frame","frequent","fresh","friend","fringe","frog","front","frost","frown","frozen","fruit","fuel","fun","funny","furnace","fury","future","gadget","gain","galaxy","gallery","game","gap","garage","garbage","garden","garlic","garment","gas","gasp","gate","gather","gauge","gaze","general","genius","genre","gentle","genuine","gesture","ghost","giant","gift","giggle","ginger","giraffe","girl","give","glad","glance","glare","glass","glide","glimpse","globe","gloom","glory","glove","glow","glue","goat","goddess","gold","good","goose","gorilla","gospel","gossip","govern","gown","grab","grace","grain","grant","grape","grass","gravity","great","green","grid","grief","grit","grocery","group","grow","grunt","guard","guess","guide","guilt","guitar","gun","gym","habit","hair","half","hammer","hamster","hand","happy","harbor","hard","harsh","harvest","hat","have","hawk","hazard","head","health","heart","heavy","hedgehog","height","hello","helmet","help","hen","hero","hidden","high","hill","hint","hip","hire","history","hobby","hockey","hold","hole","holiday","hollow","home","honey","hood","hope","horn","horror","horse","hospital","host","hotel","hour","hover","hub","huge","human","humble","humor","hundred","hungry","hunt","hurdle","hurry","hurt","husband","hybrid","ice","icon","idea","identify","idle","ignore","ill","illegal","illness","image","imitate","immense","immune","impact","impose","improve","impulse","inch","include","income","increase","index","indicate","indoor","industry","infant","inflict","inform","inhale","inherit","initial","inject","injury","inmate","inner","innocent","input","inquiry","insane","insect","inside","inspire","install","intact","interest","into","invest","invite","involve","iron","island","isolate","issue","item","ivory","jacket","jaguar","jar","jazz","jealous","jeans","jelly","jewel","job","join","joke","journey","joy","judge","juice","jump","jungle","junior","junk","just","kangaroo","keen","keep","ketchup","key","kick","kid","kidney","kind","kingdom","kiss","kit","kitchen","kite","kitten","kiwi","knee","knife","knock","know","lab","label","labor","ladder","lady","lake","lamp","language","laptop","large","later","latin","laugh","laundry","lava","law","lawn","lawsuit","layer","lazy","leader","leaf","learn","leave","lecture","left","leg","legal","legend","leisure","lemon","lend","length","lens","leopard","lesson","letter","level","liar","liberty","library","license","life","lift","light","like","limb","limit","link","lion","liquid","list","little","live","lizard","load","loan","lobster","local","lock","logic","lonely","long","loop","lottery","loud","lounge","love","loyal","lucky","luggage","lumber","lunar","lunch","luxury","lyrics","machine","mad","magic","magnet","maid","mail","main","major","make","mammal","man","manage","mandate","mango","mansion","manual","maple","marble","march","margin","marine","market","marriage","mask","mass","master","match","material","math","matrix","matter","maximum","maze","meadow","mean","measure","meat","mechanic","medal","media","melody","melt","member","memory","mention","menu","mercy","merge","merit","merry","mesh","message","metal","method","middle","midnight","milk","million","mimic","mind","minimum","minor","minute","miracle","mirror","misery","miss","mistake","mix","mixed","mixture","mobile","model","modify","mom","moment","monitor","monkey","monster","month","moon","moral","more","morning","mosquito","mother","motion","motor","mountain","mouse","move","movie","much","muffin","mule","multiply","muscle","museum","mushroom","music","must","mutual","myself","mystery","myth","naive","name","napkin","narrow","nasty","nation","nature","near","neck","need","negative","neglect","neither","nephew","nerve","nest","net","network","neutral","never","news","next","nice","night","noble","noise","nominee","noodle","normal","north","nose","notable","note","nothing","notice","novel","now","nuclear","number","nurse","nut","oak","obey","object","oblige","obscure","observe","obtain","obvious","occur","ocean","october","odor","off","offer","office","often","oil","okay","old","olive","olympic","omit","once","one","onion","online","only","open","opera","opinion","oppose","option","orange","orbit","orchard","order","ordinary","organ","orient","original","orphan","ostrich","other","outdoor","outer","output","outside","oval","oven","over","own","owner","oxygen","oyster","ozone","pact","paddle","page","pair","palace","palm","panda","panel","panic","panther","paper","parade","parent","park","parrot","party","pass","patch","path","patient","patrol","pattern","pause","pave","payment","peace","peanut","pear","peasant","pelican","pen","penalty","pencil","people","pepper","perfect","permit","person","pet","phone","photo","phrase","physical","piano","picnic","picture","piece","pig","pigeon","pill","pilot","pink","pioneer","pipe","pistol","pitch","pizza","place","planet","plastic","plate","play","please","pledge","pluck","plug","plunge","poem","poet","point","polar","pole","police","pond","pony","pool","popular","portion","position","possible","post","potato","pottery","poverty","powder","power","practice","praise","predict","prefer","prepare","present","pretty","prevent","price","pride","primary","print","priority","prison","private","prize","problem","process","produce","profit","program","project","promote","proof","property","prosper","protect","proud","provide","public","pudding","pull","pulp","pulse","pumpkin","punch","pupil","puppy","purchase","purity","purpose","purse","push","put","puzzle","pyramid","quality","quantum","quarter","question","quick","quit","quiz","quote","rabbit","raccoon","race","rack","radar","radio","rail","rain","raise","rally","ramp","ranch","random","range","rapid","rare","rate","rather","raven","raw","razor","ready","real","reason","rebel","rebuild","recall","receive","recipe","record","recycle","reduce","reflect","reform","refuse","region","regret","regular","reject","relax","release","relief","rely","remain","remember","remind","remove","render","renew","rent","reopen","repair","repeat","replace","report","require","rescue","resemble","resist","resource","response","result","retire","retreat","return","reunion","reveal","review","reward","rhythm","rib","ribbon","rice","rich","ride","ridge","rifle","right","rigid","ring","riot","ripple","risk","ritual","rival","river","road","roast","robot","robust","rocket","romance","roof","rookie","room","rose","rotate","rough","round","route","royal","rubber","rude","rug","rule","run","runway","rural","sad","saddle","sadness","safe","sail","salad","salmon","salon","salt","salute","same","sample","sand","satisfy","satoshi","sauce","sausage","save","say","scale","scan","scare","scatter","scene","scheme","school","science","scissors","scorpion","scout","scrap","screen","script","scrub","sea","search","season","seat","second","secret","section","security","seed","seek","segment","select","sell","seminar","senior","sense","sentence","series","service","session","settle","setup","seven","shadow","shaft","shallow","share","shed","shell","sheriff","shield","shift","shine","ship","shiver","shock","shoe","shoot","shop","short","shoulder","shove","shrimp","shrug","shuffle","shy","sibling","sick","side","siege","sight","sign","silent","silk","silly","silver","similar","simple","since","sing","siren","sister","situate","six","size","skate","sketch","ski","skill","skin","skirt","skull","slab","slam","sleep","slender","slice","slide","slight","slim","slogan","slot","slow","slush","small","smart","smile","smoke","smooth","snack","snake","snap","sniff","snow","soap","soccer","social","sock","soda","soft","solar","soldier","solid","solution","solve","someone","song","soon","sorry","sort","soul","sound","soup","source","south","space","spare","spatial","spawn","speak","special","speed","spell","spend","sphere","spice","spider","spike","spin","spirit","split","spoil","sponsor","spoon","sport","spot","spray","spread","spring","spy","square","squeeze","squirrel","stable","stadium","staff","stage","stairs","stamp","stand","start","state","stay","steak","steel","stem","step","stereo","stick","still","sting","stock","stomach","stone","stool","story","stove","strategy","street","strike","strong","struggle","student","stuff","stumble","style","subject","submit","subway","success","such","sudden","suffer","sugar","suggest","suit","summer","sun","sunny","sunset","super","supply","supreme","sure","surface","surge","surprise","surround","survey","suspect","sustain","swallow","swamp","swap","swarm","swear","sweet","swift","swim","swing","switch","sword","symbol","symptom","syrup","system","table","tackle","tag","tail","talent","talk","tank","tape","target","task","taste","tattoo","taxi","teach","team","tell","ten","tenant","tennis","tent","term","test","text","thank","that","theme","then","theory","there","they","thing","this","thought","three","thrive","throw","thumb","thunder","ticket","tide","tiger","tilt","timber","time","tiny","tip","tired","tissue","title","toast","tobacco","today","toddler","toe","together","toilet","token","tomato","tomorrow","tone","tongue","tonight","tool","tooth","top","topic","topple","torch","tornado","tortoise","toss","total","tourist","toward","tower","town","toy","track","trade","traffic","tragic","train","transfer","trap","trash","travel","tray","treat","tree","trend","trial","tribe","trick","trigger","trim","trip","trophy","trouble","truck","true","truly","trumpet","trust","truth","try","tube","tuition","tumble","tuna","tunnel","turkey","turn","turtle","twelve","twenty","twice","twin","twist","two","type","typical","ugly","umbrella","unable","unaware","uncle","uncover","under","undo","unfair","unfold","unhappy","uniform","unique","unit","universe","unknown","unlock","until","unusual","unveil","update","upgrade","uphold","upon","upper","upset","urban","urge","usage","use","used","useful","useless","usual","utility","vacant","vacuum","vague","valid","valley","valve","van","vanish","vapor","various","vast","vault","vehicle","velvet","vendor","venture","venue","verb","verify","version","very","vessel","veteran","viable","vibrant","vicious","victory","video","view","village","vintage","violin","virtual","virus","visa","visit","visual","vital","vivid","vocal","voice","void","volcano","volume","vote","voyage","wage","wagon","wait","walk","wall","walnut","want","warfare","warm","warrior","wash","wasp","waste","water","wave","way","wealth","weapon","wear","weasel","weather","web","wedding","weekend","weird","welcome","west","wet","whale","what","wheat","wheel","when","where","whip","whisper","wide","width","wife","wild","will","win","window","wine","wing","wink","winner","winter","wire","wisdom","wise","wish","witness","wolf","woman","wonder","wood","wool","word","work","world","worry","worth","wrap","wreck","wrestle","wrist","write","wrong","yard","year","yellow","you","young","youth","zebra","zero","zone","zoo");

    $binval = null;
    foreach ($entr_arr as $earr) {$binarr[] = str_pad(decbin(array_search($earr, $hexarr)), 4, 0, STR_PAD_LEFT);}
    $binval = implode('', $binarr);
    $crccheck = str_pad(decbin(strlen($entr)/32), 4, 0, STR_PAD_LEFT);
    $crc = $binval.$crccheck;
    $splitbin = str_split($crc, 11);
    $mnemonic = '';
    foreach ($splitbin as $split) {
        if($mnemonic == ''){
            $mnemonic = $wordlist[bindec($split)];
        }else{
            $mnemonic = $mnemonic.' '.$wordlist[bindec($split)];
        }
        //$mnemonic[] = $wordlist[bindec($split)];
    }  
    return  $mnemonic;
}  

/////////////////////////////////////////////////////////////////////
///  CONVERT DISPLAY         ///////////////////////////////////////
//////////////////////////////////////////////////////////////////// 
	function disply_convert($fromconv,$toconv,$nilai){   
		if($fromconv=='BTC'){
			if($toconv=='Bits'){ 
                $total = $nilai * 1000000; 
			}
			else if($toconv=='mBTC'){
				$total = $nilai * 1000;
			}
			else if($toconv=='SAT'){
				$total = $nilai * 100000000;
			}
			else{
				$total = $nilai;
			}
		}  
		else if($fromconv=='mBTC'){
			if($toconv=='Bits'){
				$total = $nilai * 1000;
			}
			else if($toconv=='BTC'){
				$total = $nilai * 0.001;
			}
			else if($toconv=='SAT'){
				$total = $nilai * 100000;
			}
			else{
				$total = $nilai;
			}
		}  
		else if($fromconv=='Bits'){
			if($toconv=='BTC'){
				$total = $nilai * 0.000001;
			}
			else if($toconv=='mBTC'){
				$total = $nilai * 0.001;
			}
			else if($toconv=='SAT'){
				$total = $nilai * 100;
			}
			else{
				$total = $nilai;
			}
		}
		else if($fromconv=='SAT'){
			if($toconv=='Bits'){
				$total = $nilai * 0.01;
			}
			else if($toconv=='mBTC'){
				$total = $nilai * 0.00001;
			}
			else if($toconv=='BTC'){
				$total = $nilai * 0.00000001;
			}
			else{
				$total = $nilai;
			}
		}
		else{
			if($toconv=='mDOGE' || $toconv=='mDASH' || $toconv=='mLTC' || $toconv=='mBCH'){
				$total = $nilai * 1000;
            }
            else if($fromconv=='mDOGE' || $fromconv=='mDASH' || $fromconv=='mLTC' || $fromconv=='mBCH'){
				$total = $nilai * 0.001;
			}
			else{
				$total = $nilai;
			}
		}

		return $total;
 
	}

