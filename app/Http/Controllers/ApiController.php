<?php
 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator; 
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Images;
use App\Lib\GoogleAuthenticator;
use Denpa\Bitcoin\LaravelClient as BitcoinClient;
use Carbon\Carbon; 

use DB;
use Mail; 
use Maatwebsite\Excel\Facades\Excel;
use App\User;
use App\State;
use App\Currency;
use App\PriceCrypto;
use App\WalletAddress; 
use App\Withdrawal; 
use App\MenuTicket;
use App\Messages; 
use App\Ticket; 
use App\Setting; 
use App\Appver;
use App\TransLND; 
use App\InvoiceLND; 
use App\Exports\TransExport; 
use App\TransUser; 

class ApiController extends Controller{ 
	
	#################Debug #########################
	public function debug(){ 
		//dd(getbalance_lndbtc('usr_niha_pinkexc'),getbalance_lndlnd('usr_niha_pinkexc'));
	    //dd(getbalance('LND','usr_bsod666'));
	    dd(listransaction('LND', 'usr_bsod666', 130, 'bitcoin'));
		dd(getconnection('BTC'));
	//	dd(genseed(23));
		//dd(listransactionall('BTC'));
		//dd(listchannel('LND','usr_bsod666'));
		//c3da7372d65dbeae090c769acb755d39d04181ce559bc647dd7c1882a36acca8
		//1e1c0529daf9d31b1aef010e8acd66ff01c698cb0476daf7a25cb80e27447623
		//0a6da1f0b67a7ebe1a1e7475ba7904ee2d7fd29e35ce6b7eead60400ec334d5c

		//dd(getLightningTXDet('c3da7372d65dbeae090c769acb755d39d04181ce559bc647dd7c1882a36acca8'));
		//dd(gettransaction_crypto('BTC', 'be14814ed8b9e4292a93ce630c060e5a2237c33e75192aa96773e764a8e87fa5'));
		//$create_ts = "1568790796";
		//$hours = strval("9000"/3600);
		//$create_date = Carbon::createFromTimestamp($create_ts); 
		//$exp_date = Carbon::parse($create_date)->addHour($hours);
		//$diff = $exp_date->diffInMinutes($curr);

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
        dd($crosscheck, $checktx);
        //dd($strans_txid,$txid_arr);
        dd($btctx, $txid_arr);








        // foreach ($alluserdet as $userdet) {
        // 	$alltransbch[] = bitcoind()->client($crypto[1])->listtransactions($userdet['label'], 1000000, 0)->get();
        // 	$i=0;
        // 	if($alltransbch){
	       //  	foreach ($alltransbch as $transbch) {
	       //  		if(!array_key_exists('account', $transbch)){
		      //   		foreach ($transbch as $tbch) {
		      //   			if(isset($tbch['address'])){$address = $tbch['address'];}
		      //   			if(isset($tbch['label'])){$label = $tbch['label'];}
		      //   			if(isset($tbch['confirmations'])){$confirmations = $tbch['confirmations'];}
		      //   			if(isset($tbch['txid'])){$txid = $tbch['txid'];}
		      //   			if(isset($tbch['timereceived'])){$timereceived = $tbch['timereceived'];}

		      //   			$bchtx[$i] = array(
		      //   				'uid' => 23,
        // 						'status' => 'success',
		      //   				'crypto' => 'BCH',
		      //   				'type' => 'external',
		      //   				'remarks' => 'RECEIVE',
		      //   				'before_bal' => '0.01538660',
						  //       'after_bal' => '0.0145559',
						  //       'myr_amount' => '0.07',
						  //       'rate' => '1302.6',
						  //       'currency' => '130',
			     //    			'recipient' => substr($address,12),
			     //    			'category' => $tbch['category'],
			     //    			'amount' => number_format($tbch['amount'], 8, '.', ''),
			     //    			'recipient_id' => $label,
			     //    			'confirmations' => $confirmations,
			     //    			'txid' => $txid,
			     //    			'timestamp' => date_format(Carbon::createFromTimestamp($timereceived), "Y-m-d H:i:s"),
			     //    		);
			     //    		$i++;
		      //   		}
		      //   	}
		      //   	else {
		      //   		$bchtx[$i] = array(
		      //   			'uid' => 23,
        // 					'status' => 'success',
		      //   			'crypto' => 'BCH',
		      //   			'type' => 'external',
		      //   			'remarks' => 'RECEIVE',
		      //   			'before_bal' => '0.01538660',
					   //      'after_bal' => '0.0145559',
					   //      'myr_amount' => '0.07',
					   //      'rate' => '1302.6',
					   //      'currency' => '130',
		      //   			'recipient' => substr($transbch['address'],12),
		      //   			'category' => $transbch['category'],
		      //   			'amount' => number_format($transbch['amount'], 8, '.', ''),
		      //   			'recipient_id' => $transbch['label'],
		      //   			'confirmations' => $transbch['confirmations'],
		      //   			'txid' => $transbch['txid'],
		      //   			'timestamp' => date_format(Carbon::createFromTimestamp($transbch['timereceived']), "Y-m-d H:i:s"),
		      //   		);
		      //   		$i++;
		      //   	}
	       //  	}
	       //  }
        // }
        // foreach ($alluserdet as $userdet) {
        // 	$alltransdoge[] = bitcoind()->client($crypto[2])->listtransactions($userdet['label'], 1000000, 0)->get();
        // 	$i=0;
        // 	if($alltransdoge){
	       //  	foreach ($alltransdoge as $transdoge) {
	       //  		if(!array_key_exists('account', $transdoge)){
		      //   		foreach ($transdoge as $tdoge) {
		      //   			if(isset($tdoge['address'])){$address = $tdoge['address'];}
		      //   			if(isset($tdoge['label'])){$label = $tdoge['label'];}
		      //   			if(isset($tdoge['confirmations'])){$confirmations = $tdoge['confirmations'];}
		      //   			if(isset($tdoge['txid'])){$txid = $tdoge['txid'];}
		      //   			if(isset($tdoge['timereceived'])){$timereceived = $tdoge['timereceived'];}

		      //   			$dogetx[$i] = array(
		      //   				'uid' => 23,
        // 						'status' => 'success',
		      //   				'crypto' => 'DOGE',
		      //   				'type' => 'external',
		      //   				'remarks' => 'RECEIVE',
		      //   				'before_bal' => '0.01538660',
						  //       'after_bal' => '0.0145559',
						  //       'myr_amount' => '0.07',
						  //       'rate' => '1302.6',
						  //       'currency' => '130',
			     //    			'recipient' => $address,
			     //    			'using' => 'mobile',
			     //    			'category' => $tdoge['category'],
			     //    			'amount' => number_format($tdoge['amount'], 8, '.', ''),
			     //    			'recipient_id' => $label,
			     //    			'confirmations' => $confirmations,
			     //    			'txid' => $txid,
			     //    			'timestamp' => date_format(Carbon::createFromTimestamp($timereceived), "Y-m-d H:i:s"),
			     //    		);
			     //    		$i++;
		      //   		}
		      //   	}
		      //   	else {
		      //   		$dogetx[$i] = array(
		      //   			'uid' => 23,
        // 					'status' => 'success',
		      //   			'crypto' => 'DOGE',
		      //   			'type' => 'external',
		      //   			'remarks' => 'RECEIVE',
		      //   			'before_bal' => '0.01538660',
					   //      'after_bal' => '0.0145559',
					   //      'myr_amount' => '0.07',
					   //      'rate' => '1302.6',
					   //      'currency' => '130',
		      //   			'recipient' => $transdoge['address'],
		      //   			'category' => $transdoge['category'],
		      //   			'amount' => number_format($transdoge['amount'], 8, '.', ''),
		      //   			'recipient_id' => $transdoge['label'],
		      //   			'confirmations' => $transdoge['confirmations'],
		      //   			'txid' => $transdoge['txid'],
		      //   			'timestamp' => date_format(Carbon::createFromTimestamp($transdoge['timereceived']), "Y-m-d H:i:s"),
		      //   		);
		      //   		$i++;
		      //   	}
	       //  	}
	       //  }
        // }
        // dd($alltransbtc, $btctx, $bchtx, $dogetx);
		// ########################RefillLNDUpdate COMMAND####################################
  //       //update funding lnd txid n balance
  //       $alltrans = Withdrawal::where('crypto', 'BTC')->where('status', 'success')->whereNotNull('txid')->get();
  //       foreach ($alltrans as $trans) {
  //           $transdet[] = getLightningTXDet($trans['txid']);
  //           if($transdet != null){
  //           	foreach ($transdet as $txdet) {

	 //                if($txdet['dest_addresses'][0] == $trans['recipient'] && $txdet['num_confirmations'] >= 6){
	 //                    $userdet = WalletAddress::where('crypto', 'LND')->where('address', $txdet['dest_addresses'][0])->first();
	 //                    $after_bal = $userdet->balance + $txdet['amount'];
	 //                    $test[] = $txdet['dest_addresses'][0] == $trans['recipient'];
	                    
	                    // $walletupdate = WalletAddress::where('crypto', 'LND')->where('address', $txdet['dest_addresses'][0])
	 //                        ->update([
	 //                            'balance' => $after_bal
	 //                        ]);

	 //                    $checktx = TransLND::where('category', 'refill')->where('status', 'success')->where('txid', $txdet['tx_hash'])->count();
	 //                    if($checktx == 0){
	 //                        $trans = TransLND::create([
	 //                            'uid' => $userdet->uid,
	 //                            'type' => $trans['type'],
	 //                            'crypto' => 'LND',
	 //                            'category' => 'refill',
	 //                            'using' => $trans['using'],
	 //                            'status' => $trans['status'],
	 //                            'recipient' => $txdet['dest_addresses'][0],
	 //                            'txid' => $txdet['tx_hash'],
	 //                            'amount' => $txdet['amount'],
	 //                            'before_bal' => $userdet->balance,
	 //                            'after_bal' => $after_bal,
	 //                            'myr_amount' => $trans['myr_amount'],
	 //                            'rate' => $trans['rate'],
	 //                            'currency' => $trans['currency'],
	 //                            'netfee' => $trans['netfee'],
	 //                            'walletfee' => $trans['walletfee'],
	 //                            'remarks' => 'FUND_LND',

	 //                        ]);
	 //                    }
	 //                }
	 //            }
  //           }
            
  //       }
		// dd($alltrans, $trans['currency'], $userdet->balance, $after_bal, $txdet['num_confirmations'], $txdet['amount'], $txdet['tx_hash'], $txdet['dest_addresses'][0], $transdet);


		########################InvoiceUpdate COMMAND####################################
		// //update expired invoice
		// $allinv = InvoiceLND::all();
		// $curr = Carbon::now(); 
		// foreach ($allinv as $inv) {
		// 	$invhash[] = $inv['hash'];
		// 	foreach ($invhash as $hash) {
		// 		$invdet[] = getInvoiceDet($hash);
		// 		foreach ($invdet as $det) {
		// 			if(!array_key_exists('error', $det)){
		// 				$create_ts = $det['timestamp'];
		// 				$hours = $det['expiry']/3600;
		// 				$create_date = Carbon::createFromTimestamp($create_ts); 
		// 				$exp_date = Carbon::parse($create_date)->addHour($hours);
		// 				$diff = $exp_date->diffInMinutes($curr);
		// 				if($diff > 1){$upinv = InvoiceLND::where('hash', $hash)->update(['status' => 'expired']);}
		// 			}
		// 		}	
		// 	}
		// }
		// //update paid invoice
		// $allpayment = getLightningPayment();
		// foreach ($allpayment as $payment) {
		// 	foreach ($payment as $pay) {
		// 		if($pay['status'] == "SUCCEEDED"){
		// 			$upinv = InvoiceLND::where('hash', $pay['payment_request'])->update([
		// 				'txid' => $pay['payment_hash'],
		// 				'status' => 'paid'
		// 			]);
		// 		}
		// 	}
		// }


		// ########################CloseChanUpdate COMMAND####################################
		// //update close chan tx
		// $crypto = 'LND';
		// $alltrans = TransLND::where('category', 'open')->get();
		// foreach ($alltrans as $trans) {
		// 	$sat = 100000000;
		// 	$user = User::where('id', $trans['uid'])->first();
		// 	$currency = Currency::where('id',$user->currency)->first();
		// 	$priceApi = PriceCrypto::where('crypto',$crypto)->first(); 	 
		// 	$json_string = settings('url_gecko').'simple/price?ids='.$priceApi->id_gecko.'&vs_currencies='.strtolower($currency->code);
		// 	$jsondata = file_get_contents($json_string);
		// 	$obj = json_decode($jsondata, TRUE); 
		// 	$price = $obj[$priceApi->id_gecko][strtolower($currency->code)];

		// 	$closedchan = listClosedChannel();
		// 	foreach ($closedchan as $chan) {
		// 		foreach ($chan as $c) {
		// 			if(explode(':', $c['channel_point'])[0] == $trans['txid']){
		// 				$checktx = TransLND::where('category', 'closed')->where('status', 'success')->where('txid', $c['closing_tx_hash'])->count();
		// 				if($checktx == 0){
		// 					$userbalance = number_format(getbalance($crypto, $user->label), 8, '.', ''); // in sat
		// 					$totalfunds = number_format($c['settled_balance'], 8, '.', ''); // in sat
		// 					$after_bal =  number_format($userbalance + $totalfunds, 8, '.', '');  // in sat
		// 					$myr_amount = ($c['settled_balance']/$sat)*$price;

		// 					if(array_key_exists('close_type', $c)){$remarks = $c['close_type'];}
		// 					else{$remarks = 'NEGOTIABLE _CLOSE';}

		// 					$withdraw = new TransLND;
		// 					$withdraw->uid = $trans['uid'];
		// 					$withdraw->status = 'success';
		// 					$withdraw->amount= $totalfunds; 
		// 					$withdraw->before_bal = $userbalance;
		// 					$withdraw->after_bal = $after_bal;
		// 					$withdraw->recipient = $c['remote_pubkey'];
		// 					$withdraw->txid = $c['closing_tx_hash'];
		// 					$withdraw->netfee = 0; 
		// 					$withdraw->walletfee = 0; 
		// 					$withdraw->remarks = $remarks; 
		// 					$withdraw->invoice_id = '0';
		// 					$withdraw->type = 'external';
		// 					$withdraw->using = 'mobile';
		// 					$withdraw->category = 'closed';
		// 					$withdraw->currency = $trans['currency'];
		// 					$withdraw->rate = number_format($price, 2, '.', '');
		// 					$withdraw->myr_amount = number_format($myr_amount, 2, '.', ''); 
		// 					$withdraw->save();
		// 				}
		// 			}
		// 		}
		// 	}
		// }
		// dd($checktx);

		

		// foreach ($allinv as $inv) {
		// 	$invhash[] = $inv['hash'];
		// 	foreach ($invhash as $hash) {
		// 		$invdet[] = getInvoiceDet($hash);
		// 		foreach ($invdet as $det) {
		// 			if(!array_key_exists('error', $det)){
		// 				$create_ts = $det['timestamp'];
		// 				$hours = $det['expiry']/3600;
		// 				$create_date = Carbon::createFromTimestamp($create_ts); 
		// 				$exp_date = Carbon::parse($create_date)->addHour($hours);
		// 				$diff = $exp_date->diffInMinutes($curr);
		// 				if($diff > 1){$upinv = InvoiceLND::where('hash', $hash)->update(['status' => 'expired']);}
		// 			}
		// 		}	
		// 	}
		// }
		// //update paid invoice
		// $allpayment = getLightningPayment();
		// foreach ($allpayment as $payment) {
		// 	foreach ($payment as $pay) {
		// 		if($pay['status'] == "SUCCEEDED"){
		// 			$upinv = InvoiceLND::where('hash', $pay['payment_request'])->update([
		// 				'txid' => $pay['payment_hash'],
		// 				'status' => 'paid'
		// 			]);
		// 		}
		// 	}
		// }
        
    

		// //$conn = test();
		// //dd($conn);
		// //dd(receivelightning001('usr_bsod666', 160, 'lolo', 1));
		// //BTC//
		// $crypto = 'LND';
		// $label = 'usr_niha_pinkexc';
		// $address = 'bc1q2gu8gq43j3zemzz6setdte4jk2tntt55hpfdpz';
		// $txid = '6faef5d1c7a423b858a025605c176a8cc22f12a3d3ddbd02f3ef818320ebcbf4';

		// // // //BCH//
		// // $crypto = 'BCH';
		// // $label = 'usr_bsod666';
		// // $address = 'qztrk7m57450h65qffhjrd6ekaams3kas5ecpw6pzz';
		// // $txid = '53c0b56f1f46046d328666ba1e86897da8b88df1da259f4b8c3ed49b1fd08114';

		// // //DOGE//
		// // $crypto = 'DOGE';
		// // $label = 'usr_bsod666';
		// // $address = 'DKzRr2pUGLVQRe2Csr7Y1znDhGtB1eBZLw';
		// // $txid = '989b981221a1cc860d509a8ca3979f46fd222db8ec63a1bdf910ea1f39b94ac4';

		// //walletinfo
		// //$data = getconnection($crypto);
		// //$data = getconnection($crypto);
		// //$data = getestimatefee($crypto);  
		// //$data = getbalance($crypto, $label);
		// //$data = getaddress($crypto, $label); 
		// //$data = addCrypto($crypto, $label);
		// $data = listchannel($crypto, $label);
		// //$data = get_label_crypto($crypto, $address);
		// //$data = listransactionall($crypto, $label); 
		// //$data = listransaction($crypto, $label);
		// //$data = gettransaction_crypto($crypto, $txid);
		// //$data = dumpkey($crypto, $label);
		// //$data = getbalanceAll($crypto); 
		// dd($data);  
		// $datamsg = response()->json( 
		// 	 $data
		//  );
		// return $datamsg->content();
	}

