<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\InvoiceLND;

class InvoiceUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:InvoiceUpdate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Invoice payment status';

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
        ########################InvoiceUpdate COMMAND####################################
        //update expired invoice
        $allinv = InvoiceLND::all();
        $curr = Carbon::now(); 
        foreach ($allinv as $inv) {
            $invhash[] = $inv['hash'];
            foreach ($invhash as $hash) {
                $invdet[] = getInvoiceDet($hash);
                foreach ($invdet as $det) {
                    if(!array_key_exists('error', $det)){
                        $create_ts = $det['timestamp'];
                        $hours = $det['expiry']/3600;
                        $create_date = Carbon::createFromTimestamp($create_ts); 
                        $exp_date = Carbon::parse($create_date)->addHour($hours);
                        $diff = $exp_date->diffInMinutes($curr);
                        if($diff > 1){$upinv = InvoiceLND::where('hash', $hash)->update(['status' => 'expired']);}
                    }
                }   
            }
        }
        //update paid invoice
        $allpayment = getLightningPayment();
        foreach ($allpayment as $payment) {
            foreach ($payment as $pay) {
                if($pay['status'] == "SUCCEEDED"){
                    $upinv = InvoiceLND::where('hash', $pay['payment_request'])->update([
                        'txid' => $pay['payment_hash'],
                        'status' => 'paid'
                    ]);
                }
            }
        }
    }
}
