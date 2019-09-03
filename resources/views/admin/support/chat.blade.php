@extends('admin.layouts.template')

@section('content') 
 
<div class="container-fluid">
    
<!-- ============================================================== -->
<!-- Email campaign chart -->
<!-- ============================================================== -->
  <?php 
			$titleM = \App\MenuTicket::where('id',$ticket->type)->first();
			$details = json_decode($ticket->details);
			
			?>
			
			<div class="row">
                    <div class="col-2">
                        
					</div>
                    <div class="col-8">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Ticket Details</h4> 
								<div class="row">
									<div class="col-md-12">
									<div class="form-group">
									<h5>Subject </h5>
									<div class="controls"> 
									<input type="text" class="form-control" value="{{$ticket->subject}}" readonly > </div>
									</div>
									</div> 
								</div> 
								<div class="row">
									<div class="col-md-12">
									<div class="form-group">
									<h5>Type </h5>
									<div class="controls"> 
									<input type="text" class="form-control" value="{{$titleM->title}}" readonly > </div>
									</div>
									</div> 
								</div> 		
								<?php  if($titleM->id==1){ ?>
								
								<div class="row">
									<div class="col-md-12">
									<div class="form-group">
									<h5>Digital Currency that User Send </h5>
									<div class="controls"> 
									<input type="text" class="form-control" value="{{$details->currency}}" readonly > </div>
									</div>
									</div> 
								</div> 	
								
								<div class="row">
									<div class="col-md-12">
									<div class="form-group">
									<h5>Is the transaction showing on a block explorer? </h5>
									<div class="controls"> 
									<input type="text" class="form-control" value="{{$details->blockExp}}" readonly > </div>
									</div>
									</div> 
								</div> 	
								
								<div class="row">
									<div class="col-md-12">
									<div class="form-group">
									<h5>Did you send to the correct deposit address? </h5>
									<div class="controls"> 
									<input type="text" class="form-control" value="{{$details->depoAddr}}" readonly > </div>
									</div>
									</div> 
								</div> 	
								
								<div class="row">
									<div class="col-md-12">
									<div class="form-group">
									<h5>Transaction date </h5>
									<div class="controls"> 
									<input type="text" class="form-control" value="{{$details->date}}" readonly > </div>
									</div>
									</div> 
								</div> 	
								
								<?php  }else if($titleM->id==2){ ?>
								
								<div class="row">
									<div class="col-md-12">
									<div class="form-group">
									<h5>Digital Currency that User Send </h5>
									<div class="controls"> 
									<input type="text" class="form-control" value="{{$details->currency}}" readonly > </div>
									</div>
									</div> 
								</div> 	
								
								<div class="row">
									<div class="col-md-12">
									<div class="form-group">
									<h5>Is the transaction showing on a block explorer? </h5>
									<div class="controls"> 
									<input type="text" class="form-control" value="{{$details->blockExp}}" readonly > </div>
									</div>
									</div> 
								</div> 	
								
								<div class="row">
									<div class="col-md-12">
									<div class="form-group">
									<h5>Did you send to the correct deposit address? </h5>
									<div class="controls"> 
									<input type="text" class="form-control" value="{{$details->depoAddr}}" readonly > </div>
									</div>
									</div> 
								</div> 	
								
								<div class="row">
									<div class="col-md-12">
									<div class="form-group">
									<h5>Transaction Hash </h5>
									<div class="controls"> 
									<input type="text" class="form-control" value="{{$details->transactionID}}" readonly > </div>
									</div>
									</div> 
								</div> 		
								
								<div class="row">
									<div class="col-md-12">
									<div class="form-group">
									<h5>Transaction date </h5>
									<div class="controls"> 
									<input type="text" class="form-control" value="{{$details->date}}" readonly > </div>
									</div>
									</div> 
								</div> 		 
								 
								<?php  }else if($titleM->id==4){ ?>
								
								<div class="row">
									<div class="col-md-12">
									<div class="form-group">
									<h5>Current verification status </h5>
									<div class="controls"> 
									<input type="text" class="form-control" value="{{$details->status}}" readonly > </div>
									</div>
									</div> 
								</div> 	 
								
								<?php  }else if($titleM->id==5){ ?>
								
								<div class="row">
									<div class="col-md-12">
									<div class="form-group">
									<h5>Reason for loss access </h5>
									<div class="controls"> 
									<input type="text" class="form-control" value="{{$details->reasonLoss}}" readonly > </div>
									</div>
									</div> 
								</div> 	
								
								<div class="row">
									<div class="col-md-12">
									<div class="form-group">
									<h5>User's Account </h5>
									<div class="controls"> 
									<input type="text" class="form-control" value="{{$details->acc}}" readonly > </div>
									</div>
									</div> 
								</div> 	
								
								<?php  }else if($titleM->id==7){ ?>  
								
								<div class="row">
									<div class="col-md-12">
									<div class="form-group">
									<h5>Category </h5>
									<div class="controls"> 
									<input type="text" class="form-control" value="{{$details->category}}" readonly > </div>
									</div>
									</div> 
								</div> 	
								
								<div class="row">
									<div class="col-md-12">
									<div class="form-group">
									<h5>Type </h5>
									<div class="controls"> 
									<input type="text" class="form-control" value="{{$details->title}}" readonly > </div>
									</div>
									</div> 
								</div> 	
								
								<?php  } ?>
								
								<div class="row">
									<div class="col-md-12">
									<div class="form-group">
									<h5>Status </h5>
									<div class="controls"> 
									<input type="text" class="form-control" value="{{$ticket->status}}" readonly > </div>
									</div>
									</div> 
								</div> 
							</div>
						</div>
					</div> 
                    <div class="col-2">
                        
					</div>