	#################State #########################
	public function state(){
		$state = State::all();       
		$datamsg = response()->json( 
			 $state
		 );
		return $datamsg->content();
	}
		
	#################App version#########################
	public function appversion(){
		$appver = Appver::where('id',1)->first();
		$datamsg = response()->json([
			'version' => $appver->version,
			'iosversion' => $appver->ios_version,
			'iosversion2' => $appver->ios_version2
		]);
		return $datamsg->content();
	}
	
	#################Reset Wallet #########################
	public function reset_mnemonic(Request $request){
		$mnemonic = $request->mnemonic;   
		$user = User::where('mnemonic',$mnemonic)->first();
		
		if(isset($user)){
			$hash = $user->email_hash;
			$id = $user->id;
 
			  $email_msj = ucwords($user->username).'<p> We received a request to reset your DORADO wallet. Below is your username to login Dorado Wallet: <p></p>Username: '.$user->username.' <p></p>Please click link below.</p><p><a href="'.settings('url').'password/reset/'.$hash.'"  style="display: inline-block; padding: 11px 30px; margin: 20px 0px 30px; font-size: 15px; color: #fff; background: #4fc3f7; border-radius: 60px; text-decoration:none;">Reset Pasword</a></p>';
		  	  send_email_basic($user->email, 'DORADO', settings('infoemail'), 'DORADO Account Reset Wallet', $email_msj);  

			$msg = array(
				"display_msj"=>"Please check your inbox, to get back your Dorado Wallet.",
				"mesej"=>"jaya"
			);
			$datamsg = response()->json([
				'data' => $msg
			]);
		}
		else{	
			$msg = array("mesej"=>"No such user with this mnemonic.");
			$datamsg = response()->json([
				'data' => $msg
			]);
		}
		return $datamsg->content(); 
	}
	
	#################SecretPin #########################
	public function send_secretpin(Request $request){
		$user = User::where('id',$request->uid)->where('secretpin',$request->secretpin)->first();

		if($user){ 
			$tokenORI = apiToken($user->id);
			if($request->tokenAPI==$tokenORI){
				$msg = array("mesej"=>"jaya");
				$datamsg = response()->json([
					'data' => $msg
				]);
				return $datamsg->content();
			}
			else{
				$msg = array("mesej"=>"No Access");
				$datamsg = response()->json([
					'data' => $msg
				]);
				return $datamsg->content();
			}	
		}
		else{
			$msg = array("mesej"=>"Sorry, wrong secret pin.");
			$datamsg = response()->json([
				'data' => $msg
			]);
			return $datamsg->content();
		}
	}
	
	
	#################Login2 #########################
	public function login2(Request $request){
		$msg = array("mesej"=>"This service currently unavailable.");
		$datamsg = response()->json([
			'data' => $msg
		]);
		return $datamsg->content();
	}
	
	
	#################Login2 #########################
	public function login_back($uid){
		$user = User::where('id',$uid)->first();
		$tokenAPI = apiToken($user->id);
		$msg = array(
			"id"=>$user->id,
			"label"=>$user->label,
			"username"=>$user->username,
			"tokenAPI"=>$tokenAPI, 
		);
		$datamsg = response()->json([
			'data' => $msg
		]);
		return $datamsg->content();
	}

	
	#################Login #########################
	public function login(Request $request){
		$userData = '';
		$user = User::where('username',$request->username)->orWhere('email',$request->username)->first();

		if($user){
			if(!Hash::check($request->password, $user->password)){
				$msg = array("mesej"=>"Wrong Password.");
				$datamsg = response()->json([
					'data' => $msg
				]);
				return $datamsg->content();
			}
			elseif($user->status=='block'){
				$msg = array("mesej"=>"Your account was blocked. Please contact with administrator.");
				$datamsg = response()->json([
					'data' => $msg
				]);
				return $datamsg->content();
			}
			elseif($user->email_verify=='0'){
				$msg = array("mesej"=>"Please verify your email.");
				$datamsg = response()->json([
					'data' => $msg
				]);
				return $datamsg->content();
			}
			else{
				$tokenAPI = apiToken($user->id);
				$msg = array(
					"id"=>$user->id,
					"label"=>$user->label,
					"username"=>$user->username,
					"tokenAPI"=>$tokenAPI,
					"mesej"=>"jaya"
				);
				$datamsg = response()->json([
					'data' => $msg
				]);
				return $datamsg->content();
			} 
		}
		else{
			$msg = array("mesej"=>"User does not exist.");
			$datamsg = response()->json([
				'data' => $msg
			]);
			return $datamsg->content();
		}

	} 

	#################User registration#########################	 
	public function signup(Request $request) {
		$fullname = $request->fullname;
		$username = $request->username;
		$email = $request->email;
		$password = $request->password;
		$cpassword = $request->cpassword;
		$secretpin = $request->secretpin;
		$boxagree = $request->boxagree;
			  
		$secret_pin2 = preg_match('/^[0-9]{6}$/', $secretpin);
		$pword1 = preg_match("/[a-zA-Z0-9]/", $password); 
		$pword2 = preg_match("/[^\da-zA-Z]/", $password); 
	  
		if(strlen($username)<6) { echo '{"data":{"mesej":"Username must be more than 6 characters."}}'; }
		elseif(strlen($secretpin)!=6) { echo '{"data":{"mesej":"Secret PIN must be 6 digits."}}'; }
		elseif(!$secret_pin2) { echo '{"data":{"mesej":"Secret PIN must be digits only."}}'; }
		elseif(strlen($password)<8) { echo '{"data":{"mesej":"Password must be more than 8 characters."}}'; }
		elseif(!$pword1) { echo '{"data":{"mesej":"Password must have at least one symbol, one capital letter,one number, one letter."}}'; }
		elseif(!$pword2) { echo '{"data":{"mesej":"Password must have at least one symbol, one capital letter,one number, one letter."}}'; }
		elseif($password !== $cpassword) { echo '{"data":{"mesej":"Password does not match with password for confirmation."}}'; } 
		elseif($boxagree == false) { echo '{"data":{"mesej":"Please click checkbox for agree the term."}}'; } 
		else{  
			$userData = '';
			$mainCount = User::where('username',$username)->orWhere('email',$email)->count();
			$created=time();
			
			if($mainCount==0){
				$ga = new GoogleAuthenticator();	  
				$secret = $ga->createSecret();
		
				/*Inserting user values*/
				$hash = sha1($email);
				$email_msj = 'To activate your account, please click link below.<p><a href="'.settings('url').'verify/email/'.$hash.'"  style="display: inline-block; padding: 11px 30px; margin: 20px 0px 30px; font-size: 15px; color: #fff; background: #4fc3f7; border-radius: 60px; text-decoration:none;">Link Activate</a></p>';
				send_email_basic($email, 'DORADO', settings('infoemail'), 'DORADO Account Verification', $email_msj);
 
				$user = User::create([
					'name' => $fullname,
					'username' => $username,
					'secretpin' => $secretpin,
					'label' => 'usr_'.$username,
					'password' => bcrypt($password),
					'email' => $email,
					'email_hash' => $hash,
					'ip' => \Request::ip(),
					'google_auth_code' => $secret,
				]);

				$userData = User::where('email',$request->email)->first();
				$systemToken = apiToken($userData->id);

				$msg = array(
					"id"=>$userData->id, 
					"name"=>$userData->name,
					 "username"=>$userData->username, 
					 "secretpin"=>$userData->secretpin, 
					 "email"=>$userData->email,
					 "token"=>$systemToken,
					 'display_msj'=>'Registration Successfull!',
					 'mesej'=>'jaya'
				);

				$datamsg = response()->json([
					'data' => $msg
				]);
				return $datamsg->content();
			}
			else {					 
				$msg = array("mesej"=>"This username or email has already been used. Please enter another.");
				$datamsg = response()->json([
					'data' => $msg
				]);
				return $datamsg->content();
			}
		}		 
	}

