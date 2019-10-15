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

            $priceApi = PriceCrypto::where('crypto',$crypto)->first(); 
            $currency = Currency::where('id',$user->currency)->first();

            $obj = json_decode(file_get_contents(settings('url_gecko').'simple/price?ids='.$priceApi->id_gecko.'&vs_currencies='.strtolower($currency->code)), TRUE); 
            $price = $obj[$priceApi->id_gecko][strtolower($currency->code)];

            $closedchan = listClosedChannel();
            foreach ($closedchan as $chan) {
                foreach ($chan as $c) {
                    if(explode(':', $c['channel_point'])[0] == $trans['txid']){
                        $checktx = TransLND::where('category', 'closed')->where('status', 'success')->where('txid', $c['closing_tx_hash'])->count();
                        if($checktx == 0){
                            $txdet = json_decode(file_get_contents('https://api.blockchair.com/bitcoin/dashboards/transaction/'.$c['closing_tx_hash']), TRUE);
                            if($txdet['data']) {
                                $cap_change = number_format($txdet->data->$c['closing_tx_hash']->inputs[0]->value, 8, '.', ''); // in sat
                                $net_fee = number_format($txdet->data->$c['closing_tx_hash']->transaction->fee, 8, '.', ''); // in sat
                                $return_bal = number_format($txdet->data->$c['closing_tx_hash']->outputs[0]->value, 8, '.', ''); // in sat

                                $latesttx = TransLND::where('status', 'success')->latest()->first();
                                $before_cap = $latesttx->lnd_cap;
                                $capacity = number_format($before_cap - $cap_change, 8, '.', ''); // in sat
                                $lndlnd_bal = number_format(getbalance_lndlnd($label), 8, '.', ''); // in sat
                                $lndbtc_bal = number_format(getbalance_lndbtc($label), 8, '.', ''); // in sat
                                $after_bal =  number_format($lndbtc_bal + $return_bal, 8, '.', '');  // in sat
                                $myr_amount = ($return_bal/$sat)*$price; 

                                if(array_key_exists('close_type', $c)){$remarks = $c['close_type'];}
                                else{$remarks = 'NEGOTIABLE _CLOSE';}

                                $withdraw = new TransLND;
                                $withdraw->uid = $useruid->id;
                                $withdraw->status = 'success';
                                $withdraw->amount = $return_bal; 
                                $withdraw->before_bal = $lndbtc_bal;
                                $withdraw->after_bal = $after_bal;
                                $withdraw->lnd_cap = $capacity;
                                $withdraw->lnd_bal = $lndlnd_bal;
                                $withdraw->recipient = $c['remote_pubkey'];
                                $withdraw->txid = $c['closing_tx_hash'];
                                $withdraw->netfee = $net_fee; 
                                $withdraw->walletfee = 0.00000000; 
                                $withdraw->invoice_id = '0';
                                $withdraw->type = 'external';
                                $withdraw->using = 'mobile';
                                $withdraw->category = 'closed';
                                $withdraw->remarks = 'NEGOTIABLE _CLOSE';
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
}
