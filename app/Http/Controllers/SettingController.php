<?php

namespace App\Http\Controllers;
  
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Intervention\Image\Facades\Image;
use Illuminate\Http\Request; 

use DB;
use App\Setting;   


class SettingController extends Controller
{
	   
	#################View Setting#########################
	public function view()
	{   
		$settings = Setting::first(); 
 
		return view('admin.setting', compact('settings'));
		
	}
		 
	#################Update Setting#########################
	public function setting_update(Request $request)
	{  
 
		$this->validate($request, [
			'title' =>'required',
			'name' =>'required',
			'url' =>'required',
			'infoemail' =>'required',
			'supportemail' =>'required',
			'commission_withdraw' =>'required',
			'fee_btc' =>'required',
			'fee_bch' =>'required',
			'fee_doge' =>'required',
		]);
		 
		$setting = Setting::findorFail(1);
		$setting->title = $request->title;
		$setting->description = $request->description;
		$setting->keywords = $request->keywords;
		$setting->name = $request->name;
		$setting->infoemail = $request->infoemail;
		$setting->supportemail = $request->supportemail;
		$setting->url = $request->url;
		$setting->commission_withdraw = $request->commission_withdraw;
		$setting->fee_btc = $request->fee_btc;
		$setting->fee_bch = $request->fee_bch;
		$setting->fee_doge = $request->fee_doge;
		$setting->save();
		 
		notify()->flash('Update successfull!', 'success', [
		'timer' => 3000,
		'text' => '',
		'buttons' => true
		]);			 
		  
		return redirect()->back();
		
	} 
	  
 
	
}  // tag