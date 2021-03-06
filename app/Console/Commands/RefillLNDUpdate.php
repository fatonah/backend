<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Withdrawal; 
use App\WalletAddress;
use App\TransLND;  

class RefillLNDUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:RefillLNDUpdate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update balance of Lightning based on BTC txid confirmation';

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
        ########################RefillLNDUpdate COMMAND####################################
        //update funding lnd txid n balance
        $alltrans = Withdrawal::where('crypto', 'BTC')->where('status', 'success')->where('remarks', 'FUND_LND')->get();
        foreach ($alltrans as $trans) {
            $transdet[] = getLightningTXDet($trans['txid']);
            foreach ($transdet as $txdet) {
                if($txdet['num_confirmations'] >= 6){
                    $userdet = WalletAddress::where('crypto', 'LND')->where('address', $txdet['dest_addresses'][0])->first();
                    $amount = number_format($txdet['amount'], 8, '.', '');
                    $after_bal = $userdet->balance + $amount;
                    
                    $checktx = TransLND::where('category', 'refill')->where('status', 'success')->where('txid', $txdet['tx_hash'])->count();
                    if($checktx == 0){
                        $trans = TransLND::create([
                            'uid' => $userdet->uid,
                            'type' => $trans['type'],
                            'crypto' => 'LND',
                            'category' => 'refill',
                            'using' => $trans['using'],
                            'status' => $trans['status'],
                            'recipient' => $txdet['dest_addresses'][0],
                            'txid' => $txdet['tx_hash'],
                            'amount' => $amount,
                            'before_bal' => $userdet->balance,
                            'after_bal' => $after_bal,
                            'myr_amount' => $trans['myr_amount'],
                            'rate' => $trans['rate'],
                            'currency' => $trans['currency'],
                            'netfee' => $trans['netfee'],
                            'walletfee' => $trans['walletfee'],
                            'remarks' => $trans['remarks'],

                        ]);

                        $translnd = TransLND::where('status', 'success')->latest()->first();
                        $walletupdate = WalletAddress::where('crypto', 'LND')->where('address', $txdet['dest_addresses'][0])
                        ->update([
                            'balance' => $translnd->after_bal
                        ]);
                        
                    }

                    $translnd = TransLND::where('status', 'success')->latest()->first();
                    $walletupdate = WalletAddress::where('crypto', 'LND')->where('address', $txdet['dest_addresses'][0])
                    ->update([
                        'balance' => $translnd->after_bal
                    ]);
                }
            }
        }
    }
}
