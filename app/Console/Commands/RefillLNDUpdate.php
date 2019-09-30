<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Withdrawal; 
use App\WalletAddress; 

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
                    $newbalance = $userdet->balance + $txdet['amount'];
                    $walletupdate = WalletAddress::where('crypto', 'LND')->where('address', $txdet['dest_addresses'][0])
                        ->update([
                            'balance' => $newbalance
                        ]);
                }

            }
        }
    }
}
