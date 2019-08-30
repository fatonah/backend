<?php

namespace App\Http\Controllers;
  
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Intervention\Image\Facades\Image;
use Illuminate\Http\Request;

use DB;
use App\Admin;  
use App\User; 
use App\MenuTicket;
use App\Messages; 
use App\Ticket;   


class SupportAdminController extends Controller
{
	public function __construct()
    {
        $this->middleware('auth:admin');
    }
	
	
	#################List Support#########################
	public function support_list()
	{   
		$ticket_open = Ticket::where('status','Open')->get(); 
		$ticket_waiting = Ticket::where('status','Awaiting Reply')->get(); 
		$ticket_answered = Ticket::where('status','Answered')->get(); 
		$ticket_closed = Ticket::where('status','Closed')->get(); 
		$jum_open = Ticket::where('status','Open')->count();
		$jum_waiting = Ticket::where('status','Awaiting Reply')->count();
		$jum_answered = Ticket::where('status','Answered')->count();
		$jum_closed = Ticket::where('status','Closed')->count();
		  
		return view('admin.support.index', compact('ticket_open','ticket_waiting','ticket_answered','ticket_closed','jum_open','jum_waiting','jum_answered','jum_closed'));
		
	}
			 
	#################Edit Support#########################
	public function support_edit($id)
	{  
		$ticket = Ticket::where('id',$id)->first();  
		$menuT = MenuTicket::all();  
		$chat = Messages::where('ticket_id',$ticket->id)->get(); 
		$user = User::where('id',$ticket->uid)->first(); 
		 
		return view('admin.support.chat',compact('ticket','menuT','chat','user'));		
		
	}
		
	#################Update Support#########################
	public function support_update(Request $request)
	{ 
	$uid = Auth::guard('admin')->user()->id;
	 
		$this->validate($request,array(
            'attachment' => 'mimes:png,jpg,jpeg,gif,pdf' ,
            'content' => 'required' , 
        ));
		
		$user = Admin::where('id',$uid)->first(); 
		  
			$ticketU = Ticket::findorFail($request->id);
			$ticketU->status = 'Answered';
			$ticketU->save();
			
			if ($request->hasFile('attachment')) {	 		
            $image = $request->file('attachment'); 
			$filename = time() . '.'.$image->getClientOriginalExtension(); 
            $location = 'support/'. $filename; 
            $path2 = '/../asset/support/';  
			$image->move(realpath(base_path().$path2),$filename);
			 
			$msj = new Messages;
			$msj->uid = $user->id;
			$msj->ticket_id = $request->id;
			$msj->typeP = 'admin';
			$msj->contents = $request->content;
			$msj->attachment = $location;
			$msj->save();
		
			}else{	
 		
			$msj = new Messages;
			$msj->uid = $user->id;
			$msj->ticket_id = $request->id;
			$msj->typeP = 'admin';
			$msj->contents = $request->content; 
			$msj->save(); 
				
			}
	 
		return redirect()->back();
		
	} 
		
	#################Closed Support#########################
	public function support_closed(Request $request) 
	{ 
	$uid = Auth::guard('admin')->user()->id;
	
		$ticket = Ticket::where('id', $request->id)->first(); 
		 
		if($ticket){
			$ticketU = Ticket::findorFail($request->id);
			$ticketU->status = 'closed';
			$ticketU->save();
		
		}else{
		notify()->flash('Sorry, Error!!', 'error', [
		'timer' => 3000,
		'text' => '',
		'buttons' => true
		]);
		
		}
		return redirect()->back();
	}
		
	#################Delete Support#########################
	public function support_delete(Request $request) 
	{ 
	$uid = Auth::guard('admin')->user()->id;
	
		$ticket = Ticket::where('id', $request->id)->first(); 
		 
		if($ticket){
			$ticketU = Ticket::findorFail($request->id);
			$ticketU->status = 'delete';
			$ticketU->save();
		
		}else{
		notify()->flash('Sorry, Error!!', 'error', [
		'timer' => 3000,
		'text' => '',
		'buttons' => true
		]);
		
		}
		return redirect()->back();
	}
	 
	
}  // tag