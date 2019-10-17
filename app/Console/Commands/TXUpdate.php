<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\Withdrawal; 
use App\WalletAddress;
use App\TransUser;
use App\PriceCrypto;
use App\Currency;
use Carbon\Carbon;   

class TXUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:TXUpdate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all coin transaction for send and receive';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // ########################TXUpdate COMMAND####################################
        //update all other crypto txid and details

        ##ALL SEND TX
        $sendtrans = Withdrawal::whereNotNull('txid')->get();
        foreach ($sendtrans as $strans ) {
            $userdet = WalletAddress::where('crypto', $strans['crypto'])->where('address', $strans['recipient'])->first();
            $checksendtx = TransUser::where('crypto', $strans['crypto'])->where('category', 'send')->where('txid', $strans['txid'])->count();
            if($checksendtx == 0){
                if(!$userdet['label']){
                    $lnddet = WalletAddress::where('crypto', 'LND')->where('address', $strans['recipient'])->first();
                    $recipientlabel = $lnddet['label'];
                }
                else{$recipientlabel = $userdet['label'];}

                $txdet = json_decode(file_get_contents('https://api.blockchair.com/bitcoin/dashboards/transaction/'.$strans['txid']), TRUE);
                if($txdet['data']) {
                    $net_fee = number_format($txdet['data'][$strans['txid']]['transaction']['fee']/100000000, 8, '.', '');
                    $blockid = $txdet['data'][$strans['txid']]['transaction']['block_id'];
                    $blockhash = getblockhash($strans['crypto'], $blockid);
                    $blocktime = getblockdet($strans['crypto'], $blockhash)['time'];
                    $conf = getblockdet($strans['crypto'], $blockhash)['confirmations'];

                    $sendtx = TransUser::create([
                        'uid' => $strans['uid'],
                        'type' => $strans['type'],
                        'crypto' => $strans['crypto'],
                        'category' => 'send',
                        'using' => $strans['using'],
                        'status' => $strans['status'],
                        'error_code' => '',
                        'recipient_id' => $recipientlabel,
                        'recipient' => $strans['recipient'],
                        'txid' => $strans['txid'],
                        'confirmation' => $conf,
                        'amount' => $strans['amount'],
                        'before_bal' => $strans['before_bal'],
                        'after_bal' => $strans['after_bal'],
                        'myr_amount' => $strans['myr_amount'],
                        'rate' => $strans['rate'],
                        'currency' => $strans['currency'],
                        'netfee' => $net_fee,
                        'walletfee' => $strans['walletfee'],
                        'remarks' => $strans['remarks'],
                        'time' => Carbon::parse($strans['created_at'])->timestamp,
                        'timereceived' => Carbon::parse($strans['created_at'])->timestamp,
                        'txdate' => date_format($strans['created_at'], "Y-m-d H:i:s"),
                        'vout' => $txdet['data'][$strans['txid']]['outputs'][0]['index'],
                        'blockhash' => $blockhash,
                        'blockindex' => $blockid,
                        'blocktime' => date_format(Carbon::createFromTimestamp($blocktime), "Y-m-d H:i:s"),
                        'walletconflicts' => ''
                    ]);
                }
            }
            else{
                if(strlen($strans['txid']) == 64) {
                    $txdet = gettransaction_crypto($strans['crypto'], $strans['txid']);
                    if($txdet){
                        $sendtx = TransUser::
                        where('crypto', $strans['crypto'])->where('category', 'send')->where('txid', $strans['txid'])
                        ->update(['confirmation' => $txdet['confirmations']]);
                    }
                }
            }
        }

        ##ALL RECEIVE TX
        $crypto = array('bitcoin','bitabc','dogecoin');
        $alluserdet = User::all();
        foreach ($alluserdet as $userdet) {
            $alltransbtc[] = bitcoind()->client($crypto[0])->listtransactions($userdet['label'], 1000000, 0)->get();
            foreach ($alltransbtc as $transbtc) {
                if(!array_key_exists('address', $transbtc)){
                    foreach ($transbtc as $tbtc) {
                        if(isset($tbtc['label'])){$label = $tbtc['label'];}
                        if(isset($tbtc['confirmations'])){$confirmations = $tbtc['confirmations'];}
                        if(isset($tbtc['txid'])){$txid = $tbtc['txid'];}
                        if(isset($tbtc['timereceived'])){$timereceived = $tbtc['timereceived'];}

                        $crosscheck = TransUser::where('category', 'send')->where('txid', $txid)->count();
                        if($crosscheck == 0) {
                            $checktx = TransUser::where('category', 'receive')->where('txid', $txid)->count();
                            if($checktx == 0) {
                                $userdet = WalletAddress::where('crypto', 'BTC')->where('label', $label)->first();
                                $recvuid = $userdet->uid;
                                $amount = number_format($tbtc['amount'], 8, '.', '');
                                $before_bal = number_format($userdet->balance/100000000, 8, '.', '');
                                $after_bal = $before_bal + $amount;

                                $useruid = User::where('label',$label)->first();   
                                $priceApi = PriceCrypto::where('crypto','BTC')->first();     
                                $currency = Currency::where('id',$useruid->currency)->first();
                                $json_string = settings('url_gecko').'simple/price?ids='.$priceApi->id_gecko.'&vs_currencies='.strtolower($currency->code);
                                $jsondata = file_get_contents($json_string);
                                $obj = json_decode($jsondata, TRUE); 
                                $price = number_format($obj[$priceApi->id_gecko][strtolower($currency->code)], 2, '.', '');
                                $myr_amt = number_format($amount*$price, 2, '.', '');

                                $btctx = TransUser::create([
                                    'uid' => $userdet->uid,
                                    'type' => 'external',
                                    'crypto' => 'BTC',
                                    'category' => $tbtc['category'],
                                    'using' => 'mobile',
                                    'status' => 'success',
                                    'error_code' => '',
                                    'recipient' => $tbtc['address'],
                                    'recipient_id' => $label,
                                    'txid' => $txid,
                                    'confirmations' => $tbtc['confirmations'],
                                    'amount' => $amount,
                                    'before_bal' => $before_bal,
                                    'after_bal' => $after_bal,
                                    'myr_amount' => $myr_amt,
                                    'rate' => $price,
                                    'currency' => $useruid->currency,
                                    'netfee' => 0.00000000,
                                    'walletfee' => 0.00000000,
                                    'remarks' => 'RECEIVE',
                                    'time' => $timereceived,
                                    'timereceived' => $timereceived,
                                    'txdate' => date_format(Carbon::createFromTimestamp($timereceived), "Y-m-d H:i:s"),
                                    'vout' => $tbtc['vout'],
                                    'blockhash' => $tbtc['blockhash'],
                                    'blockindex' => $tbtc['blockindex'],
                                    'blocktime' => date_format(Carbon::createFromTimestamp($tbtc['blocktime']), "Y-m-d H:i:s"),
                                    'walletconflicts' => ''
                                ]);
                            }
                            else {$btctx = TransUser::where('category', 'receive')->where('txid', $txid)->update(['confirmation' => $confirmations]);}
                        }           
                    }
                }
                else {
                    $crosscheck = TransUser::where('category', 'send')->where('txid', $transbtc['txid'])->count();
                    if($crosscheck == 0) {
                        $checktx = TransUser::where('category', 'receive')->where('txid', $transbtc['txid'])->count();
                        if($checktx == 0) {
                            $userdet = WalletAddress::where('crypto', 'BTC')->where('label', $transbtc['label'])->first();
                            $recvuid = $userdet->uid;
                            $amount = number_format($tbtc['amount'], 8, '.', '');
                            $before_bal = number_format($userdet->balance/100000000, 8, '.', '');
                            $after_bal = $before_bal + $amount;

                            $useruid = User::where('label',$transbtc['label'])->first();   
                            $priceApi = PriceCrypto::where('crypto','BTC')->first();     
                            $currency = Currency::where('id',$useruid->currency)->first();
                            $json_string = settings('url_gecko').'simple/price?ids='.$priceApi->id_gecko.'&vs_currencies='.strtolower($currency->code);
                            $jsondata = file_get_contents($json_string);
                            $obj = json_decode($jsondata, TRUE); 
                            $price = number_format($obj[$priceApi->id_gecko][strtolower($currency->code)], 2, '.', '');
                            $myr_amt = number_format($amount*$price, 2, '.', '');

                            $btctx = TransUser::create([
                                'uid' => $userdet->uid,
                                'type' => 'external',
                                'crypto' => 'BTC',
                                'category' => $transbtc['category'],
                                'using' => 'mobile',
                                'status' => 'success',
                                'error_code' => '',
                                'recipient' => $transbtc['address'],
                                'recipient_id' => $label,
                                'txid' => $txid,
                                'confirmations' => $transbtc['confirmations'],
                                'amount' => $amount,
                                'before_bal' => $before_bal,
                                'after_bal' => $after_bal,
                                'myr_amount' => $myr_amt,
                                'rate' => $price,
                                'currency' => $useruid->currency,
                                'netfee' => 0.00000000,
                                'walletfee' => 0.00000000,
                                'remarks' => 'RECEIVE',
                                'time' => $timereceived,
                                'timereceived' => $timereceived,
                                'txdate' => date_format(Carbon::createFromTimestamp($timereceived), "Y-m-d H:i:s"),
                                'vout' => $transbtc['vout'],
                                'blockhash' => $transbtc['blockhash'],
                                'blockindex' => $transbtc['blockindex'],
                                'blocktime' => date_format(Carbon::createFromTimestamp($transbtc['blocktime']), "Y-m-d H:i:s"),
                                'walletconflicts' => ''
                            ]);
                        }
                        else{
                            $btctx = TransUser::where('category', 'receive')->where('txid', $transbtc['txid'])->update(['confirmation' => $transbtc['confirmations']]);
                        }
                    }
                }
            }
        }
    }
}
