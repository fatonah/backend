<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\WalletAddress;
use App\PriceCrypto;
use App\User;
use App\Withdrawal;
use App\Setting; 


class currentPrice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:currentPrice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To update table price_api';

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
     
//1. BITCOIN
        $BTC = PriceCrypto::where('crypto', 'BTC')->first();
        $json_url_btc = $BTC->url_api;
//get JSON data
        $json_btc = file_get_contents($json_url_btc);

        $data_btc = json_decode($json_btc);
        $btc_name = $data_btc[0]->name;
        $btc_logo = '<img src="https://assets.coingecko.com/coins/images/1/large/bitcoin.png?1510040391" style="width:50px;">';
        $btc_image = $data_btc[0]->image;
        $btc_24H = round($data_btc[0]->price_change_percentage_24h, 1) . ' %';
        $myr_btc_price = round($data_btc[0]->current_price, 2);


//2. BCH
        $BCH = PriceCrypto::where('crypto', 'BCH')->first();
        $json_url_bch = $BCH->url_api;
//get JSON data
        $json_bch = file_get_contents($json_url_bch);

        $data_bch = json_decode($json_bch);
        $bch_name = $data_bch[0]->name;
        $bch_logo = '<img src="https://assets.coingecko.com/coins/images/780/large/bitcoin_cash.png?1529919381" style="width:50px;">';
        $bch_image = $data_bch[0]->image;
        $bch_24H = round($data_bch[0]->price_change_percentage_24h, 1) . ' %';
        $myr_bch_price = round($data_bch[0]->current_price, 2);


//3. ETH
        $ETH = PriceCrypto::where('crypto', 'ETH')->first();
        $json_url_eth = $ETH->url_api;
//get JSON data
        $json_eth = file_get_contents($json_url_eth);

        $data_eth = json_decode($json_eth);
        $eth_name = $data_eth[0]->name;
        $eth_logo = '<img src="https://assets.coingecko.com/coins/images/279/large/ethereum.png?1510040267" style="width:50px;">';
        $eth_image = $data_eth[0]->image;
        $eth_24H = round($data_eth[0]->price_change_percentage_24h, 1) . ' %';
        $myr_eth_price = round($data_eth[0]->current_price, 2);

        
//4. DASH
        $DASH = PriceCrypto::where('crypto', 'DASH')->first();
        $json_url_dash = $DASH->url_api;
//get JSON data
        $json_dash = file_get_contents($json_url_dash);

        $data_dash = json_decode($json_dash);
        $dash_name = $data_dash[0]->name;
        $dash_logo = '<img src="https://assets.coingecko.com/coins/images/19/large/dash.png?1528882129" style="width:50px;">';
        $dash_image = $data_dash[0]->image;
        $dash_24H = round($data_dash[0]->price_change_percentage_24h, 1) . ' %';
        $myr_dash_price = round($data_dash[0]->current_price, 2);


//5. LITECOIN
        $LTC = PriceCrypto::where('crypto', 'LTC')->first();
        $json_url_ltc = $LTC->url_api;
//get JSON data
        $json_ltc = file_get_contents($json_url_ltc);

        $data_ltc = json_decode($json_ltc);
        $ltc_name = $data_ltc[0]->name;
        $ltc_logo = '<img src="https://assets.coingecko.com/coins/images/2/large/litecoin.png?1510040295" style="width:50px;">';
        $ltc_image = $data_ltc[0]->image;
        $ltc_24H = round($data_ltc[0]->price_change_percentage_24h, 1) . ' %';
        $myr_ltc_price = round($data_ltc[0]->current_price, 2);

        
//XRP
        $XRP = PriceCrypto::where('crypto', 'XRP')->first();
        $json_url_xrp = $XRP->url_api;
//get JSON data
        $json_xrp = file_get_contents($json_url_xrp);

        $data_xrp = json_decode($json_xrp);
        $xrp_name = $data_xrp[0]->name;
        $xrp_logo = '<img src="https://assets.coingecko.com/coins/images/44/large/XRP.png?1536205987" style="width:50px;">';
        $xrp_image = $data_xrp[0]->image;
        $xrp_24H = round($data_xrp[0]->price_change_percentage_24h, 1) . ' %';
        $myr_xrp_price = round($data_xrp[0]->current_price, 2);

        
//7. XLM
        $XLM = PriceCrypto::where('crypto', 'XLM')->first();
        $json_url_xlm = $XLM->url_api;
//get JSON data
        $json_xlm = file_get_contents($json_url_xlm);

        $data_xlm = json_decode($json_xlm);
        $xlm_name = $data_xlm[0]->name;
        $xlm_logo = '<img src="https://assets.coingecko.com/coins/images/100/large/stellar_lumens.png?;" style="width:50px;">';
        $xlm_image = $data_xlm[0]->image;
        $xlm_24H = round($data_xlm[0]->price_change_percentage_24h, 1) . ' %';
        $myr_xlm_price = round($data_xlm[0]->current_price, 2);


//8. DOGECOIN
        $DOGE = PriceCrypto::where('crypto', 'DOGE')->first();
        $json_url_doge = $DOGE->url_api;
//get JSON data
        $json_doge = file_get_contents($json_url_doge);

        $data_doge = json_decode($json_doge);
        $doge_name = $data_doge[0]->name;
        $doge_logo = '<img src="https://assets.coingecko.com/coins/images/5/large/dogecoin.png?1510040365" style="width:50px;">';
        $doge_image = $data_doge[0]->image;
        $doge_24H = round($data_doge[0]->price_change_percentage_24h, 1) . ' %';
        $myr_doge_price = round($data_doge[0]->current_price, 6);


  $updt_BTC = PriceCrypto::where('crypto', 'BTC')
                    ->update([ 
                        'price' => $myr_btc_price,
                         'logo2' => $btc_image,
                         'percentage' => $btc_24H
                        ]);
      

  $updt_BCH = PriceCrypto::where('crypto', 'BCH')
                    ->update([ 
                        'price' => $myr_bch_price,
                         'logo2' => $bch_image,
                         'percentage' => $bch_24H
                        ]);
  
    $updt_ETH = PriceCrypto::where('crypto', 'ETH')
                    ->update([ 
                        'price' => $myr_eth_price,    
                         'logo2' => $eth_image,
                         'percentage' => $eth_24H
                        ]);

    $updt_DASH = PriceCrypto::where('crypto', 'DASH')
                    ->update([ 
                        'price' => $myr_dash_price,
                         'logo2' => $dash_image,
                         'percentage' => $dash_24H
                        ]);
    
        $updt_LTC = PriceCrypto::where('crypto', 'LTC')
                    ->update([ 
                        'price' => $myr_ltc_price,
                         'logo2' => $ltc_image,
                         'percentage' => $ltc_24H
                        ]);
        
          $updt_XRP = PriceCrypto::where('crypto', 'XRP')
                    ->update([ 
                        'price' => $myr_xrp_price,
                         'logo2' => $xrp_image,
                         'percentage' => $xrp_24H
                        ]);
          
          $updt_XLM = PriceCrypto::where('crypto', 'XLM')
                    ->update([ 
                        'price' => $myr_xlm_price,
                         'logo2' => $xlm_image,
                         'percentage' => $xlm_24H
                        ]);
          
          $updt_DOGE = PriceCrypto::where('crypto', 'DOGE')
                    ->update([ 
                        'price' => $myr_doge_price,
                         'logo2' => $doge_image,
                         'percentage' => $doge_24H
                        ]);
                    
                    
                    

    
    }
}
