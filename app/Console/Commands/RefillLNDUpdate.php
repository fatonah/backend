<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

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
        
    }
}