	#################Change Password####################
	public function change_password(Request $request){
		$uid = $request->uid; 
		$pwordold = $request->pwordold;
		$pwordnew = $request->pwordnew;
		$pwordconfirm = $request->pwordconfirm;
		
		$user = User::where('id',$uid)->first();
		$pword1 = preg_match("/[a-zA-Z0-9]/", $request->pwordnew); 
		$pword2 = preg_match("/[^\da-zA-Z]/", $request->pwordnew);
		
			
		if($pwordnew!=$pwordconfirm){  
			$msg = array("mesej"=>"The password confirmation does not match");
			$datamsg = response()->json([
				'data' => $msg
			]);
		}
		else if(strlen($pwordnew)<8){  
			$msg = array("mesej"=>"Password must be more than 8 characters.");
			$datamsg = response()->json([
				'data' => $msg
			]);
		}
		else if(!$pword1){  
			$msg = array("mesej"=>"Password must have at least one symbol, one capital letter,one number, one letter.");
			$datamsg = response()->json([
				'data' => $msg
			]);
		}
		else if(!$pword2){  
			$msg = array("mesej"=>"Password must have at least one symbol, one capital letter,one number, one letter.");
			$datamsg = response()->json([
				'data' => $msg
			]);
		}
		else if(isset($user)){
			$tokenORI = apiToken($user->id);
			
			if($request->tokenAPI==$tokenORI){
				
				if(!Hash::check($pwordold, $user->password)){
					$msg = array("mesej"=>"Wrong Old Password");
					$datamsg = response()->json([
						'data' => $msg
					]);
				}
				else{
					User::whereId($uid)
						->update([
							'password' => bcrypt($pwordnew), 
						]);
					$msg = array("mesej"=>"Success, Password successfully changed.");
					$datamsg = response()->json([
						'data' => $msg
					]);
				}
			}
			else{
			 	$msg = array("mesej"=>"No Access");
				$datamsg = response()->json([ 
				'data' => $msg
				]);
			}
			
		}
		else{
			$msg = array("mesej"=>"User does not exist.");
			$datamsg = response()->json([
				'data' => $msg
			]);
		}
		return $datamsg->content();
	}

	#################Reset Password####################
	public function reset_password(Request $request){
		$email = $request->email;   
		$user = User::where('email',$email)->first();
		
		if(isset($user)){
			$hash = $user->email_hash;
			$id = $user->id;
 
			$email_msj = $user->username.'<p> We received a request to reset your DORADO password, please click link below.</p><p><a href="'.settings('url').'password/reset/'.$hash.'"  style="display: inline-block; padding: 11px 30px; margin: 20px 0px 30px; font-size: 15px; color: #fff; background: #4fc3f7; border-radius: 60px; text-decoration:none;">Reset Pasword</a></p>';
		  	send_email_basic($user->email, 'DORADO', settings('infoemail'), 'DORADO Account Reset Password', $email_msj); 
		 
			$msg = array(
				"display_msj"=>"Email with instructions to reset password was sent. Please check your inbox.",
				"mesej"=>"jaya"
			);
			$datamsg = response()->json([
				'data' => $msg
			]);
		}
		else{	
			$msg = array("mesej"=>"No such user with this email address.");
			$datamsg = response()->json([
				'data' => $msg
			]);
		}
		return $datamsg->content();
	}

	#################Resend email####################
	public function resendemail(Request $request){
		$email = $request->email;
		$username = $request->username;
		$users = User::where('username',$username)->orWhere('email',$email)->first();
		
		if($email == '' && $username==''){
			$msg = array("mesej"=>"Please fill Username or Email");
			$datamsg = response()->json([
				'data' => $msg
			]);		
		}
		else if(isset($users) && $users->email_verify=='0'){
			$hash = $users->email_hash;
			$email_msj = 'Hai {{username}} <p> To activate your account, please click link below.</p><p> <a href="{{url}}"  style="display: inline-block; padding: 11px 30px; margin: 20px 0px 30px; font-size: 15px; color: #fff; background: #4fc3f7; border-radius: 60px; text-decoration:none;">Link Activate</a></p>';
			$replace1 = str_replace("{{username}}",$username,$email_msj); 
			$replace2 = str_replace("{{url}}",settings('url').'verify/email/'.$hash , $replace1);
		 	$email = $users->email;
			send_email_basic($email, 'DORADO', settings('infoemail'), 'DORADO Account Verification', $replace2);
		 
			$msg = array(
				"mesej"=>"jaya",
				"display_msj"=>"The email has been resend to $email .Please verify the email to Activate yout account"
			);
			$datamsg = response()->json([
				'data' => $msg
			]);
		}
		elseif(isset($users) && $users->email_verify=='1'){ 
			$msg = array(
				"mesej"=>"jaya",
				"display_msj"=>"Your email has been verified. You can login now."
			);
			$datamsg = response()->json([
				'data' => $msg
			]);
		}
		else{
			$msg = array("mesej"=>"Email or Username does not exist");
			$datamsg = response()->json([
				'data' => $msg
			]);
		}
		return $datamsg->content();
	}


	#################Private Key #########################
	public function userPrivateKey($crypto,$uid,$tokenAPI){
		$userData = '';
		$user = User::where('id',$uid)->first();
		
		if($user){
			$tokenORI = apiToken($uid);
			if($tokenAPI==$tokenORI){
				$data = dumpkey($crypto, $user->label);   
				$datamsg = response()->json([
					'mesej' => "jaya",
					'info' => $data
				]);
			}
			else{ 
				$datamsg = response()->json([
					'mesej' => 'No Access'
				]);
			}
		}
		else{ 
			$datamsg = response()->json([
				'mesej' => 'User does not exist'
			]);
		}
		return $datamsg->content();		 
	} 

	 
	#################Update Power SecretPin #########################
	public function send_powerpin(Request $request){	
		$user = User::where('id',$request->uid)->first();
		
		if($user){  
			$tokenORI = apiToken($request->uid);
			if($request->tokenAPI==$tokenORI){
				if($request->power_pin=='true'){
					$power = '1';
				}else{
					$power = '0';
				}

				$upt = User::findorFail($user->id);
				$upt->power_pin = $power;
				$upt->save();

				$msg = array(
					"display_msj"=>'Successfully update', 
					"mesej"=>"jaya"
				); 
				$datamsg = response()->json([
					'data' => $msg
				]);
				return $datamsg->content(); 
			}
			else{
				$msg = array("mesej"=>"No Access");
				$datamsg = response()->json([
					'data' => $msg
				]);
				return $datamsg->content();
			}
		}
		else{
			$msg = array("mesej"=>"User does not exist.");
			$datamsg = response()->json([
				'data' => $msg
			]);
			return $datamsg->content();
		}
	}

	 
	#################Update Power 2FA #########################
	public function send_powerauth(Request $request){	
		$user = User::where('id',$request->uid)->first();
		
		if($user){  
			$tokenORI = apiToken($request->uid);
			if($request->tokenAPI==$tokenORI){
				if($request->power_auth=='true'){
					$power = '1';
				}else{
					$power = '0';
				}

				$upt = User::findorFail($user->id);
				$upt->power_auth = $power;
				$upt->save();

				$msg = array(
					"display_msj"=>'Successfully update', 
					"mesej"=>"jaya"
				); 
			  	$datamsg = response()->json([
					'data' => $msg
			   	]);
			   return $datamsg->content(); 
			}
			else{
				$msg = array("mesej"=>"No Access");
				$datamsg = response()->json([
					'data' => $msg
				]);
				return $datamsg->content();
			}
		}
		else{
			$msg = array("mesej"=>"User does not exist.");
			$datamsg = response()->json([
				'data' => $msg
			]);
			return $datamsg->content();
		}
	}

	 
	#################Update Power FingerPrint #########################
	public function send_powerfp(Request $request){	
		$user = User::where('id',$request->uid)->first();
		
		if($user){  
			$tokenORI = apiToken($request->uid);
			if($request->tokenAPI==$tokenORI){
				if($request->power_fp=='true'){
					$power = '1';
				}else{
					$power = '0';
				}

				$upt = User::findorFail($user->id);
				$upt->power_fp = $power;
				$upt->save();

				$msg = array(
					"display_msj"=>'Successfully update', 
					"mesej"=>"jaya"
				); 
			   	$datamsg = response()->json([
				   	'data' => $msg
			   	]);
			   return $datamsg->content(); 
			}
			else{
				$msg = array("mesej"=>"No Access");
				$datamsg = response()->json([
					'data' => $msg
				]);
				return $datamsg->content();
			}
		}
		else{
			$msg = array("mesej"=>"User does not exist.");
			$datamsg = response()->json([
				'data' => $msg
			]);
			return $datamsg->content();
		}
	}

	 
	#################Update Secretpin #########################
	public function edit_secretpin(Request $request){	
		$user = User::where('id',$request->uid)->first();
		
		if($user){  
			$tokenORI = apiToken($request->uid);
			if($request->tokenAPI==$tokenORI){ 
				$secret_pin2 = preg_match('/^[0-9]{6}$/', $request->secretpin);

				if(strlen($request->secretpin)!=6) { 
					$msg = array( 
						"mesej"=>"Secret PIN must be 6 digits."
					); 
					$datamsg = response()->json([
						'data' => $msg
					]);  
				}
				else if(!$secret_pin2) {
					$msg = array( 
						"mesej"=>"Secret PIN must be digits only."
					); 
					$datamsg = response()->json([
						'data' => $msg
					]); 
				}
				else{ 
					$upt = User::findorFail($user->id);
					$upt->secretpin = $request->secretpin;
					$upt->save();

					$msg = array(
						"display_msj"=>'Successfully update', 
						"mesej"=>"jaya"
					); 
					$datamsg = response()->json([
						'data' => $msg
					]);
				}
			   return $datamsg->content(); 
			}
			else{
				$msg = array("mesej"=>"No Access");
				$datamsg = response()->json([
					'data' => $msg
				]);
				return $datamsg->content();
			}
		}
		else{
			$msg = array("mesej"=>"User does not exist.");
			$datamsg = response()->json([
				'data' => $msg
			]);
			return $datamsg->content();
		}
	} 

	
	#################User Info2 #########################
	public function userInfo2(Request $request){
		$msg = array("mesej"=>"This service currently unavailable.");
		$datamsg = response()->json([
			'data' => $msg
		]);
		return $datamsg->content();
	}


	#################User Info #########################
	public function userInfo(Request $request){
		$userData = '';
		$user = User::where('id',$request->uid)->first();
		
		if($user){  
			$tokenORI = apiToken($request->uid);
			if($request->tokenAPI==$tokenORI){
				$city = Currency::where('id',$user->currency)->first();
			 	$msg = array(
			 		"id"=>$user->id,
			 		"name"=>$user->name,
			 		"email"=>$user->email,
			 		"username"=>$user->username,
			 		"secretpin"=>$user->secretpin,
			 		"google_auth_code"=>$user->google_auth_code,
			 		"power_pin"=>$user->power_pin,
			 		"power_auth"=>$user->power_auth,
			 		"power_fp"=>$user->power_fp,
					"mnemonic" => $user->mnemonic,
			 		"mesej"=>"jaya"
			 	); 
				$datamsg = response()->json([
					'data' => $msg
				]);
				return $datamsg->content(); 
			}
			else{
				$msg = array("mesej"=>"No Access");
				$datamsg = response()->json([
					'data' => $msg
				]);
				return $datamsg->content();
			}	
		}
		else{
			$msg = array("mesej"=>"User does not exist.");
			$datamsg = response()->json([
				'data' => $msg
			]);
			return $datamsg->content();
		}
	}  
		 
	#################Dashboard#########################
	public function dashboard($userid,$tokenAPI){
		$jumMYR = 0; $bilCrypto = 0;
		$user = User::where('id',$userid)->first(); 
		if($user){
			$tokenORI = apiToken($userid);
			if($tokenAPI==$tokenORI){
				$priceapi = PriceCrypto::where('appear','1')->get();
				$currency = Currency::where('id',$user->currency)->first();
				$results = array();
				 
				foreach($priceapi as $row){
					$wallet = WalletAddress::where('uid',$user->id)->where('crypto',$row['crypto'])->first();
					if($wallet){  
						$json_string = settings('url_gecko').'simple/price?ids='.$row["id_gecko"].'&vs_currencies='.strtolower($currency->code);
						$jsondata = file_get_contents($json_string);
						$obj = json_decode($jsondata, TRUE); 
					
						$price = $obj[$row["id_gecko"]][strtolower($currency->code)];
						
						//$jumCrypto = str_replace("\n","",getbalance($row['crypto'],$user->label)/100000000); 

						if($row['crypto']=='LND'){
						$dipCryptoSAT = str_replace("\n","",getbalance_lndlnd($user->label)) + str_replace("\n","",getbalance_lndbtc($user->label));
						$dipCrypto = disply_convert('SAT','BTC',$dipCryptoSAT);
						}else{
						$dipCrypto = str_replace("\n","",getbalance($row['crypto'],$user->label)/100000000); 
						}

						if($dipCrypto<=0){ $displyCrypto = 0;  }else{ $displyCrypto = number_format($dipCrypto, 8, '.', ''); } 	
						
						$myrCrypto = number_format($displyCrypto * $price, 2, '.', '');  
						$addressCrypto = getaddress($row['crypto'], $user->label); 
						$feesCrypto = getestimatefee($row['crypto']) + number_format(strval(settings('commission_withdraw')/$price), 8, '.', '');
						
						if($row['crypto']=='LND'){
						$totaldis = disply_convert('BTC',$wallet->value_display,$displyCrypto);
						}else{
						$totaldis = disply_convert($row['crypto'],$wallet->value_display,$displyCrypto);
						}

						$results[] = array(
							'idwallet' => $wallet->id,
							'title' => $wallet->title, 
							'price' => $price, 
							'imgCrypto' => $row['url_img'], 
							'nameCrypto' => $row['name'], 
							'crypto' => $row['crypto'], 
							'balance' => $displyCrypto,  
							'myrBalance' => $myrCrypto, 
							'addressCrypto' => $addressCrypto, 
							'feesCrypto' => number_format($feesCrypto, 8, '.', ''),  
							'value_display' => $wallet->value_display,  
							'totaldis' => number_format($totaldis, 8, '.', '')
						);	
						$jumMYR = $jumMYR + $myrCrypto;
						$bilCrypto++;
					}
					$totalMYR = number_format($jumMYR,'2'); 
				} 
			 
				$datamsg = response()->json([
					'totalMYR' => $totalMYR,
					'bilCrypto' => $bilCrypto,
					'currency' => $currency->code,
					'info' => $results,
					'uid' => $user->id,
					'email' => $user->email,
					'username' => $user->username,
					'fullname' => $user->name,
					'label' => $user->label,
					"power_pin"=>$user->power_pin,
					"power_auth"=>$user->power_auth,
					"power_fp"=>$user->power_fp,
					'mnemonic' => $user->mnemonic,
					'mesej' => 'jaya',
				]);
				
			}
			else{
				$datamsg = response()->json([ 
				'mesej' => 'No Access',
				]);
			}
		}
		else{
			$datamsg = response()->json([ 
			'mesej' => 'User does not exist',
			]);
		}
		return $datamsg->content();
	}
		 
