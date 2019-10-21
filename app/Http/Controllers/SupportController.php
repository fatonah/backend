<?php

namespace App\Http\Controllers;
  
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
//use Intervention\Image\Facades\Image;
use Illuminate\Http\Request;
use App\Lib\GoogleAuthenticator;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

use DB;
use App\User;  
use App\MenuTicket;
use App\Messages; 
use App\Ticket;  

class SupportController extends Controller
{
	
	public function authy()
	{
		return view('auth.authy');
	}

	public function SubmitAuthy(Request $request)
	{
		$this->validate($request, [
			'code' =>'required'
		]);
 
		$ga = new GoogleAuthenticator();
		
		$secret = Auth::user()->google_auth_code;
		$oneCode = $ga->getCode($secret);
		$userCode = $request->code;

		if ($oneCode == $userCode) {
 
		return redirect('users/home');

		} 
		else {
		$msg = [
			'error' => 'Wrong Verification Code',
		];
		return redirect()->back()->with($msg);
		}
	}
	 
	
	#################List Support#########################
	public function support_list()
	{ 		 
		$uid = Auth::id();
 
		$menuT = MenuTicket::all();
		$ticket = Ticket::where('uid',$uid)->get();
		$user = User::where('id',$uid)->first();
		  
		return view('support.index', compact('ticket','menuT','user'));
		
	}
	 
    public function ajax($type)
    {
 
		if($type == 1)
        {
            return view('support.sendIssue');
        }
		
        else if($type == 2)
        {
            return view('support.receiveIssue');
        }
 
        else if($type == 3)
        {
            return view('support.pwReset');
        }
  
        else if($type == 5)
        {
            return view('support.verification');
        }

        else if($type == 6)
        {
            return view('support.loginIssue');
        }

        else if($type == 7)
        {
            return view('support.wallet');
        }
 
        else
        {
        return view('support.general');
        }
	 
    }
	
	#################New Support#########################
	public function support_new()
	{  
	$uid = Auth::id();
 
		$menuT = MenuTicket::all(); 
		$user = User::where('id',$uid)->first();
		
		return view('support.new', compact('user','menuT'));
	}
	
	
	#################Store Support#########################
	public function support_store(Request $request)
	{  
	$uid = Auth::id();
	
		$this->validate($request,array(
            'attachment' => 'mimes:png,jpg,jpeg,gif,pdf' ,
            'subject' => 'required' , 
            'type' => 'required' , 
            'content' => 'required' , 
        ));
		
		$user = User::where('id',$uid)->first(); 
		 
		if($user){
			
			if ($request->hasFile('attachment')) {			
            $image = $request->file('attachment'); 
			 
			$extensiondocs = strtolower($image->getClientOriginalExtension());
			$file_name = hash('adler32',rand());
			$file = file_get_contents($image);
			$fileup = Storage::disk('spaces')->put('dolphinfish_pillar/OL_'.$user->id.'/'.$file_name.'.'.$extensiondocs, $file, 'public');
			$filedir = 'dolphinfish_pillar/OL_'.$user->id.'/'.$file_name.'.'.$extensiondocs;
			}
			
			$ticket = new Ticket;
			$ticket->uid = $user->id;
			$ticket->type = $request->type; 
			$ticket->subject = $request->subject; 
			$ticket->status = 'Open'; 
			
			if($request->type == 1){
			$arr['currency'] = $request->currency;
			$arr['blockExp'] = $request->blockExp;
			$arr['depoAddr'] = $request->depoAddr;
			$arr['date'] = $request->date;

			$data = json_encode($arr);
			$ticket->details = $data;
			$ticket->save();	
			}
			else if($request->type == 2){
			$arr['currency'] = $request->currency;
			$arr['blockExp'] = $request->blockExp;
			$arr['depoAddr'] = $request->depoAddr;
			$arr['transactionID'] = $request->transactionID;
			$arr['date'] = $request->date;

			$data = json_encode($arr);

			$ticket->details = $data;
			$ticket->save();
			}
			else if($request->type == 3){
			  
			$ticket->details = '';
			$ticket->save();
			}
			else if($request->type == 5){
			$arr['status'] = $request->status;

			$data = json_encode($arr);

			$ticket->details = $data;
			$ticket->save();
			}
			else if($request->type == 6){
			$arr['reasonLoss'] = $request->reasonLoss;
            $arr['acc'] = $request->acc; 

			$data = json_encode($arr);

			$ticket->details = $data;
			$ticket->save();
			}
			else if($user->type == 7){
			$arr['category'] = $request->category; 

			$data = json_encode($arr);

			$ticket->details = $data;
			$ticket->save();
			}
			else {
			 
			$ticket->details = '';
			$ticket->save();
			}
			
			
			if ($request->hasFile('attachment')) {		 
		 
			$msj = new Messages;
			$msj->uid = $user->id;
			$msj->ticket_id = $ticket->id;
			$msj->typeP = 'user';
			$msj->contents = $request->content;
			$msj->attachment = $filedir;
			$msj->save();
		
			}else{		
				
			$msj = new Messages;
			$msj->uid = $user->id;
			$msj->ticket_id = $ticket->id;
			$msj->typeP = 'user';
			$msj->contents = $request->content; 
			$msj->save(); 
				
			}
			
			$udetails = User::where('id',Auth::id())->first();
            $to = $udetails->email;
            $from_email = settings('infoemail');
            $urlBase = settings('url');
            $subject = settings('title').' | Ticket';
           
          $name = $udetails->username;
		  $logo = asset('asset/assets/images/logo.png');
		  $message = '
			<center>
			 <img src="'.$logo.'" style="width:150px;">
			</center>
			<br>
			Dear '.$name.' ,
			<br>
			We would like to acknowledge that we have received you request and ticket has been created.
			<br>
			A support representative will be reviewing you request and will send you a personal response.
			<br>
			To view the status of the ticket or add comments, please visit <a href="'.$urlBase.'" target="_blank"> '.$urlBase.'</a>
			<br>
			Thank you for your patience.
			<br>
			';        
					  		  
			send_email_basic002($to, $subject, $name, $message);

			
		notify()->flash('Successfully Submit!', 'success', [
		'timer' => 3000,
		'text' => '',
		]);
			
		}else{ 
		notify()->flash('Error!', 'error', [
		'timer' => 3000,
		'text' => '',
		]);
		
		} 
				
		return redirect()->back();
		
	} 	 
		
