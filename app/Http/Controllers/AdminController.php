<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Lib\GoogleAuthenticator;
 
 use App\Admin;
use App\User;
use App\WalletAddress;
use App\TransAdmin;
use App\PriceCrypto;

class AdminController extends Controller
{
    
	public function testPage()
	{
 //get_label_crypto('BTC', '3BaJXxZismiTc8pfXnmsPDYuQ4A1AwYusb');getransaction('BTC', 'usr_admin');
		 $label = listransaction('BTC','usr_princeofbitcoin');
		 dd('test');
 
	}
    
	public function authyAdmin()
	{
		return view('admin.auth.authy');
	}

	public function SubmitAuthyAdmin(Request $request)
	{
		$this->validate($request, [
			'code' =>'required'
		]);
 
		$ga = new GoogleAuthenticator();

		$secret = Auth::guard('admin')->user()->google_auth_code;
		$oneCode = $ga->getCode($secret);
		$userCode = $request->code;

		if ($oneCode == $userCode) {
 
		return redirect('admin/dashboard');

		} 
		else {
		$msg = [
			'error' => 'Wrong Verification Code',
		];
		return redirect()->back()->with($msg);
		}
	}
	 
    public function index()
    {
 
        $id = Auth::guard('admin')->user()->id;
        $admin =  Admin::where('id',$id)->first();
        
        $label = 'usr_doradofees';
 
        $addressBTC =  WalletAddress::where('label',$label)->where('crypto','BTC')->first()->address;
         $addressBCH =  WalletAddress::where('label',$label)->where('crypto','BCH')->first()->address;
          $addressDOGE =  WalletAddress::where('label',$label)->where('crypto','DOGE')->first()->address;

        $balanceBTC = getbalance('BTC', $label)/100000000;
       $balanceBCH = getbalance('BCH', $label)/100000000;
       $balanceDOGE = getbalance('DOGE', $label)/100000000;
 
        return view('admin.home', Compact('admin','balanceBTC','balanceBCH','balanceDOGE','addressBTC','addressBCH','addressDOGE'));
		 
    }


  public function transactions($crypto)
    { 
         $id = Auth::guard('admin')->user()->id;
        $admin =  Admin::where('id',$id)->first();
        
        $label = 'usr_admin'; 

       $trans = listransaction($crypto, $label);
         return view('admin.transactions', Compact('admin','crypto','label','trans'));

    }


  public function listUsers($crypto)
    { 
		$trans = listransactionall($crypto);	 
 //dd($trans);
        return view('admin.list_users', compact('trans','crypto'));

    }
	
	
  public function transactionsUsers($crypto,$label)
    {
   
	   $trans = listransaction($crypto, $label);
	  // dd($trans);
         return view('admin.transactions_users', Compact('users','crypto','label','trans'));

    }

 
    public function send($crypto)
    {
       $id = Auth::guard('admin')->user()->id;
       $admin =  Admin::where('id',$id)->first();

       $label = 'usr_admin';

       $fromaddress =  WalletAddress::where('label',$label)->where('crypto',$crypto)->first();
        $balance = getbalance($crypto, $label)/100000000;
 //dd($balance);
       return view('admin.send', compact('admin','crypto','label','fromaddress','balance'));

   }

   
    public function sendSubmit(Request $request)
    {
		 
		$this->validate($request,[
			'to'   => 'required',
			'amount' => 'required|numeric',
			'code' => 'required'
		]);

		$id = Auth::guard('admin')->user()->id;
		$admin =  Admin::where('id',$id)->first();

		$label = 'usr_admin';
		$crypto = $request->crypto;

		$fromaddress =  WalletAddress::where('label',$label)->where('crypto',$crypto)->first();
		$balance = getbalance($crypto, $label)/100000000;

		$balance = sprintf('%f', $balance);
		if($crypto=='BTC'){$fees_pay = settings('fee_btc');}elseif($crypto=='BCH'){$fees_pay = settings('fee_bch');}elseif($crypto=='DOGE'){$fees_pay = settings('fee_doge');}else{$fees_pay = 0;}
		$amount = sprintf('%f', ($request->amount+$fees_pay));
		
		$ga = new GoogleAuthenticator();

		$secret = Auth::guard('admin')->user()->google_auth_code;
		$oneCode = $ga->getCode($secret);
		$userCode = $request->code;

		if ($oneCode != $userCode) { 
		notify()->flash('Wrong Verification Code!', 'error', [
			'timer' => 3000,
			'text' => '',
			'buttons' => true
			]);
             
			return redirect()->back();

		}else if($balance < $amount){
			
		notify()->flash('Insufficient Balance!', 'error', [
			'timer' => 3000,
			'text' => '',
			'buttons' => true
			]);
             
			return redirect()->back();

		}else{ 
        $send = withdrawal_admin_crypto($crypto, $label, $request->to, $request->amount,'withdraw');

       // $send = 1;

        if($send){

			$trans = new TransAdmin;
			$trans->uid = $admin->id;
			$trans->account = 'admin';
			$trans->toAddress = $request->to;
			$trans->status = 'success';
			$trans->crypto = $crypto;
			$trans->amount = $request->amount;
			$trans->balBefore = $balance;
			 
            notify()->flash('Successfully send!', 'success', [
			'timer' => 3000,
			'text' => '',
			'buttons' => true
			]);
             
			return redirect()->back();
            } 
            else {

			$trans = new TransAdmin;
			$trans->uid = $admin->id;
			$trans->account = 'admin';
			$trans->toAddress = $request->to;
			$trans->status = 'failed';
			$trans->crypto = $crypto;
			$trans->amount = $request->amount;
			$trans->balBefore = $balance;
			 
            notify()->flash('Failed to send!', 'error', [
			'timer' => 3000,
			'text' => '',
			'buttons' => true
			]);

			return redirect()->back();

			}
 
		}
    }
	
	 
	

}//