	#################Dashboard#########################
	public function dash_view($crypto,$userid,$tokenAPI){
		$jumMYR = 0; $bilCrypto = 0;
		$user = User::where('id',$userid)->first(); 

		if($user){
			$tokenORI = apiToken($userid);
			if($tokenAPI==$tokenORI){
				$priceapi = PriceCrypto::where('crypto',$crypto)->first();
				$currency = Currency::where('id',$user->currency)->first();
				$wallet = WalletAddress::where('uid',$userid)->where('crypto',$crypto)->first();
				   
				$json_string = settings('url_gecko').'simple/price?ids='.$priceapi->id_gecko.'&vs_currencies='.strtolower($currency->code);
				$jsondata = file_get_contents($json_string);
				$obj = json_decode($jsondata, TRUE); 
					
				$price = $obj[$priceapi->id_gecko][strtolower($currency->code)];		
				//$jumCrypto = str_replace("\n","",getbalance($priceapi->crypto,$user->label)/100000000); 
 
				if($crypto=='LND'){
					$dipCryptoLNDSAT = str_replace("\n","",getbalance_lndlnd($user->label));
					$dipCryptoLND = disply_convert('SAT','BTC',$dipCryptoLNDSAT);

					$dipCryptoSAT = str_replace("\n","",getbalance_lndbtc($user->label));
					$dipCrypto = disply_convert('SAT','BTC',$dipCryptoSAT);
				}else{
					$dipCryptoLND = 0;
					$dipCrypto = str_replace("\n","",getbalance($priceapi->crypto,$user->label)/100000000); 
				}
					
				if($dipCrypto<=0){ $displyCrypto = 0; $displyCryptoLND = 0; }else{ $displyCrypto = number_format($dipCrypto, 8, '.', ''); $displyCryptoLND = number_format($dipCryptoLND, 8, '.', ''); } 	
						
				$myrCrypto = number_format($displyCrypto * $price, 2, '.', '');  
				$myrCryptoLND = number_format($displyCryptoLND * $price, 2, '.', '');  

				$addressCrypto = getaddress($priceapi->crypto, $user->label);   
				$feesCryptORI = number_format(getestimatefee($priceapi->crypto) + settings('commission_withdraw')/$price, 8, '.', '');
				 
				if($crypto=='LND'){
				$totaldis = number_format(disply_convert('BTC',$wallet->value_display,$displyCrypto), 8, '.', '');
				$totaldisLND = number_format(disply_convert('BTC',$wallet->value_display,$displyCryptoLND), 8, '.', '');
				$feesCrypto = number_format(disply_convert('BTC',$wallet->value_display,$feesCryptORI), 8, '.', '');
				$totalbalance = number_format($totaldis + $totaldisLND, 8, '.', '');
				$totalmyrBalance = number_format($myrCryptoLND + $myrCrypto, 2, '.', '');
				}else{
				$totaldis = number_format(disply_convert($crypto,$wallet->value_display,$displyCrypto), 8, '.', '');
				$totaldisLND = number_format(disply_convert($crypto,$wallet->value_display,$displyCryptoLND), 8, '.', '');
				$feesCrypto = number_format(disply_convert($crypto,$wallet->value_display,$feesCryptORI), 8, '.', '');
				$totalbalance = number_format($totaldisLND + $totaldis, 8, '.', '');
				$totalmyrBalance = number_format($myrCryptoLND + $myrCrypto, 2, '.', '');
				}

				if($crypto=='LND'){
					$local_fund = disply_convert('SAT',$wallet->value_display,20000);
					$remote_fund = disply_convert('SAT',$wallet->value_display,0);
				}else{
					$local_fund = 0;
					$remote_fund = 0;
				}
			 
				$datamsg = response()->json([  
					'currency' => $currency->code,
					'price' => $priceapi->price,
					'address' => $addressCrypto,
					'imgCrypto' => $priceapi->url_img,
					'nameCrypto' => $priceapi->name,
					'feesCrypto' => $feesCrypto,
					'balance' => $displyCrypto,
					'myrBalance' => $myrCrypto,
					'balanceLND' => $displyCryptoLND,
					'myrBalanceLND' => $myrCryptoLND,
					'uid' => $user->id,
					'email' => $user->email,
					'username' => $user->username,
					'fullname' => $user->name,
					'label' => $user->label,
					'power_pin' =>$user->power_pin,
					'power_auth' =>$user->power_auth,
					'power_fp' =>$user->power_fp,
					'mnemonic' => $user->mnemonic,
					'value_display' => $wallet->value_display,
					'totaldis' => number_format($totaldis, 8, '.', ''),
					'totaldisLND' => number_format($totaldisLND, 8, '.', ''),
					'totalbalance' => $totalbalance,
					'totalmyrBalance' => $totalmyrBalance,
					'localfund_app' => round($local_fund, 8).' '.$wallet->value_display, //20000SAT
					'remotefund_app' => round($remote_fund, 8).' '.$wallet->value_display, //0SAT
					'mesej' => 'jaya',
				]);
				
			}
			else{
				$datamsg = response()->json([ 
				'mesej' => 'No Access',
				]);
			}
		}
		else{
			$datamsg = response()->json([ 
			'mesej' => 'User does not exist',
			]);
		}
		return $datamsg->content();
	}
		 