	#################Edit Support#########################
	public function support_edit($id)
	{  
		$ticket = Ticket::where('id',$id)->first();  
		$menuT = MenuTicket::all();  
		$chat = Messages::where('ticket_id',$ticket->id)->get(); 
		$user = User::where('id',$ticket->uid)->first(); 
		 
		return view('support/chat',compact('ticket','menuT','chat','user'));		
		
	}
		
	#################Update Support#########################
	public function support_update(Request $request)
	{ 
	$uid = Auth::id();
	
		$this->validate($request,array(
            'attachment' => 'mimes:png,jpg,jpeg,gif,pdf' ,
            'content' => 'required' , 
        ));
		
		$user = User::where('id',$uid)->first(); 
		  
			$ticketU = Ticket::findorFail($request->id);
			$ticketU->status = 'Awaiting Reply';
			$ticketU->save();
			
			if ($request->hasFile('attachment')) {	 		
            $image = $request->file('attachment'); 
			 
			$extensiondocs = strtolower($image->getClientOriginalExtension());
			$file_name = hash('adler32',rand());
			$file = file_get_contents($image);
			$fileup = Storage::disk('spaces')->put('dolphinfish_pillar/OL_'.$user->id.'/'.$file_name.'.'.$extensiondocs, $file, 'public');
			$filedir = 'dolphinfish_pillar/OL_'.$user->id.'/'.$file_name.'.'.$extensiondocs;
			 
			 
			$msj = new Messages;
			$msj->uid = $user->id;
			$msj->ticket_id = $request->id;
			$msj->typeP = 'user';
			$msj->contents = $request->content;
			$msj->attachment = $filedir;
			$msj->save();
		
			}else{	
 		
			$msj = new Messages;
			$msj->uid = $user->id;
			$msj->ticket_id = $request->id;
			$msj->typeP = 'user';
			$msj->contents = $request->content; 
			$msj->save(); 
				
			}
	 
		return redirect()->back();
		
	} 
		
	#################Delete Support#########################
	public function support_delete(Request $request) 
	{ 
	$uid = Auth::id();
	
		$ticket = Ticket::where('id', $request->id)->first();
		 
		if($ticket){
			$ticketU = Ticket::findorFail($request->id);
			$ticketU->status = 'Closed';
			$ticketU->save();
		
		notify()->flash('Ticket Closed!', 'success', [
		'timer' => 3000,
		'text' => '',
		]);
		
		}else{
		notify()->flash('Sorry, Error!!', 'error', [
		'timer' => 3000,
		'text' => '',
		]);
		
		}
		return redirect()->back();
	}
	 
	
	}  // tag