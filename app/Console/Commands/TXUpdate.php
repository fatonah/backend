<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Withdrawal; 
use App\WalletAddress;
use App\TransUser;  

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

                $sendtx = TransUser::create([
                    'uid' => $strans['uid'],
                    'status' => $strans['status'],
                    'crypto' => $strans['crypto'],
                    'type' => $strans['type'],
                    'using' => $strans['using'],
                    'remarks' => $strans['remarks'],
                    'before_bal' => $strans['before_bal'],
                    'after_bal' => $strans['after_bal'],
                    'myr_amount' => $strans['myr_amount'],
                    'netfee' => $strans['netfee'],
                    'walletfee' => $strans['walletfee'],
                    'rate' => $strans['rate'],
                    'currency' => $strans['currency'],
                    'recipient' => $strans['recipient'],
                    'category' => 'send',
                    'amount' => $strans['amount'],
                    'recipient_id' => $recipientlabel,
                    'txid' => $strans['txid'],
                    'time' => Carbon::parse($strans['created_at'])->timestamp,
                    'timereceived' => Carbon::parse($strans['created_at'])->timestamp,
                    'txdate' => date_format($strans['created_at'], "Y-m-d H:i:s"),
                ]);
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
                                    'status' => 'success',
                                    'crypto' => 'BTC',
                                    'type' => 'external',
                                    'remarks' => 'RECEIVE',
                                    'before_bal' => $before_bal,
                                    'after_bal' => $after_bal,
                                    'myr_amount' => $myr_amt,
                                    'netfee' => getestimatefee('BTC'),
                                    'walletfee' => 0,
                                    'rate' => $price,
                                    'currency' => $useruid->currency,
                                    'recipient' => $tbtc['address'],
                                    'category' => $tbtc['category'],
                                    'amount' => $amount,
                                    'recipient_id' => $label,
                                    'confirmations' => $confirmations,
                                    'txid' => $txid,
                                    'time' => $timereceived,
                                    'timereceived' => $timereceived,
                                    'txdate' => date_format(Carbon::createFromTimestamp($timereceived), "Y-m-d H:i:s")
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
                                'status' => 'success',
                                'crypto' => 'BTC',
                                'type' => 'external',
                                'remarks' => 'RECEIVE',
                                'before_bal' => $before_bal,
                                'after_bal' => $after_bal,
                                'myr_amount' => $myr_amt,
                                'netfee' => getestimatefee('BTC'),
                                'walletfee' => 0,
                                'rate' => $price,
                                'currency' => $useruid->currency,
                                'recipient' => $transbtc['address'],
                                'category' => $transbtc['category'],
                                'amount' => number_format($transbtc['amount'], 8, '.', ''),
                                'recipient_id' => $transbtc['label'],
                                'confirmations' => $transbtc['confirmations'],
                                'txid' => $transbtc['txid'],
                                'time' => $transbtc['timereceived'],
                                'timereceived' => $transbtc['timereceived'],
                                'txdate' => date_format(Carbon::createFromTimestamp($transbtc['timereceived']), "Y-m-d H:i:s")
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