	#################Dashboard Older#########################
	public function dashboardOLD($userid,$tokenAPI){
		$user = User::where('id',$userid)->first(); 

		if($user){
			$tokenORI = apiToken($userid);
			if($tokenAPI==$tokenORI){ 
				$priceBTC = PriceCrypto::where('crypto','BTC')->first();
				$priceBCH = PriceCrypto::where('crypto','BCH')->first();
				$priceDOGE = PriceCrypto::where('crypto','DOGE')->first();
		
				$jumBTC = str_replace("\n","",getbalance('BTC',$user->label)/100000000);
				$jumBCH = str_replace("\n","",getbalance('BCH',$user->label)/100000000);
				$jumDOGE = str_replace("\n","",getbalance('DOGE',$user->label)/100000000);
			  
				if($jumBTC<=0){ $totalBTC = 0; }else{ $totalBTC = $jumBTC; }
				if($jumBCH<=0){ $totalBCH = 0; }else{ $totalBCH = $jumBCH; }
				if($jumDOGE<=0){ $totalDOGE = 0; }else{ $totalDOGE = $jumDOGE; }		
				 
				$myrBTC = number_format($totalBTC * $priceBTC->price, 2, '.', '');
				$myrBCH = number_format($totalBCH * $priceBCH->price, 2, '.', '');
				$myrDOGE = number_format($totalDOGE * $priceDOGE->price, 2, '.', ''); 
				$totalMYR = number_format($myrBTC + $myrBCH + $myrDOGE, 2, '.', '');
 
				$addressBTC = getaddress('BTC', $user->label); 
				$addressBCH = getaddress('BCH', $user->label);
				$addressDOGE = getaddress('DOGE', $user->label);

				$feesBTC = number_format(getestimatefee('BTC') + settings('commission_withdraw')/$priceBTC->price, 8, '.', '');
				$feesBCH = number_format(getestimatefee('BCH') + settings('commission_withdraw')/$priceBCH->price, 8, '.', '');
				$feesDOGE = number_format(getestimatefee('DOGE') + settings('commission_withdraw')/$priceDOGE->price, 8, '.', '');
				
				$datamsg = response()->json([
					'totalMYR' => $totalMYR,
					'priceBTC' => number_format($priceBTC->price,'2'),
					'priceDOGE' => number_format($priceDOGE->price,'2'),
					'priceBCH' => number_format($priceBCH->price,'2'),
					'totalBTC' => number_format($totalBTC,'8'),
					'totalBCH' => number_format($totalBCH,'8'),
					'totalDOGE' => number_format($totalDOGE,'8'),
					'myrBTC' => number_format($myrBTC,'2'),
					'myrBCH' => number_format($myrBCH,'2'),
					'myrDOGE' => number_format($myrDOGE,'2'),
					'addressBTC' => $addressBTC,
					'addressBCH' => $addressBCH,
					'addressDOGE' => $addressDOGE,
					'feesBTC' => $feesBTC,
					'feesBCH' => $feesBCH,
					'feesDOGE' => $feesDOGE,
					'uid' => $user->id,
					'email' => $user->email,
					'username' => $user->username,
					'fullname' => $user->name,
					'label' => $user->label,
					"power_pin"=>$user->power_pin,
					"power_auth"=>$user->power_auth,
					"power_fp"=>$user->power_fp,
					'mesej' => 'jaya',
				]);
				
			}
			else{
				$datamsg = response()->json([ 
				'mesej' => 'No Access',
				]);
			}
		}
		else{
			$datamsg = response()->json([ 
			'mesej' => 'User does not exist',
			]);
		}
		return $datamsg->content();
	}
	
	
	#################Update Currency #########################
	public function update_currency(Request $request){  
		$currency = Currency::all();
		$user = User::where('id',$request->uid)->first();
        
        if($user){
			$tokenORI = apiToken($user->id);		  
			if($request->tokenAPI==$tokenORI){
				if($currency){ 
					$upt = User::findorFail($user->id);
					$upt->currency = $request->currency;
					$upt->save();
					
					$datamsg = response()->json([ 
						'mesej' => 'jaya',
						'info' => $currency,
						'currency' => $upt->currency,
						'display_msj' => 'Successfully update!',
					]);   
				}else{
					$datamsg = response()->json([ 
						'mesej' => 'Currency failed',
						'info' => null,
						'currency' => $user->currency,
					]);
				}
			}
			else{
				$datamsg = response()->json([ 
					'mesej' => 'No Access',
					'info' => null,
					'currency' => $user->currency,
				]);	
			}
        }
        else{
            $datamsg = response()->json([ 
				'mesej' => 'User does not exist',
				'info' => null,
				'currency' => '',
			]);
		}
		return $datamsg->content();
	}
	
	
	#################Currency #########################
	public function getcurrency($uid,$tokenAPI){  
		$currency = Currency::all();
		$user = User::where('id',$uid)->first();
        
        if($user){
			$tokenORI = apiToken($user->id);		  
			if($tokenAPI==$tokenORI){
				if($currency){  
					$datamsg = response()->json([ 
						'mesej' => 'jaya',
						'info' => $currency,
						'currency' => $user->currency,
					]);   
				}
				else{
					$datamsg = response()->json([ 
						'mesej' => 'Currency failed',
						'info' => null,
						'currency' => $user->currency,
					]);
				}
			}
			else{
				$datamsg = response()->json([ 
					'mesej' => 'No Access',
					'info' => null,
					'currency' => $user->currency,
				]);	
			}
        }
        else{
            $datamsg = response()->json([ 
				'mesej' => 'User does not exist',
				'info' => null,
				'currency' => '',
			]);
		}
		return $datamsg->content();
	}
	
	
	#################Crypto #########################
	public function getcrypto($uid,$tokenAPI){  
		$user = User::where('id',$uid)->first();
		$results = null;
        
        if($user){
			$tokenORI = apiToken($user->id);		  
			if($tokenAPI==$tokenORI){
				$priceapi = PriceCrypto::where('appear','1')->get();
				
				foreach($priceapi as $row){
					$wallet = WalletAddress::where('uid',$user->id)->where('crypto',$row['crypto'])->first();
					if(!$wallet){
					$results[] = array('crypto' =>$row['crypto'],'name' => strtoupper($row['name']),'imgCrypto' =>$row['url_img']);	
					}
				}
				$json2 = json_encode($results);
				$json = json_decode($json2);
				   
				$datamsg = response()->json([ 
					'mesej' => 'jaya',
					'info' => $json,
				]);   
			}
			else{
				$datamsg = response()->json([ 
					'mesej' => 'No Access',
					'info' => null,
				]);	
			}
        }
        else{
            $datamsg = response()->json([ 
				'mesej' => 'User does not exist',
				'info' => null,
			]);
		}
		
		return $datamsg->content();
	}

	 
	#################Add Crypto #########################
	public function create_asset(Request $request){	
		$user = User::where('id',$request->uid)->first();

		if($user){  
			$tokenORI = apiToken($request->uid);
			if($request->tokenAPI==$tokenORI){
				$pcrypto = PriceCrypto::where('crypto',$request->crypto)->where('appear','1')->first();
				if($pcrypto){
				$wallet = WalletAddress::where('label',$user->label)->where('crypto',$request->crypto)->first();
				if(!$wallet){
					$crypto = $request->crypto; 
					$address = addCrypto($crypto, $user->label);
				
					$wallAddress = new WalletAddress;
					$wallAddress->uid = $user->id;
					$wallAddress->label = $user->label;
					$wallAddress->address = $address; 
					$wallAddress->balance = '0.00000000';
					$wallAddress->crypto = $crypto;
					$wallAddress->title = $pcrypto->name;
					$wallAddress->value_display = $pcrypto->value_display;
					$wallAddress->save();
					
					getbalance($crypto, $user->label);
					getaddress($crypto, $user->label);
				}

					$msg = array(
						"display_msj"=>'Successfully Create', 
						"mesej"=>"jaya"
					); 
					$datamsg = response()->json([
						'data' => $msg
					]);
					return $datamsg->content(); 

				}else{
				$msg = array("mesej"=>"Wallet does not exist");
				$datamsg = response()->json([
					'data' => $msg
				]);
				return $datamsg->content();
				}

			}
			else{
				$msg = array("mesej"=>"No Access");
				$datamsg = response()->json([
					'data' => $msg
				]);
				return $datamsg->content();
			}
		}
		else{
			$msg = array("mesej"=>"User does not exist.");
			$datamsg = response()->json([
				'data' => $msg
			]);
			return $datamsg->content();
		}
	}

	 
	#################Rename Crypto #########################
	public function rename_asset(Request $request){	
		$user = User::where('id',$request->uid)->first();

		if($user){  
			$tokenORI = apiToken($request->uid);
			if($request->tokenAPI==$tokenORI){
				$pcrypto = PriceCrypto::where('crypto',$request->crypto)->where('appear','1')->first();
				if($pcrypto){
				$wallet = WalletAddress::where('label',$user->label)->where('crypto',$request->crypto)->where('id',$request->idwallet)->first();
					if(!$wallet){
						$msg = array("mesej"=>"Wallet does not exist");
						$datamsg = response()->json([
							'data' => $msg
						]);
						return $datamsg->content();
					}else{
						if(strlen($request->title)>15) { 
							$msg = array("mesej"=>"Title must be less than 15 characters.");
							$datamsg = response()->json([
								'data' => $msg
							]);
							return $datamsg->content(); 
						}
						$upt = WalletAddress::findorFail($wallet->id);
						$upt->title = ucwords($request->title);
						$upt->save();

						$msg = array(
							"display_msj"=>'Successfully Update', 
							"mesej"=>"jaya"
						); 
						$datamsg = response()->json([
							'data' => $msg
						]);
						return $datamsg->content(); 
					}

				}else{
				$msg = array("mesej"=>"Wallet does not exist");
				$datamsg = response()->json([
					'data' => $msg
				]);
				return $datamsg->content();
				}

			}
			else{
				$msg = array("mesej"=>"No Access");
				$datamsg = response()->json([
					'data' => $msg
				]);
				return $datamsg->content();
			}
		}
		else{
			$msg = array("mesej"=>"User does not exist.");
			$datamsg = response()->json([
				'data' => $msg
			]);
			return $datamsg->content();
		}
	}
	
	
	#################Remarks Transaction #########################
	public function remark_trans($txid,$crypto,$uid,$tokenAPI){  
		$withdraw = Withdrawal::where('txid',$txid)->where('crypto',$crypto)->first();
		$user = User::where('id',$uid)->first();
        
        if($user){
			$tokenORI = apiToken($user->id);		  
			if($tokenAPI==$tokenORI){
				if($withdraw){ 
					$datamsg = response()->json([ 
						'mesej' => 'jaya',
						'remarks' => $withdraw->remarks,
					]);   
				}
				else{
					$datamsg = response()->json([ 
						'mesej' => 'Withdraw info does not exist',
						'remarks' => '',
					]);
				}
			}
			else{
				$datamsg = response()->json([ 
					'mesej' => 'No Access',
					'remarks' => '',
				]);	
			}
        }
        else{
            $datamsg = response()->json([ 
				'mesej' => 'User does not exist',
				'remarks' => '',
			]);
		}
		return $datamsg->content();
	}
	
	
	#################Transaction #########################
	public function transaction($crypto,$usr_crypto,$tokenAPI){ 
		$user = User::where('label',$usr_crypto)->first();
		$currency = Currency::where('id',$user->currency)->first();
		$priCrypto = PriceCrypto::where('crypto',$crypto)->first(); 
		$trans = listransaction($crypto,$usr_crypto,strtolower($currency->code),$priCrypto->id_gecko);
      
        if($user){
			$tokenORI = apiToken($user->id);		  
			if($tokenAPI==$tokenORI){
				$datamsg = response()->json([ 
					'mesej' => 'jaya',
					'info' => $trans,
				]);
			}
			else{
				$datamsg = response()->json([ 
					'mesej' => 'No Access',
				]);	
			}
		}
		else{
            $datamsg = response()->json([ 
				'mesej' => 'User does not exist',
			]);
        }
		return $datamsg->content();
	}
	
	
	#################Max Crypto #########################
	public function maxCrypto($crypto,$uid){
		$priceApi = PriceCrypto::where('crypto',$crypto)->first();		
		$user = User::where('id',$uid)->first(); 
		$currency = Currency::where('id',$user->currency)->first();
		$wallet = WalletAddress::where('uid',$user->id)->where('crypto',$crypto)->first();
 
		$json_string = settings('url_gecko').'simple/price?ids='.$priceApi->id_gecko.'&vs_currencies='.strtolower($currency->code);
		$jsondata = file_get_contents($json_string);
		$obj = json_decode($jsondata, TRUE); 
		$price = $obj[$priceApi->id_gecko][strtolower($currency->code)];
		
		$comm_fee = number_format(settings('commission_withdraw')/$price, 8, '.', '');
		$net_fee = getestimatefee($crypto);
		
		if($user){

			if($crypto=='LND'){
				$userbalance = number_format(getbalance($crypto, $user->label), 8, '.', '');
				$userbalanceBTC = disply_convert('SAT','BTC',$userbalance);
				$fee = number_format($comm_fee+$net_fee, 8, '.', '');
				$maxDraw =  number_format($userbalanceBTC - $fee, 8, '.', '');  
				$disCrypto = disply_convert('BTC',$wallet->value_display,$maxDraw);
			}else{
				$userbalance = number_format(getbalance($crypto, $user->label)/100000000, 8, '.', '');
				$fee = number_format($comm_fee+$net_fee, 8, '.', '');
				$maxDraw =  number_format($userbalance - $fee, 8, '.', ''); 
				$disCrypto = disply_convert($crypto,$wallet->value_display,$maxDraw);
			}
 
			if($maxDraw<=0){ $maxWithdraw =0; $displyCrypto = 0; }
			else{ 
				$maxWithdraw =$maxDraw; 
				$displyCrypto = number_format($disCrypto, 8, '.', '');
			}

			$priceWithdraw = $maxWithdraw*$price;
			$datamsg = response()->json([
				'mesej' => 'jaya',
				"totalMyr"=>number_format($priceWithdraw, 2, '.', ''),
				"totalCrypto"=>$displyCrypto
			]);
		}
		else{
			$datamsg = response()->json([ 
				'mesej' => 'User does not exist',
			]);
		}
		return $datamsg->content();	
	}
	
	#################Send Crypto #########################
	public function sendCrypto(Request $request){ 
		$crypto = $request->crypto;
		$amountDis = $request->amountcrypto;
		$label = $request->sendfrom; 
		$recipient = $request->sendto;
		$remarks = $request->remarks;
		$secretpin = $request->secretpin;
	 
		$useruid = User::where('label',$label)->first();   
		$priceApi = PriceCrypto::where('crypto',$crypto)->first(); 	 
		$currency = Currency::where('id',$useruid->currency)->first();  
				   
		$json_string = settings('url_gecko').'simple/price?ids='.$priceApi->id_gecko.'&vs_currencies='.strtolower($currency->code);
		$jsondata = file_get_contents($json_string);
		$obj = json_decode($jsondata, TRUE); 
		$price = $obj[$priceApi->id_gecko][strtolower($currency->code)];

		$amountset = 0.01;
		$minwithdraw = number_format($amountset/$price, 8, '.', '');
		
		if(!isset($useruid)){
		 	$msg = array("mesej"=>"Id Sender does not exist!");
			$datamsg = response()->json([
				'data' => $msg
			]);
		 	return $datamsg->content();
		}
		else if(isset($useruid) && $useruid->secretpin!=$secretpin){
		 	$msg = array("mesej"=>"Wrong Secret Pin!");
			$datamsg = response()->json([
				'data' => $msg
			]);
		 	return $datamsg->content();	 
		}
		else if(checkAddress($crypto, $recipient)!=true){
			$msg = array("mesej"=>'Invalid Address');
			$datamsg = response()->json([
				'data' => $msg
			]);
			 return $datamsg->content();
		 }
		else{
			$wuserF = WalletAddress::where('uid',$useruid->id)->where('crypto',$crypto)->first();
			if(!isset($wuserF)){
				$m = $crypto.' for user does not exist!';
			 	$msg = array("mesej"=>$m);
				$datamsg = response()->json([
					'data' => $msg
				]);
			 	return $datamsg->content();
			}else{
				$minwithdrawDis = disply_convert($crypto,$wuserF->value_display,$minwithdraw);
				if($amountDis<=$minwithdrawDis){
					$m = 'Minimum withdraw must more than '.$minwithdrawDis;
					 $msg = array("mesej"=>$m);
					$datamsg = response()->json([
						'data' => $msg
					]);
					 return $datamsg->content();	
				}
			}

		}			 
		 
		$wallet = WalletAddress::where('uid',$useruid->id)->where('crypto',$crypto)->first();

		$amount = number_format(disply_convert($wuserF->value_display,$crypto,$amountDis), 8, '.', '');

		$wuserF = getaddress($crypto,$label); 
		$comm_fee = number_format(settings('commission_withdraw')/$price, 8, '.', '');
		$net_fee = getestimatefee($crypto);
		
		$fee = number_format($comm_fee+$net_fee, 8, '.', '');
		$userbalance = number_format(getbalance($crypto, $label)/100000000, 8, '.', '');
		$totalfunds = number_format($amount + $fee, 8, '.', '');
		$after_bal =  number_format($userbalance - $totalfunds, 8, '.', ''); 
		
		$tokenORI = apiToken($useruid->id); 
		if($request->tokenAPI==$tokenORI){
			
			if($userbalance < $totalfunds) {   
				$msg = array("mesej"=>"Sorry, you do not have enough funds for withdrawal!");
				$datamsg = response()->json([
					'data' => $msg
				]);
				return $datamsg->content();
			}
			else{
				$crypto_txid = sendtoaddressRAW($crypto, $label, $recipient, $amount, 'withdraw', $comm_fee);   
				$myr_amount = $amount*$price;
					 
				if($crypto_txid==''){ //failed withdraw
					$withdraw = new Withdrawal;
					$withdraw->uid = $useruid->id;
					$withdraw->status = 'failed';
					$withdraw->amount= $amount; 
					$withdraw->before_bal = $userbalance;
					$withdraw->after_bal = $after_bal; 
					$withdraw->recipient = $recipient;
					$withdraw->netfee = $net_fee; 
					$withdraw->walletfee = $comm_fee; 
					$withdraw->txid = $crypto_txid;
					$withdraw->crypto = $crypto;
					$withdraw->remarks = $remarks;
					$withdraw->currency = $useruid->currency;
					$withdraw->rate = number_format($price, 2, '.', '');
					$withdraw->myr_amount = number_format($myr_amount, 2, '.', '');
					$withdraw->type = 'external';
					$withdraw->save();
					  
					$msg = array("mesej"=>"Failed widthdraw!");
					$datamsg = response()->json([
						'data' => $msg
					]);
					return $datamsg->content();
				}
				else{ //success withdraw
					$withdraw = new Withdrawal;
					$withdraw->uid = $useruid->id;
					$withdraw->status = 'success';
					$withdraw->amount = $amount; 
					$withdraw->before_bal = $userbalance;
					$withdraw->after_bal = $after_bal;
					$withdraw->recipient = $recipient;
					$withdraw->netfee = $net_fee; 
					$withdraw->walletfee = $comm_fee; 
					$withdraw->txid = $crypto_txid;
					$withdraw->crypto = $crypto;
					$withdraw->remarks = $remarks;
					$withdraw->currency = $useruid->currency;
					$withdraw->rate = number_format($price, 2, '.', '');
					$withdraw->myr_amount = number_format($myr_amount, 2, '.', '');
					$withdraw->type = 'external';
					$withdraw->save();
						 
					$msg = array(
						"mesej"=>"jaya",
						"display_msj"=>'Successfully withdraw. Amount '.$amount .' '.$crypto .' was sent to '.$recipient
					);
					$datamsg = response()->json([
						'data' => $msg
					]);
					return $datamsg->content(); 
				}
			}// end send /move crypto	
		}
		else{
			$msg = array("mesej"=>"No Access");
			$datamsg = response()->json([
				'data' => $msg
			]);	
			return $datamsg->content();
		}	 
	}
	
	 
	#################Convert#########################
	public function convert(Request $request){	
		$sat = 100000000;
		
		$priceApi = PriceCrypto::where('crypto',$request->crypto)->first();		
		$user = User::where('id',$request->uid)->first(); 
		$currency = Currency::where('id',$user->currency)->first();
		$wallet = WalletAddress::where('uid',$request->uid)->where('crypto',$request->crypto)->first();
	
		$json_string = settings('url_gecko').'simple/price?ids='.$priceApi->id_gecko.'&vs_currencies='.strtolower($currency->code);
		$jsondata = file_get_contents($json_string);
		$obj = json_decode($jsondata, TRUE); 
		$priceCrypto = $obj[$priceApi->id_gecko][strtolower($currency->code)];
	 
		if($request->crypto=='LND'){
			if($request->type=='crypto'){ 
				$convetDis = disply_convert($wallet->value_display,'BTC',$request->nilai);
				$amount = number_format($convetDis, 8, '.', '');
				$jum = number_format($amount*$priceCrypto, 2, '.', '');  
			}
			else{
				$amount = number_format($request->nilai/$priceCrypto, 8, '.', '');
				$convetDis = disply_convert('BTC',$wallet->value_display,$amount);
				$jum = number_format($convetDis, 8, '.', '');
			}
		}
		else{
			if($request->type=='crypto'){ 
				$convetDis = disply_convert($wallet->value_display,$request->crypto,$request->nilai);
				$amount = number_format($convetDis, 8, '.', '');
				$jum = number_format($amount*$priceCrypto, 2, '.', '');  
			}
			else{
				$amount = number_format($request->nilai/$priceCrypto, 8, '.', '');
				$convetDis = disply_convert($request->crypto,$wallet->value_display,$amount);
				$jum = number_format($convetDis, 8, '.', ''); 
			}
		}
		
		$msg = array("mesej"=>"jaya","display_msj"=>$jum);
		$datamsg = response()->json([ 
			'data' => $msg
		]); 
		return $datamsg->content();	
	}

