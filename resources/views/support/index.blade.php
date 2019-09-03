 @extends('layouts.default')

@section('contents') 

<div class="page-wrapper">
 
<div class="page-breadcrumb">
    <div class="row"> 
        <div class="col-7">
            <h4 class="page-title"> </h4> 
			<a class="btn dark btn-md pull-right" href="{{route('support.new')}}" >
            <i class="fa fa-plus"></i>   ADD NEW
            </a>   
        </div> 
        <div class="col-5">
		</div>
    </div>
</div>
<!-- ============================================================== -->
<!-- End Bread crumb and right sidebar toggle -->
<!-- ============================================================== -->
<!-- ============================================================== -->
<!-- Container fluid  -->
<!-- ============================================================== -->
<div class="container-fluid">
     
<!-- ============================================================== -->
<!-- User Pending Review -->
<!-- ============================================================== --> 

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Support </h4>
								<div class="table-responsive"> 
									<table id="zero_config2" class="table table-striped table-bordered display">
                                        <thead>
                                            <tr>
                                                    <th>ID</th>
                                                    <th>Title</th> 
                                                    <th>Content</th>
                                                    <th>Image</th>   
                                                    <th>Updated At</th> 
                                                    <th>Feedback</th>   
                                                    <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
										<?php   
                                    foreach ($ticket as $data) {  
										$message = \App\Messages::where('ticket_id',$data->id)->orderBy('id','desc')->first(); 
										$menu = \App\MenuTicket::where('id',$data->type)->first(); 
									
                                            ?>  
											
                                            <tr>
                                                <td>{{$data->id}}</td> 
												<td>{{$menu->title}} </td> 
                                               <td>{!!substr($message->contents, 0, 30)!!}</td>
												<td><?php $imgN = 'assets/'.$data->attachment;
												if($data->attachment!=''){?>
												<a href="{{asset($imgN)}}" target="_blank" >View</a>
												<?php }?></td>  
												<td>{{$data->updated_at}}</td> 
												<td><?php if($data->status=='Answered'){ echo 'YES';}else{ echo 'NO';} ?></td> 
												<td> 
												
												<div id="deleteModal{{$data->id}}" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
												<div class="modal-dialog">
												<div class="modal-content">
												<form role="form" action="{{route('support.delete')}}" method="post">
                                                {{csrf_field()}}
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                                    <h2 class="modal-title" style="color: red;">Are you sure?</h2>
													<input type="hidden" value="{{$data->id}}" name="id"> 
													<input type="hidden" name="uid" value="{{$user->id}}" class="form-control">
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" data-dismiss="modal" class="btn default">Cancel</button>
                                                    <button type="submit" class="btn red" id="delete"><i class="fa fa-trash"></i> Closed</button>
                                                </div>
												</form>
												</div>
												</div>
												</div> 
												 
												<a href="{{ route('support.edit',$data->id)}}" title="Edit"><i class="fas fa-edit"></i></a> 
												<a class="btn blue" href="#deleteModal{{$data->id}}" data-toggle="modal"><i class="fas fa-trash"></i></a>  
											 
												</td> 
                                            </tr>
											
									<?php }   ?>
                                        </tbody> 
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                 
</div>
 
		

@endsection