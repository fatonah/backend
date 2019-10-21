<?php
// Borrowed from https://gitlab.com/MMuArFF/lnd-php-wallet with added macaroon and genseed support by @ketominer
// Full Documentation regarding all API URL : https://api.lightning.community/rest/
namespace App\Avant;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class LNDAvtClient 
{

    public $macaroon = '';
    public $rest_url = '';
    
    function __construct($mac = '', $url = '' ) {
        $this->macaroon = Storage::disk('spaces')->get("lateralblk-004-conf/lateralblk-004-access002.macaroon");
        //$this->rest_url = Storage::disk('spaces')->get("lateralblk-004-conf/lateralblk-004-endpt.txt");
        $this->rest_url = Storage::disk('spaces')->get("lateralblk-004-conf/lateralblk-004-endpt-002.txt");
        //$this->rest_url = "https://lateral4.pinkexc.com:8443";
    }
    private function LnRest($add,$post= ""){
        $url = $this->rest_url.$add;
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_CAINFO, getcwd() . Storage::disk('spaces')->get("lateralblk-004-conf/lateralblk-004-tls-002.crt"));

        //curl_setopt($ch, CURLOPT_CAPATH, $capath);
        curl_setopt($ch, CURLOPT_TIMEOUT, 25);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 25);
        $machex = bin2hex($this->macaroon);
        $payload = 'Grpc-Metadata-macaroon : '.$machex;
        curl_setopt($ch, CURLOPT_HTTPHEADER, array($payload,'Accept: application/json','Content-Type: application/json')); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if(!empty($post)){
            curl_setopt($ch,CURLOPT_POST, 1);
            curl_setopt($ch,CURLOPT_POSTFIELDS, $post);
        }
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
    // private function LnRest($add,$post= ""){
    //     $url = $this->rest_url.$add;
    //     //$capath = Storage::disk('spaces')->get("lateralblk-004-conf/lateralblk-004-tls.cert");
    //     $ch = curl_init($url);
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //     //curl_setopt($ch, CURLOPT_CAPATH, $capath);
    //     curl_setopt($ch, CURLOPT_TIMEOUT, 25);
    //     curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 25);
    //     $machex = bin2hex($this->macaroon);
    //     $payload = 'Grpc-Metadata-macaroon : '.$machex;
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, array($payload,'Accept: application/json','Content-Type: application/json')); 
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     if(!empty($post)){
    //         curl_setopt($ch,CURLOPT_POST, 1);
    //         curl_setopt($ch,CURLOPT_POSTFIELDS, $post);
    //     }
    //     $data = curl_exec($ch);
    //     curl_close($ch);
    //     dd($data);
    //     return $data;
    // }
    private function LnRestDEL($add){
        $url = $this->rest_url.$add;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        //curl_setopt($ch, CURLOPT_CAPATH, $capath);
        curl_setopt($ch, CURLOPT_TIMEOUT, 25);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 25);
        $machex = bin2hex($this->macaroon);
        $payload = 'Grpc-Metadata-macaroon : '.$machex;
        curl_setopt($ch, CURLOPT_HTTPHEADER, array($payload,'Accept: application/json','Content-Type: application/json')); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    
    ///////////////////////////////////////////////////////////////
    /// NODE STATUS /////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////
    public function testconnect(){
        $res = LNDAvtClient::LnRest('/v1/getinfo');
        if(empty($res)):
            return false;
        else:
            return true;
        endif;
    }
    public function testunlock() {
        $info = json_decode(LNDAvtClient::LnRest('/v1/getinfo'),true);
        if(!empty($info)):        
            return true; 
        else:
            return false;
        endif;
    }
    public function getInfo() {
        $res = LNDAvtClient::LnRest('/v1/getinfo');
        return json_decode($res,true);   
    }
     public function getNodeInfos($pub_key) {
        $res = LNDAvtClient::LnRest('/v1/graph/node/'.$pub_key);
        return json_decode($res,true); 
    }
    public function getPeers() {
        $res = LNDAvtClient::LnRest('/v1/peers');
        return json_decode($res,true);
    }
    public function connectPeers($addr,$perm = true) { 
        $addr = explode("@",$addr);
        $host = $addr[1];
        $node = $addr[0];
        $peers = json_encode(array(
            'addr' => array ("host"=> $host,"pubkey"=>$node),
            'perm'=> $perm
        ));
        $res = LNDAvtClient::LnRest('/v1/peers',$peers);
        return json_decode($res,true);
    }
    public function getGraph() {
        $res = LNDAvtClient::LnRest('/v1/graph');
        return json_decode($res,true);    
    }
    public function getGraphInfo() {
        $res = LNDAvtClient::LnRest('/v1/graph/info');
        return json_decode($res,true);
    }
    public function getGraphRoutes($pub_key, $amount) {
        $res = LNDAvtClient::LnRest('/v1/graph/routes/'.$pub_key.'/'.$amount);
        return json_decode($res,true);
    }
    public function getGraphEdge($chanid) {
        $res = LNDAvtClient::LnRest('/v1/graph/edge'.$chanid);
        return json_decode($res,true);
    }
    ///////////////////////////////////////////////////////////////
    /// WALLET /////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////
    public function initWallet($pass, $seed) {
        $payload =  json_encode(array(
            'wallet_password' => base64_encode($pass), 
            'cipher_seed_mnemonic' => $seed
        ));
        $res = LNDAvtClient::LnRest('/v1/initwallet',$payload);
        return json_decode($res,true);
    }
    public function unlockWallet($pass) {
        $pass =  json_encode(array('wallet_password' => base64_encode($pass)));
        $res = LNDAvtClient::LnRest('/v1/unlockwallet',$pass);
        return json_decode($res,true);
    }
    public function genSeed() {
        $res = LNDAvtClient::LnRest('/v1/genseed');
        return json_decode($res,true);
    }
    public function changePassword($newpass, $currpasss) {
        $payload =  json_encode(array(
            'new_password' => $newpass,
            'current_password' => $currpasss
        ));
        $res = LNDAvtClient::LnRest('/v1/changepassword',$payload);
        return json_decode($res,true);
    }
    public function newAddress($type = "") {
        $res =LNDAvtClient::LnRest('/v1/newaddress');
        return json_decode($res,true);
    }
    public function getWalletBalance() {
        $res = LNDAvtClient::LnRest('/v1/balance/blockchain');
        return json_decode($res,true);
    }
    public function sendOnChain($sendall, $amount, $address) {
        $payload =  json_encode(array(
            'send_all' => $sendall,
            'amount' => $amount,
            //'sat_per_byte' => $satperbyte,
            'addr' => $address
        ));
        $res = LNDAvtClient::LnRest('/v1/transactions',$payload);
        return json_decode($res,true);
    }
    public function decodeInvoice($invoice) {
        $res = LNDAvtClient::LnRest('/v1/payreq/' . $invoice);
        return json_decode($res,true);
    }
    public function sendPayment($destination) {
        $payreq = array('payment_request' => $destination);
        $payreq = json_encode($payreq);
        $res = LNDAvtClient::LnRest('/v1/channels/transactions',$payreq);
        return json_decode($res,true);
    }
    public function addInvoice($value,$memo,$expiry,$falladdr,$private=true) {
        $payload =  json_encode(array(
            'value' => $value,
            'memo' => $memo,
            'expiry' => $expiry,
            'private' => $private,
            'fallback_addr' => $falladdr
        ));    
        $res = LNDAvtClient::LnRest('/v1/invoices',$payload);
        return json_decode($res,true);
    }
    public function subInvoice() {
        $res = LNDAvtClient::LnRest('/v1/invoices/subscribe');
        return json_decode($res,true);    
    }
     public function getFee() {
        $res = LNDAvtClient::LnRest('/v1/fees');
        return json_decode($res,true);
    }
    public function getTransactionFee() {
        $res = LNDAvtClient::LnRest('/v1/transactions/fee');
        return json_decode($res,true);
    }
    public function verifyMessage($msg, $sign) {
        $payload =  json_encode(array(
            'msg' => $msg,
            'signature' => $sign
        ));
        $res = LNDAvtClient::LnRest('/v1/verifymessage',$payload);
        return json_decode($res,true);    
    }
    public function getSignMessage($msg) {
        $payload =  json_encode(array('msg' => $msg));
        $res = LNDAvtClient::LnRest('/v1/signmessage',$payload);
        return json_decode($res,true);
    }
    ///////////////////////////////////////////////////////////////
    /// CHANNELS /////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////
    public function getAllChannels() {
        $res = LNDAvtClient::LnRest('/v1/channels');
        return json_decode($res,true);
    }
    public function closeChannel($tx_id){
        $tx_id = explode(':',$tx_id);
        //($force)? $force = '?force=true': "";
        $hex_id =$tx_id['0'].'/'.$tx_id['1'];
        $url = '/v1/channels/'.$hex_id;
        $res = LNDAvtClient::LnRestDEL($url);
        return json_decode($res,true);        
    }
    public function openChannel($nodePubkeyString,$localFundingAmount,$pushSat,$targetConf = "",$satPerByte =""){
        $array = json_encode(array(
            'node_pubkey_string' =>  $nodePubkeyString,
            'local_funding_amount' => $localFundingAmount,
            'push_sat' => $pushSat   
        ));
        $res = LNDAvtClient::LnRest('/v1/channels',$array);
        return json_decode($res,true);
    }
    public function getChanClosed() {
        $res = LNDAvtClient::LnRest('/v1/channels/closed');
        return json_decode($res,true);    
    }
    public function getPendingChannels() {
        $res = LNDAvtClient::LnRest('/v1/channels/pending');
        return json_decode($res,true);
    }
    public function getChannelBalance() {
        $res = LNDAvtClient::LnRest('/v1/balance/channels');
        return json_decode($res,true);
    }
    public function getChanPolicyUpdate($chanid, $basefeemsat, $feerate, $global) {
        $payload =  json_encode(array(
            'chan_point' => $chanid,
            'time_lock_delta' => (int)Carbon::now()->timestamp,
            'base_fee_msat' => $basefeemsat,
            'fee_rate' => $feerate,
            'global' => $global
        ));
        $res = LNDAvtClient::LnRest('/v1/chanpolicy',$payload);
        return json_decode($res,true);
    }
    public function getChanBackup($fundingtxid, $outputindex) {
        $res = LNDAvtClient::LnRest('/v1/channels/backup'.$fundingtxid.'/'.$outputindex);
          return json_decode($res,true);
    }
    public function getChanBackupVerify($chan, $multichan) {
        $payload =  json_encode(array(
            'multi_chan_backup' => $multichan,
            'chan_backups' => $chan
        ));
        $res = LNDAvtClient::LnRest('/v1/channels/backup/verify',$payload);
        return json_decode($res,true);
    }
    public function getChanRestore($chan, $multichan) {
        $payload =  json_encode(array(
            'chan_backups' => $chan,
            'multi_chan_backup' => $multichan
        ));
        $res = LNDAvtClient::LnRest('/v1/channels/backup/restore',$payload);
        return json_decode($res,true);
    }
    ///////////////////////////////////////////////////////////////
    /// TRANSACTIONS /////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////
    public function getTransactions() {
        $res = LNDAvtClient::LnRest('/v1/transactions');
        return json_decode($res,true);
    }
    public function getAllInvoices($pending_only = false) {
        $res = LNDAvtClient::LnRest('/v1/invoices');
        return json_decode($res,true);
    }
    public function getChanTransaction($outchanid) {
        $payload =  json_encode(array('outgoing_chan_id' => $outchanid));
        $res = LNDAvtClient::LnRest('/v1/channels/transactions',$payload);
        return json_decode($res,true);    
    }
    public function getPayments() {
        $res = LNDAvtClient::LnRest('/v1/payments');
        return json_decode($res,true);
    }
    public function getUnspent() {
        $res = LNDAvtClient::LnRest('/v1/utxos');
        return json_decode($res,true);
    }
    public function getInvoice($payhash) {
        $payload =  json_encode(array('r_hash' => $payhash));
        $res = LNDAvtClient::LnRest('/v1/invoices',$payload);
        return json_decode($res,true);
    }
    public function getSwitch($start, $end, $offset, $maxevt) {
        $payload =  json_encode(array(
            'start_time' => $start,
            'index_offset' => $offset,
            'end_time' => $end,
            'num_max_events' => $maxevt
        ));
        $res = LNDAvtClient::LnRest('/v1/switch',$payload);
        return json_decode($res,true);    
    }    
}