	#################Transaction Lightning#########################
	public function transactionLND($crypto,$uid,$tokenAPI){
		$user = User::where('id',$uid)->first();
		$trans = null;
		
        if($user){
			$tokenORI = apiToken($user->id);		  
			if($tokenAPI==$tokenORI){ 
				$wallet = WalletAddress::where('uid',$uid)->where('crypto',$crypto)->first();
				if($wallet){
					$transX = TransLND::where('uid',$uid)->orderBy('id','desc')->get();

					foreach($transX as $tran){
						$totaldis = disply_convert('SAT',$wallet->value_display,$tran->amount);
						$totaldisAfter = disply_convert('SAT',$wallet->value_display,$tran->after_bal);

						$currency = Currency::where('id',$tran->currency)->first()->code;
						$totalfees = $tran->netfee + $tran->walletfee;
						$trans[] = array(
							'uid' => $tran->uid,
							'type' => $tran->type,
							'category' => $tran->category,
							'using' => $tran->using,
							'status' => $tran->status,
							'invoice_id' => $tran->invoice_id,
							'amount' => $tran->amount,
							'totaldis' => number_format($totaldis, 8, '.', '').' '.$wallet->value_display,
							'before_bal' => $tran->before_bal,
							'after_bal' => $tran->after_bal,
							'totaldisAfter' => number_format($totaldisAfter, 8, '.', '').' '.$wallet->value_display,
							'myr_amount' => $tran->myr_amount,
							'remarks' => $tran->remarks,
							'rate' => $tran->rate,
							'currency' => $currency,
							'recipient' => $tran->recipient,
							'netfee' => $tran->netfee,
							'walletfee' => $tran->walletfee,
							'totalfees' => $totalfees,
							'txid' => $tran->txid,
							'error_code' => $tran->error_code,
							'created_at' => date('Y-m-d h:i:s', strtotime($tran->created_at)),
						); 
					}

					$datamsg = response()->json([ 
						'mesej' => 'jaya',
						'info' => $trans,
					]);
				}
				else{
					$datamsg = response()->json([ 
						'mesej' => 'Lightning user does not exist',
					]);
				}
			}
			else{
				$datamsg = response()->json([ 
					'mesej' => 'No Access',
				]);	
			}
		}
		else{
            $datamsg = response()->json([ 
				'mesej' => 'User does not exist',
			]);
        }
        return $datamsg->content();
	}

	#################Transaction Invoice Lightning#########################
	public function transactionInvLND($crypto,$uid,$tokenAPI){
		$user = User::where('id',$uid)->first();
        
        if($user){
			$tokenORI = apiToken($user->id);		  
			if($tokenAPI==$tokenORI){ 
				$wallet = WalletAddress::where('uid',$uid)->where('crypto',$crypto)->first();
				if($wallet){
					$invoice = InvoiceLND::where('uid',$uid)->orderBy('id','desc')->get();
					$trans_inv = array();

					foreach($invoice as $data){
						$nilconv = disply_convert('SAT',$wallet->value_display,$data->amount);

						$trans_inv[] = array(
							"id" => $data->id,
							"uid" => $data->uid,
							"hash" => $data->hash,
							"amount" => $data->amount,
							"amountDis" => $nilconv.' '.$wallet->value_display,
							"expired" => $data->expired,
							"date_expired" => $data->date_expired,
							"memo" => $data->memo,
							"status" => $data->status,
							"txid" => $data->txid,
							"created_at" => $data->created_at,
							"updated_at" => $data->updated_at,
						);
					}

					$datamsg = response()->json([ 
						'mesej' => 'jaya',
						'info' => $trans_inv,
					]);
				}else{
					$datamsg = response()->json([ 
						'mesej' => 'Lightning user does not exist',
					]);
				}
			}
			else{
				$datamsg = response()->json([ 
					'mesej' => 'No Access',
				]);	
			}
		}
		else{
            $datamsg = response()->json([ 
				'mesej' => 'User does not exist',
			]);
        }
        return $datamsg->content();
	}

	#################Create Invoice#########################
	public function create_inv(Request $request){
		$user = User::where('id',$request->uid)->first();
        
        if($user){
			$tokenORI = apiToken($user->id);		  
			if($request->tokenAPI==$tokenORI){
				$invcreate = receivelightning001($user->label, $request->amount, $request->memo, $request->expired);
				if(array_key_exists("error", $invcreate)){
					$datamsg = response()->json([ 
						'mesej' => $invcreate['error'],
					]);
			 		return $datamsg->content(); 
				}
				$newHash = $invcreate['payment_request'];

				$ins = new InvoiceLND;
				$ins->uid = $user->id;
				$ins->hash = $newHash;
				$ins->amount = $request->amount;
				$ins->expired = $request->expired;
				$ins->date_expired = date_format(Carbon::now()->addHours($request->expired),"Y-m-d H:i:s");
				$ins->memo = $request->memo;
				$ins->save();

				$datamsg = response()->json([ 
					'mesej' => 'jaya',
					'display_msj' => 'Successfully Create',
				]);
			}
			else{
				$datamsg = response()->json([ 
					'mesej' => 'No Access',
				]);	
			}
		}
		else{
            $datamsg = response()->json([ 
				'mesej' => 'User does not exist',
			]);
        }
        return $datamsg->content();
	}
	
	#################Send Crypto LND TO LND #########################
	public function sendLND(Request $request){ 
		$crypto = $request->crypto;
		$label = $request->sendfrom; 
		$recipient = $request->sendto;
		$remarks = $request->remarks;
		$secretpin = $request->secretpin;
		$sat = 100000000;
	 
		$useruid = User::where('label',$label)->first();  
		
		if(!isset($useruid)){
			$msg = array("mesej"=>"Id Sender does not exist!");
		   $datamsg = response()->json([
			   'data' => $msg
		   ]);
			return $datamsg->content();
	   	}

		$priceApi = PriceCrypto::where('crypto',$crypto)->first(); 	 
		$currency = Currency::where('id',$useruid->currency)->first();

		$invdet = getInvoiceDet($recipient);
		
		if(array_key_exists("error", $invdet)){
			$msg = array("mesej"=>"Invalid Invoice!");
			$datamsg = response()->json([
				'data' => $msg
			]);
		 	return $datamsg->content(); 
		}
		
		$amount = $invdet['num_satoshis'];  //in sat	   
		$json_string = settings('url_gecko').'simple/price?ids='.$priceApi->id_gecko.'&vs_currencies='.strtolower($currency->code);
		$jsondata = file_get_contents($json_string);
		$obj = json_decode($jsondata, TRUE); 
		$price = $obj[$priceApi->id_gecko][strtolower($currency->code)];

		if(isset($useruid) && $useruid->secretpin!=$secretpin){
		 	$msg = array("mesej"=>"Wrong Secret Pin!");
			$datamsg = response()->json([
				'data' => $msg
			]);
		 	return $datamsg->content();	 
		}
		else{
			$wuserF = WalletAddress::where('uid',$useruid->id)->where('crypto',$crypto)->first();
			if(!isset($wuserF)){
				$m = $crypto.' for user does not exist!';
			 	$msg = array("mesej"=>$m);
				$datamsg = response()->json([
					'data' => $msg
				]);
			 	return $datamsg->content();
			}
		 }			 
		 
		$userbalance = number_format(getbalance_lndlnd($label), 8, '.', ''); // in sat
		$totalfunds = number_format($amount, 8, '.', ''); // in sat
		$after_bal =  number_format($userbalance - $totalfunds, 8, '.', '');  // in sat
		 
		$tokenORI = apiToken($useruid->id); 
		if($request->tokenAPI==$tokenORI){
			
			if($userbalance < $totalfunds) {   
				$msg = array("mesej"=>"Sorry, you do not have enough funds for withdrawal!");
				$datamsg = response()->json([
					'data' => $msg
				]);
				return $datamsg->content();
			}
			else{
				$crypto_txid = paymentlightning003($useruid->label, $recipient);
				$myr_amount = ($amount/$sat)*$price;
	 
				if($crypto_txid=='' || array_key_exists("error", $crypto_txid) || array_key_exists("payment_error", $crypto_txid)){ //failed withdraw
					if(array_key_exists("error", $crypto_txid)){
						$error = $crypto_txid['error'];
					}
					else{
						$error = $crypto_txid['payment_error'];
					}

					$withdraw = new TransLND;
					$withdraw->uid = $useruid->id;
					$withdraw->status = 'failed';
					$withdraw->error_code = $error;
					$withdraw->amount= $totalfunds; 
					$withdraw->before_bal = $userbalance;
					$withdraw->after_bal = $after_bal; 
					$withdraw->recipient = $recipient;
					$withdraw->netfee = 0; 
					$withdraw->walletfee = 0; 
					$withdraw->invoice_id = '0';
					$withdraw->type = 'external';
					$withdraw->using = 'mobile';
					$withdraw->category = 'send';
					$withdraw->remarks = $remarks;
					$withdraw->currency = $useruid->currency;
					$withdraw->rate = number_format($price, 2, '.', '');
					$withdraw->myr_amount = number_format($myr_amount, 2, '.', '');
					$withdraw->save();
					  
					WalletAddress::where('uid', $useruid->id)->where('crypto', $crypto)
						->update([
							'balance' => number_format($after_bal, 8, '.', ''), 
					]);

					$msg = array("mesej"=>$error);
					$datamsg = response()->json([
						'data' => $msg
					]);
					return $datamsg->content();
				}
				else{ //success withdraw
					$withdraw = new TransLND;
					$withdraw->uid = $useruid->id;
					$withdraw->status = 'success';
					$withdraw->amount= $totalfunds; 
					$withdraw->before_bal = $userbalance;
					$withdraw->after_bal = $after_bal;
					$withdraw->recipient = $recipient;
					$withdraw->txid = $crypto_txid;
					$withdraw->netfee = 0; 
					$withdraw->walletfee = 0; 
					$withdraw->invoice_id = '0';
					$withdraw->type = 'external';
					$withdraw->using = 'mobile';
					$withdraw->category = 'send';
					$withdraw->remarks = $remarks;
					$withdraw->currency = $useruid->currency;
					$withdraw->rate = number_format($price, 2, '.', '');
					$withdraw->myr_amount = number_format($myr_amount, 2, '.', ''); 
					$withdraw->save();

					WalletAddress::where('uid', $useruid->id)->where('crypto', $crypto)
					->update([
						'balance' => number_format($after_bal, 8, '.', ''), 
					]);
					
					$msg = array(
						"mesej"=>"jaya",
						"display_msj"=>'Successfully withdraw. Amount '.$amount .' '.$crypto .' was sent to '.$recipient
					);
					$datamsg = response()->json([
						'data' => $msg
					]);
					return $datamsg->content(); 
				} 
				
			}// end send /move crypto	
		}
		else{
			$msg = array("mesej"=>"No Access");
			$datamsg = response()->json([
				'data' => $msg
			]);	
			return $datamsg->content();
		}	 
	}
	