</div>

		<div class="row">
                    <div class="col-7">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Recent Chat</h4>  
								<div class="comment-widgets scrollable" style="height:637px;">
                                    <!--chat Row -->
                                    <ul class="chat-list">
									
									<?php 
									foreach ($chat as $data) {  
									if($data->typeP=='user'){
									?>
										<!--user -->
                                        <li class="chat-item">
                                            <div class="chat-img"><img src="{{asset('asset/user.png')}}" alt="user"></div>
                                            <div class="chat-content">
												
                                                <div class="box bg-light-info">{!!$data->contents!!}</div>
                                            </div>
                                            <div class="chat-time"><?php if($data->attachment!=''){ $file = settings('url_img_ocean').$data->attachment; ?><a href="{{asset($file)}}" target="_blank">  <i class="fas fa-paperclip la-2x text-primary"></i>Attachment </a> <?php } ?>  &nbsp; &nbsp;&nbsp; {{$data->created_at}}</div>
                                        </li>
									<?php 
									}else{
									?> 	
                                        <!--admin-->
                                        <li class="odd chat-item"> 
                                            <div class="chat-content"> 
                                                <div class="box bg-light-inverse">{!!$data->contents!!}</div>
												<div class="chat-time"><?php if($data->attachment!=''){ $file = settings('url_img_ocean').$data->attachment; ?><a href="{{asset($file)}}" target="_blank">  <i class="fas fa-paperclip la-2x text-primary"></i>Attachment </a> <?php } ?>  &nbsp; &nbsp;&nbsp; {{$data->created_at}}</div> 
                                            </div>
                                        </li>
                                    <?php 
									}}
									?>    
                                    </ul>
								</div> 
							</div>
						</div>
					</div> 
                    <div class="col-5">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Reply Message</h4> 
								@include('partials.errors')
								<form role="form" action="{{route('admin.support.update')}}" method="post" enctype="multipart/form-data">
									{{csrf_field()}}
									<p></p>   
											<div class="row">
												<div class="col-md-12">
												<div class="form-group">
												<h5>Attachment </h5>
												<div class="controls">
												<input id="attachment" type="file" class="form-control" name="attachment"> </div>
												</div>
												</div> 
											</div>  
											<div class="row">
												<div class="col-md-12">
												<div class="form-group">
												<h5>Content *</h5>
												<div class="controls"> 
												<textarea id="mymce" name="content"></textarea> </div>
												</div>
												</div> 
											</div>   
											<?php if($ticket->status!='Closed'){ ?>
												<div class="text-xs-right">
												<input type="hidden" class="form-control" name="id" value="{{$ticket->id}}">
												<button type="submit" class="btn btn-info" name="btn_save">Save changes</button> 
												</div> 
											<?php } ?> 
											</div> 
											</form>
								
							</div>
						</div>
					</div>
</div>

 

@endsection