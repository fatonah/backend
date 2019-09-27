<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\TransLND; 

class CloseChanUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:CloseChanUpdate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Channel Closing Transaction';

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
        ########################CloseChanUpdate COMMAND####################################
        //update close chan tx
        $crypto = 'LND';
        $alltrans = TransLND::where('category', 'open')->get();
        foreach ($alltrans as $trans) {
            $sat = 100000000;
            $user = User::where('id', $trans['uid'])->first();
            $currency = Currency::where('id',$user->currency)->first();
            $priceApi = PriceCrypto::where('crypto',$crypto)->first();   
            $json_string = settings('url_gecko').'simple/price?ids='.$priceApi->id_gecko.'&vs_currencies='.strtolower($currency->code);
            $jsondata = file_get_contents($json_string);
            $obj = json_decode($jsondata, TRUE); 
            $price = $obj[$priceApi->id_gecko][strtolower($currency->code)];

            $closedchan = listClosedChannel();
            foreach ($closedchan as $chan) {
                foreach ($chan as $c) {
                    if(explode(':', $c['channel_point'])[0] == $trans['txid']){
                        $checktx = TransLND::where('category', 'closed')->where('status', 'success')->where('txid', $c['closing_tx_hash'])->count();
                        if($checktx == 0){
                            $userbalance = number_format(getbalance($crypto, $user->label), 8, '.', ''); // in sat
                            $totalfunds = number_format($c['settled_balance'], 8, '.', ''); // in sat
                            $after_bal =  number_format($userbalance + $totalfunds, 8, '.', '');  // in sat
                            $myr_amount = ($c['settled_balance']/$sat)*$price;

                            if(array_key_exists('close_type', $c)){$remarks = $c['close_type'];}
                            else{$remarks = 'NEGOTIABLE _CLOSE';}

                            $withdraw = new TransLND;
                            $withdraw->uid = $trans['uid'];
                            $withdraw->status = 'success';
                            $withdraw->amount= $totalfunds; 
                            $withdraw->before_bal = $userbalance;
                            $withdraw->after_bal = $after_bal;
                            $withdraw->recipient = $c['remote_pubkey'];
                            $withdraw->txid = $c['closing_tx_hash'];
                            $withdraw->netfee = 0; 
                            $withdraw->walletfee = 0; 
                            $withdraw->remarks = $remarks; 
                            $withdraw->invoice_id = '0';
                            $withdraw->type = 'external';
                            $withdraw->using = 'mobile';
                            $withdraw->category = 'closed';
                            $withdraw->currency = $trans['currency'];
                            $withdraw->rate = number_format($price, 2, '.', '');
                            $withdraw->myr_amount = number_format($myr_amount, 2, '.', ''); 
                            $withdraw->save();
                        }
                    }
                }
            }
        }
    }
}