	//	
	#################Send Crypto LND TO Bitcoin #########################
	public function sendLNDBTC(Request $request){ 
		$crypto = $request->crypto;
		$cryptoSend = 'BTC';
		$label = $request->sendfrom; 
		$recipient = $request->sendto;
		$amountDis = $request->amountcrypto;
		$secretpin = $request->secretpin; 
		$sat = 100000000;
	 
		$useruid = User::where('label',$label)->first();   

		if(!isset($useruid)){
			$msg = array("mesej"=>"Id Sender does not exist!");
		   $datamsg = response()->json([
			   'data' => $msg
		   ]);
			return $datamsg->content();
	   	}
	    
		$priceApi = PriceCrypto::where('crypto',$crypto)->first(); 	 
		$currency = Currency::where('id',$useruid->currency)->first();
				   
		$json_string = settings('url_gecko').'simple/price?ids='.$priceApi->id_gecko.'&vs_currencies='.strtolower($currency->code);
		$jsondata = file_get_contents($json_string);
		$obj = json_decode($jsondata, TRUE); 
		$price = $obj[$priceApi->id_gecko][strtolower($currency->code)];

		if(isset($useruid) && $useruid->secretpin!=$secretpin){
		 	$msg = array("mesej"=>"Wrong Secret Pin!");
			$datamsg = response()->json([
				'data' => $msg
			]);
		 	return $datamsg->content();	 
		}
		else if(checkAddress($cryptoSend, $recipient)!=true){
			$msg = array("mesej"=>'Invalid Address');
			$datamsg = response()->json([
				'data' => $msg
			]);
			 return $datamsg->content();
		 }
		else{
			$wuserF = WalletAddress::where('uid',$useruid->id)->where('crypto',$crypto)->first();
			if(!isset($wuserF)){
				$m = $crypto.' for user does not exist!';
			 	$msg = array("mesej"=>$m);
				$datamsg = response()->json([
					'data' => $msg
				]);
			 	return $datamsg->content();
			}
		}

		$wallet = WalletAddress::where('uid',$useruid->id)->where('crypto',$crypto)->first();
		$amount = disply_convert($wallet->value_display,'SAT',$amountDis); // in sat

		$userbalance = number_format(getbalance_lndbtc($label), 8, '.', ''); // in sat
		$satfees = number_format(getestimatefee($crypto)*$sat, 8, '.', ''); // in sat
		$totalfunds = number_format($amount + $satfees, 8, '.', ''); // in sat
		$after_bal =  number_format($userbalance - $totalfunds, 8, '.', '');  // in sat
		$remarks = 'REFUND_LND';
		 
		$tokenORI = apiToken($useruid->id); 
		if($request->tokenAPI==$tokenORI){
			
			if($userbalance < $totalfunds) {   
				$msg = array("mesej"=>"Sorry, you do not have enough funds for withdrawal!");
				$datamsg = response()->json([
					'data' => $msg
				]);
				return $datamsg->content();
			}
			else{
				$crypto_txid = refundlightning001($label, $amount, $recipient);
				$myr_amount = ($amount/$sat)*$price;
	 
				if($crypto_txid=='' || array_key_exists("error", $crypto_txid) || array_key_exists("payment_error", $crypto_txid)){ //failed withdraw
					if(array_key_exists("error", $crypto_txid)){
						$error = $crypto_txid['error'];
					}
					else{
						$error = $crypto_txid['payment_error'];
					}

					$withdraw = new TransLND;
					$withdraw->uid = $useruid->id;
					$withdraw->status = 'failed';
					$withdraw->error_code = $error;
					$withdraw->amount= number_format($amount, 8, '.', ''); 
					$withdraw->before_bal = $userbalance;
					$withdraw->after_bal = $after_bal; 
					$withdraw->recipient = $recipient; 
					$withdraw->netfee = $satfees; 
					$withdraw->walletfee = 0; 
					$withdraw->invoice_id = '0';
					$withdraw->crypto = 'BTC';
					$withdraw->type = 'external';
					$withdraw->using = 'mobile';
					$withdraw->category = 'send';
					$withdraw->remarks = $remarks;
					$withdraw->currency = $useruid->currency;
					$withdraw->rate = number_format($price, 2, '.', '');
					$withdraw->myr_amount = number_format($myr_amount, 2, '.', '');
					$withdraw->save();
					  
					WalletAddress::where('uid', $useruid->id)->where('crypto', $crypto)
					->update([
						'balance' => number_format($after_bal, 8, '.', ''), 
					]);

					$msg = array("mesej"=>$error);
					$datamsg = response()->json([
						'data' => $msg
					]);
					return $datamsg->content();
				}
				else{ //success withdraw 

					$withdraw = new TransLND;
					$withdraw->uid = $useruid->id;
					$withdraw->status = 'success';
					$withdraw->amount= number_format($amount, 8, '.', ''); 
					$withdraw->before_bal = $userbalance;
					$withdraw->after_bal = $after_bal;
					$withdraw->recipient = $recipient;
					$withdraw->txid = $crypto_txid['txid'];
					$withdraw->netfee = $satfees; 
					$withdraw->walletfee = 0; 
					$withdraw->invoice_id = '0';
					$withdraw->crypto = 'BTC';
					$withdraw->type = 'external';
					$withdraw->using = 'mobile';
					$withdraw->category = 'send';
					$withdraw->remarks = $remarks;
					$withdraw->currency = $useruid->currency;
					$withdraw->rate = number_format($price, 2, '.', '');
					$withdraw->myr_amount = number_format($myr_amount, 2, '.', ''); 
					$withdraw->save();

					WalletAddress::where('uid', $useruid->id)->where('crypto', $crypto)
						->update([
							'balance' => number_format($after_bal, 8, '.', ''), 
					]);
					
					$msg = array(
						"mesej"=>"jaya",
						"display_msj"=>'Successfully withdraw. Amount '.$amountDis .' '.$wallet->value_display .' was sent to '.$recipient
					);
					$datamsg = response()->json([
						'data' => $msg
					]);
					return $datamsg->content(); 
				} 	
			}// end send /move crypto	
		}
		else{
			$msg = array("mesej"=>"No Access");
			$datamsg = response()->json([
				'data' => $msg
			]);	
			return $datamsg->content();
		}	 
	}
	
	#################Send BTC to LND #########################
	public function sendBTCLND(Request $request){ 
		$crypto = $request->crypto;
		$amountDis = $request->amountcrypto;
		$label = $request->sendfrom; 
		$recipient = $request->sendto;
		$secretpin = $request->secretpin;
	 
		$useruid = User::where('label',$label)->first();   
		$priceApi = PriceCrypto::where('crypto',$crypto)->first(); 	 
		$currency = Currency::where('id',$useruid->currency)->first();
				   
		$json_string = settings('url_gecko').'simple/price?ids='.$priceApi->id_gecko.'&vs_currencies='.strtolower($currency->code);
		$jsondata = file_get_contents($json_string);
		$obj = json_decode($jsondata, TRUE); 
		$price = $obj[$priceApi->id_gecko][strtolower($currency->code)];

		$amountset = 0.01;
		$minwithdraw = number_format($amountset/$price, 8, '.', '');
	
		if(!isset($useruid)){
		 	$msg = array("mesej"=>"Id Sender does not exist!");
			$datamsg = response()->json([
				'data' => $msg
			]);
		 	return $datamsg->content();
		}
		else if(isset($useruid) && $useruid->secretpin!=$secretpin){
		 	$msg = array("mesej"=>"Wrong Secret Pin!");
			$datamsg = response()->json([
				'data' => $msg
			]);
		 	return $datamsg->content();	 
		}
		else{
			$wuserF = WalletAddress::where('uid',$useruid->id)->where('crypto',$crypto)->first();
			if(!isset($wuserF)){
				$m = $crypto.' for user does not exist!';
			 	$msg = array("mesej"=>$m);
				$datamsg = response()->json([
					'data' => $msg
				]);
			 	return $datamsg->content();
			}else{
				$minwithdrawDis = disply_convert('BTC',$wuserF->value_display,$minwithdraw);
				if($amountDis<=$minwithdrawDis){
					$m = 'Minimum withdraw must more than '.$minwithdraw;
					$msg = array("mesej"=>$m);
					$datamsg = response()->json([
						'data' => $msg
					]);
					return $datamsg->content();	
				}
			}

		 }			 
		
		$wallet = WalletAddress::where('uid',$useruid->id)->where('crypto',$crypto)->first();
		$amount = disply_convert($wuserF->value_display,'SAT',$amountDis); // in sat

		$wuserF = getaddress($crypto,$label); 
		$comm_fee = number_format(settings('commission_withdraw')/$price, 8, '.', '');
		$net_fee = getestimatefee($crypto);
		$remarks = 'FUND_LND';
		
		$fee = number_format(($comm_fee+$net_fee)*100000000, 8, '.', ''); // in sat
		$userbalance = number_format(getbalance($crypto, $label), 8, '.', ''); // in sat
		$totalfunds = number_format($amount + $fee, 8, '.', ''); // in sat
		$after_bal =  number_format($userbalance - $totalfunds, 8, '.', '');  // in sat
		
		$tokenORI = apiToken($useruid->id); 
		if($request->tokenAPI==$tokenORI){
			
			if($userbalance < $totalfunds) {   
				$msg = array("mesej"=>"Sorry, you do not have enough funds for withdrawal!");
				$datamsg = response()->json([
					'data' => $msg
				]);
				return $datamsg->content();
			}
			else{
				$crypto_txid = fundlightning001($crypto, $label, $recipient, $amount, $remarks, $comm_fee);   
				$myr_amount = ($amount/100000000)*$price;
					 
				if($crypto_txid==''){ //failed withdraw
					$withdraw = new Withdrawal;
					$withdraw->uid = $useruid->id;
					$withdraw->status = 'failed';
					$withdraw->amount= number_format($amount/100000000, 8, '.', ''); 
					$withdraw->before_bal = number_format($userbalance/100000000, 8, '.', '');
					$withdraw->after_bal = number_format($after_bal/100000000, 8, '.', ''); 
					$withdraw->recipient = $recipient;
					$withdraw->netfee = $net_fee; 
					$withdraw->walletfee = $comm_fee; 
					$withdraw->txid = $crypto_txid;
					$withdraw->crypto = 'BTC';
					$withdraw->remarks = $remarks;
					$withdraw->currency = $useruid->currency;
					$withdraw->rate = number_format($price, 2, '.', '');
					$withdraw->myr_amount = number_format($myr_amount, 2, '.', '');
					$withdraw->type = 'external';
					$withdraw->save();
					  
					$msg = array("mesej"=>"Failed widthdraw!");
					$datamsg = response()->json([
						'data' => $msg
					]);
					return $datamsg->content();
				}
				else{ //success withdraw
					$withdraw = new Withdrawal;
					$withdraw->uid = $useruid->id;
					$withdraw->status = 'success';
					$withdraw->amount= number_format($amount/100000000, 8, '.', ''); 
					$withdraw->before_bal = number_format($userbalance/100000000, 8, '.', '');
					$withdraw->after_bal = number_format($after_bal/100000000, 8, '.', ''); 
					$withdraw->recipient = $recipient;
					$withdraw->netfee = $net_fee; 
					$withdraw->walletfee = $comm_fee; 
					$withdraw->txid = $crypto_txid;
					$withdraw->crypto = 'BTC';
					$withdraw->remarks = $remarks;
					$withdraw->currency = $useruid->currency;
					$withdraw->rate = number_format($price, 2, '.', '');
					$withdraw->myr_amount = number_format($myr_amount, 2, '.', '');
					$withdraw->type = 'external';
					$withdraw->save();
						 
					$msg = array(
						"mesej"=>"jaya",
						"display_msj"=>'Successfully refill. Amount '.$amount .' '.$crypto .' was sent to '.$recipient
					);
					$datamsg = response()->json([
						'data' => $msg
					]);
					return $datamsg->content(); 
				}
			}// end send /move crypto	
		}
		else{
			$msg = array("mesej"=>"No Access");
			$datamsg = response()->json([
				'data' => $msg
			]);	
			return $datamsg->content();
		}	 
	}
	
	 	
	#################List Channel #########################
	public function list_channel(Request $request){ 
		//afaa5899c0f876337a491c7e9555750a58fe671de4de12a236f121eba0a591a2 23
		//6b961d85d18af62a785cd5f3811d7a742eaad56fcfa8e20cbb50be0f0701476a 9
		$uid = $request->uid;
		$crypto = $request->crypto;
		$token = $request->tokenAPI;

		$user = User::where('id',$uid)->first();
        
        if($user){
			$tokenORI = apiToken($user->id);		  
			if($token==$tokenORI){
				if($crypto != 'LND'){
					$datamsg = response()->json([ 
						'mesej' => 'Channel Only Available for Lightning Blockchain',
					]);	
				}
				else{
					$trans = listchannel($crypto, $user->label); 
					$datamsg = response()->json([ 
						'mesej' => 'jaya',
						'info' => $trans,
					]);
				}
			}
			else{
				$datamsg = response()->json([ 
					'mesej' => 'No Access',
				]);	
			}   
        }
        else{
            $datamsg = response()->json([ 
				'mesej' => 'User does not exist',
				]);
        }
        return $datamsg->content();
    }
		
	#################Add Channel #########################
	public function create_channel(Request $request){
		$sat = 100000000; 
		$uid = $request->uid;
		$peers = $request->peer;
		$crypto = $request->crypto;

		$useruid = User::where('id',$uid)->first();

		if(!isset($useruid)){
			$msg = array("mesej"=>"Id Sender does not exist!");
		   $datamsg = response()->json([
			   'data' => $msg
		   ]);
			return $datamsg->content();
	   	} 
		else{
			$tokenORI = apiToken($useruid->id); 
			if($request->tokenAPI!=$tokenORI){
				$msg = array("mesej"=>"No Access");
				$datamsg = response()->json([
					'data' => $msg
				]);	
				return $datamsg->content();
			}
		}

		$wallet = WalletAddress::where('uid',$useruid->id)->first();
		$priceApi = PriceCrypto::where('crypto',$crypto)->first(); 	 
		$currency = Currency::where('id',$useruid->currency)->first();
		
		$localsat = disply_convert($wallet->value_display,'SAT',$request->localF);
		$pushsat = disply_convert($wallet->value_display,'SAT',$request->remoteF);
				   
		$json_string = settings('url_gecko').'simple/price?ids='.$priceApi->id_gecko.'&vs_currencies='.strtolower($currency->code);
		$jsondata = file_get_contents($json_string);
		$obj = json_decode($jsondata, TRUE); 
		$price = $obj[$priceApi->id_gecko][strtolower($currency->code)];

		$peerExp = preg_match("/[a-zA-Z0-9]@[a-zA-Z0-9]/", $peers);

		$net_fee = number_format(getestimatefee('BTC')*$sat, 8, '.', ''); // in sat
		$amount = $localsat+$pushsat;
		$userbalance = number_format(getbalance_lndbtc($label), 8, '.', ''); // in sat
		$totalfunds = number_format($amount + $net_fee, 8, '.', ''); // in sat
		$after_bal =  number_format($userbalance - $totalfunds, 8, '.', '');  // in sat
		$myr_amount = ($amount/$sat)*$price; 

		if($pushsat > $localsat){
			$msg = array("mesej"=>"Remote Funding must less than Local Funding");
			$datamsg = response()->json([
				'data' => $msg
			]);	
			return $datamsg->content();
		}
		else if(!$peerExp){
			$msg = array("mesej"=>"Please make sure Peer like as '<public_key>@<ip_address>:<port_number>' or '<public_key>' ");
			$datamsg = response()->json([
				'data' => $msg
			]);	
			return $datamsg->content();
		}
		else if($userbalance < $totalfunds){
			$msg = array("mesej"=>"Insufficient balance to perform transaction. Got ".$userbalance." SAT. Required ".$totalfunds." SAT.");
			$datamsg = response()->json([
				'data' => $msg
			]);	
			return $datamsg->content();
		}
		else{
			$crypto_txid = openchanlightning001($peers, $localsat, $pushsat);
 
			if($crypto_txid=='' || array_key_exists("error", $crypto_txid)){
				$error = $crypto_txid['error'];

				$msg = array("mesej"=>"jaya","mesej"=>$error);
				$datamsg = response()->json([
					'data' => $msg
				]);	
				return $datamsg->content();
			}
			else{
				$withdraw = new TransLND;
				$withdraw->uid = $useruid->id;
				$withdraw->status = 'success';
				$withdraw->amount = $amount; 
				$withdraw->before_bal = $userbalance;
				$withdraw->after_bal = $after_bal;
				$withdraw->recipient = $peers;
				$withdraw->txid = $crypto_txid;
				$withdraw->netfee = $net_fee; 
				$withdraw->walletfee = 0.00000000; 
				$withdraw->invoice_id = '0';
				$withdraw->type = 'external';
				$withdraw->using = 'mobile';
				$withdraw->category = 'open';
				$withdraw->remarks = 'OPEN_CHANNEL';
				$withdraw->currency = $useruid->currency;
				$withdraw->rate = number_format($price, 2, '.', '');
				$withdraw->myr_amount = number_format($myr_amount, 2, '.', ''); 
				$withdraw->save();

				$msg = array("mesej"=>"jaya","mesej"=>"Successfull Create!");
				$datamsg = response()->json([
					'data' => $msg
				]);	
				return $datamsg->content();
			}

		}
	}
	
	
	#################Close Channel #########################
	public function close_channel(Request $request){ 
		$uid = $request->uid; 
		$idHash = $request->idHash;
		$crypto = $request->crypto;
		$useruid = User::where('id',$uid)->first();

		if(!isset($useruid)){
			$msg = array("mesej"=>"Id Sender does not exist!");
		   	$datamsg = response()->json([
			   'data' => $msg
		   	]);
			return $datamsg->content();
	   	} 
		else{
			$tokenORI = apiToken($useruid->id); 
			if($request->tokenAPI!=$tokenORI){
				$msg = array("mesej"=>"No Access");
				$datamsg = response()->json([
					'data' => $msg
				]);	
				return $datamsg->content();
			}
		}

		$priceApi = PriceCrypto::where('crypto',$crypto)->first(); 	 
		$currency = Currency::where('id',$useruid->currency)->first();
	 
		$json_string = settings('url_gecko').'simple/price?ids='.$priceApi->id_gecko.'&vs_currencies='.strtolower($currency->code);
		$jsondata = file_get_contents($json_string);
		$obj = json_decode($jsondata, TRUE); 
		$price = $obj[$priceApi->id_gecko][strtolower($currency->code)];
 
		$crypto_txid = closechanlightning001($idHash);

		if($crypto_txid=='' || array_key_exists("error", $crypto_txid)){
			$error = $crypto_txid['error'];

			$msg = array("mesej"=>"jaya","mesej"=>$error);
			$datamsg = response()->json([
				'data' => $msg
			]);	
			return $datamsg->content();
		}
		else{
			$msg = array("mesej"=>"jaya","mesej"=>"Pending Closed!");
			$datamsg = response()->json([
				'data' => $msg
			]);	
			return $datamsg->content();
		}
	} 
	 

	#################send transaction xlsx mail #########################
	public function mail_transaction(Request $request){ 
		$user = User::where('id',$request->uid)->first();
		$trans = null; 

		if($request->sendto=='' || $request->mesej=='' || $request->subject==''){
			$datamsg = response()->json([ 
				'mesej' => 'All field a required',
			]); 
        	return $datamsg->content();
		}

        if($user){
			$tokenORI = apiToken($user->id);		  
			if($request->tokenAPI==$tokenORI){ 
				$wallet = WalletAddress::where('uid',$request->uid)->where('crypto',$request->crypto)->first();
				if($wallet){
					$userid = $request->uid;
					$walletid = $wallet->id;
					$crypto = $request->crypto;
					$file_name = 'transaction'.$crypto.'_'.time().'.xlsx';
					$sendto = $request->sendto;
					$subject = $request->subject;
					$msj = $request->mesej;

					if($crypto=='LND'){ 
						$val_crypto = 'SAT'; 
					 
						$trans_data = TransLND::where('uid',$userid)->orderBy('id','desc')->get();
						$customer_array[] = array('Category', 'Status', 'Error Code', 'Invoice', 'Txid', 'Amount', 'Balance', 'Rate', 'Fees', 'Remarks', 'Created At');
						foreach($trans_data as $data)
						{
							$fees = $data->netfee + $data->walletfee;
							$currency = Currency::where('id',$data->currency)->first();

							$customer_array[] = array(
							'Category'  => $data->category,
							'Status'   => $data->status,
							'Error Code'    => $data->error_code,
							'Invoice'  => $data->recipient,
							'Txid'   => $data->txid,
							'Amount'   => $data->amount.' '.$val_crypto,
							'Balance'   => $data->after_bal,
							'Rate'   => $data->rate.' '.$currency->code,
							'Fees'   => $fees,
							'Remarks'   => $data->remarks,
							'Created At'   => $data->created_at
							);
						}
					}else{ 
						$val_crypto = $crypto; 

						$trans_data = TransUser::where('uid',$userid)->where('crypto',$crypto)->orderBy('id','desc')->get();
						$customer_array[] = array('Category', 'Status', 'Error Code', 'Recipient', 'Txid', 'Amount', 'Balance', 'Rate', 'Fees', 'Remarks', 'Created At');
						foreach($trans_data as $data)
						{
							$fees = $data->netfee + $data->walletfee;
							$currency = Currency::where('id',$data->currency)->first();

							$customer_array[] = array(
							'Category'  => $data->category,
							'Status'   => $data->status,
							'Error Code'    => $data->error_code,
							'Invoice'  => $data->recipient,
							'Txid'   => $data->txid,
							'Amount'   => $data->amount.' '.$val_crypto,
							'Balance'   => $data->after_bal,
							'Rate'   => $data->rate.' '.$currency->code,
							'Fees'   => $fees,
							'Remarks'   => $data->remarks,
							'Created At'   => $data->created_at
							);
						}
					
					}

					$export = new TransExport($customer_array);

					Excel::store($export, $file_name, 'public');	
			
					$storage_path = 'app/public/'.$file_name;

					$data = array('messages'=>$msj);
					Mail::send('mail', $data, function($message) use($storage_path,$sendto,$subject,$user) {
						$message->to($sendto, 'Users')->subject
						($subject);
						$message->attach(storage_path($storage_path)); 
						$message->from($user->email,ucwords($user->name));
					});
					unlink(storage_path($storage_path));

					$datamsg = response()->json([ 
						'mesej' => 'jaya',
						'display_msj' => 'Send Successfully',
					]);
				}
				else{
					$datamsg = response()->json([ 
						'mesej' => 'Lightning user does not exist',
					]);
				}
			}
			else{
				$datamsg = response()->json([ 
					'mesej' => 'No Access',
				]);	
			}
		}
		else{
            $datamsg = response()->json([ 
				'mesej' => 'User does not exist',
			]);
        }
        return $datamsg->content();
	}

	
	#################send KEYS xlsx mail #########################
	public function mail_keys(Request $request){ 
		$user = User::where('id',$request->uid)->first();
		$trans = null; 

		if($request->sendto=='' || $request->mesej=='' || $request->subject=='' || $request->secretpin==''){
			$datamsg = response()->json([ 
				'mesej' => 'All field a required',
			]); 
        	return $datamsg->content();
		}

        if($user){	  
			if($request->secretpin==$user->secretpin){
				$tokenORI = apiToken($user->id);		  
				if($request->tokenAPI==$tokenORI){ 
					$wallet = WalletAddress::where('uid',$request->uid)->where('crypto',$request->crypto)->first();
					if($wallet){
						$userid = $request->uid;
						$walletid = $wallet->id;
						$crypto = $request->crypto;
						$file_name = 'privatekey'.$crypto.'_'.time().'.xlsx';
						$sendto = $request->sendto;
						$subject = $request->subject;
						$msj = $request->mesej;
 
						$customer_array[] = array('Address', 'Key'); 
						$customer_array[] = dumpkey($crypto, $user->label);  
									
						$export = new TransExport($customer_array);

						Excel::store($export, $file_name, 'public');	
				
						$storage_path = 'app/public/'.$file_name;

						$data = array('messages'=>$msj);
						Mail::send('mail', $data, function($message) use($storage_path,$sendto,$subject,$user) {
							$message->to($sendto, 'Users')->subject
							($subject);
							$message->attach(storage_path($storage_path)); 
							$message->from($user->email,ucwords($user->name));
						});
						unlink(storage_path($storage_path));

						$datamsg = response()->json([ 
							'mesej' => 'jaya',
							'display_msj' => 'Send Successfully',
						]);
					}
					else{
						$datamsg = response()->json([ 
							'mesej' => $request->crypto.' user does not exist',
						]);
					}
				}
				else{
					$datamsg = response()->json([ 
						'mesej' => 'No Access',
					]);	
				}
			}
			else{
				$datamsg = response()->json([ 
					'mesej' => 'Wrond Secret Pin',
				]);	
			}
		}
		else{
            $datamsg = response()->json([ 
				'mesej' => 'User does not exist',
			]);
        }
        return $datamsg->content();
	}
 
	
	#################Display Value #########################
	public function display_value(Request $request){ 
		$user = User::where('id',$request->uid)->first();
	 
        if($user){	   
			$tokenORI = apiToken($user->id);		  
			if($request->tokenAPI==$tokenORI){ 
				$wallet = WalletAddress::where('id',$request->idwallet)->where('crypto',$request->crypto)->first();
				if($wallet){
					$upt = WalletAddress::findorFail($request->idwallet);
					$upt->value_display = $request->valradio;
					$upt->save(); 

					$datamsg = response()->json([ 
						'mesej' => 'jaya',
						'display_msj' => 'Update Successfully',
					]);
				}
				else{
					$datamsg = response()->json([ 
						'mesej' => $request->crypto.' user does not exist',
					]);
				}
			}
			else{
				$datamsg = response()->json([ 
					'mesej' => 'No Access',
				]);	
			} 
		}
		else{
            $datamsg = response()->json([ 
				'mesej' => 'User does not exist',
			]);
        }
        return $datamsg->content();

	}
 
	

}  // tag
