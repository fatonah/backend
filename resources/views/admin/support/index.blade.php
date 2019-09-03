 @extends('admin.layouts.template')

@section('content') 
 
<div class="container-fluid">
     
<!-- ============================================================== -->
<!-- User Pending Review -->
<!-- ============================================================== --> 

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Support</h4>
								 <div class="widget-body sliding-tabs">

                                        <ul class="nav nav-tabs" id="example-one" role="tablist">
                                           <li class="nav-item">
                                                <a class="nav-link active" id="base-tab-1" data-toggle="tab" href="#tab-1" role="tab" aria-controls="tab-1" aria-selected="true">Open&nbsp;&nbsp;<span class="badge" style="background-color:red;">{{$jum_open}}</span></a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="base-tab-2" data-toggle="tab" href="#tab-2" role="tab" aria-controls="tab-2" aria-selected="false">Awaiting Reply&nbsp;&nbsp;<span class="badge" style="background-color:red;">{{$jum_waiting}}</span></a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="base-tab-3" data-toggle="tab" href="#tab-3" role="tab" aria-controls="tab-3" aria-selected="false">Answered&nbsp;&nbsp;<span class="badge" style="background-color:red;">{{$jum_answered}}</span></a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="base-tab-4" data-toggle="tab" href="#tab-4" role="tab" aria-controls="tab-4" aria-selected="false">Closed&nbsp;&nbsp;<span class="badge" style="background-color:red;">{{$jum_closed}}</span></a>
                                            </li>                                             
                                        </ul>

                                       <div class="tab-content pt-3">
											<div class="tab-pane fade show active" id="tab-1" role="tabpanel" aria-labelledby="base-tab-1">
                                               <div class="widget-body">
													<table id="zero_config2" class="table table-striped table-bordered display">
														<thead>
															<tr>
																	<th>ID</th>
																	<th>Username</th>
																	<th>Title</th>     
																	<th>Updated At</th>   
																	<th>Action</th>
															</tr>
														</thead>
														<tbody>
														<?php   
													foreach ($ticket_open as $data) {   
														$menu = \App\MenuTicket::where('id',$data->type)->first(); 
														$user = \App\User::where('id',$data->uid)->first(); 
															?>  
															
															<tr>
																<td>{{$data->id}}</td> 
																<td>{{$user->username}} </td> 
																<td>{{$menu->title}} </td>  
																<td>{{$data->updated_at}}</td> 
																<td> 
																
																<div id="closedModal{{$data->id}}" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
																<div class="modal-dialog">
																<div class="modal-content">
																<form role="form" action="{{route('admin.support.closed')}}" method="post">
																{{csrf_field()}}
																<div class="modal-header">
																	<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
																	<h2 class="modal-title" style="color: red;">Are you sure to CLOSED?</h2>
																	<input type="hidden" value="{{$data->id}}" name="id">  
																</div>
																<div class="modal-footer">
																	<button type="button" data-dismiss="modal" class="btn default">Cancel</button>
																	<button type="submit" class="btn red" id="delete"><i class="fa fa-trash"></i> Closed</button>
																</div>
																</form>
																</div>
																</div>
																</div> 
																
																<a href="{{ route('admin.support.edit',$data->id)}}" title="Edit"><i class="fas fa-edit"></i></a> 
																<?php if($data->status!='Closed'){ ?>
																<a class="btn blue" href="#closedModal{{$data->id}}" data-toggle="modal"><i class="fas fa-lock"></i></a>   
																<?php } ?>
																</td> 
															</tr>
															
													<?php }   ?>
														</tbody> 
													</table>
											   </div>
											</div>
											
											
											<div class="tab-pane" id="tab-2" role="tabpanel" aria-labelledby="base-tab-2">
                                               <div class="widget-body">
													<table id="zero_config3" class="table table-striped table-bordered display">
														<thead>
															<tr>
																	<th>ID</th>
																	<th>Username</th>
																	<th>Title</th>    
																	<th>Updated At</th>   
																	<th>Action</th>
															</tr>
														</thead>
														<tbody>
														<?php   
													foreach ($ticket_waiting as $data) {   
														$menu = \App\MenuTicket::where('id',$data->type)->first(); 
														$user = \App\User::where('id',$data->uid)->first(); 
															?>  
															
															<tr>
																<td>{{$data->id}}</td> 
																<td>{{$user->username}} </td> 
																<td>{{$menu->title}} </td>  
																<td>{{$data->updated_at}}</td> 
																<td> 
																
																<div id="closedModal{{$data->id}}" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
																<div class="modal-dialog">
																<div class="modal-content">
																<form role="form" action="{{route('admin.support.closed')}}" method="post">
																{{csrf_field()}}
																<div class="modal-header">
																	<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
																	<h2 class="modal-title" style="color: red;">Are you sure to CLOSED?</h2>
																	<input type="hidden" value="{{$data->id}}" name="id">  
																</div>
																<div class="modal-footer">
																	<button type="button" data-dismiss="modal" class="btn default">Cancel</button>
																	<button type="submit" class="btn red" id="delete"><i class="fa fa-trash"></i> Closed</button>
																</div>
																</form>
																</div>
																</div>
																</div> 
																
																<a href="{{ route('admin.support.edit',$data->id)}}" title="Edit"><i class="fas fa-edit"></i></a> 
																<?php if($data->status!='Closed'){ ?>
																<a class="btn blue" href="#closedModal{{$data->id}}" data-toggle="modal"><i class="fas fa-lock"></i></a>   
																<?php } ?>
																</td> 
															</tr>
															
													<?php }   ?>
														</tbody> 
													</table>
											   </div>
											</div>
											
											
											<div class="tab-pane fade" id="tab-3" role="tabpanel" aria-labelledby="base-tab-3">
                                               <div class="widget-body">
													<table id="zero_config4" class="table table-striped table-bordered display">
														<thead>
															<tr>
																	<th>ID</th>
																	<th>Username</th>
																	<th>Title</th>  
																	<th>Updated At</th>   
																	<th>Action</th>
															</tr>
														</thead>
														<tbody>
														<?php   
													foreach ($ticket_answered as $data) {    
														$menu = \App\MenuTicket::where('id',$data->type)->first(); 
														$user = \App\User::where('id',$data->uid)->first(); 
															?>  
															
															<tr>
																<td>{{$data->id}}</td> 
																<td>{{$user->username}} </td> 
																<td>{{$menu->title}} </td>  
																<td>{{$data->updated_at}}</td> 
																<td> 
																
																<div id="closedModal{{$data->id}}" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
																<div class="modal-dialog">
																<div class="modal-content">
																<form role="form" action="{{route('admin.support.closed')}}" method="post">
																{{csrf_field()}}
																<div class="modal-header">
																	<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
																	<h2 class="modal-title" style="color: red;">Are you sure to CLOSED?</h2>
																	<input type="hidden" value="{{$data->id}}" name="id">  
																</div>
																<div class="modal-footer">
																	<button type="button" data-dismiss="modal" class="btn default">Cancel</button>
																	<button type="submit" class="btn red" id="delete"><i class="fa fa-trash"></i> Closed</button>
																</div>
																</form>
																</div>
																</div>
																</div> 
																
																<a href="{{ route('admin.support.edit',$data->id)}}" title="Edit"><i class="fas fa-edit"></i></a> 
																<?php if($data->status!='Closed'){ ?>
																<a class="btn blue" href="#closedModal{{$data->id}}" data-toggle="modal"><i class="fas fa-lock"></i></a>   
																<?php } ?>
																</td> 
															</tr>
															
													<?php }   ?>
														</tbody> 
													</table>
											   </div>
											</div>
											
											<div class="tab-pane fade" id="tab-4" role="tabpanel" aria-labelledby="base-tab-4">
                                               <div class="widget-body">
													<table id="zero_config5" class="table table-striped table-bordered display">
														<thead>
															<tr>
																	<th>ID</th>
																	<th>Username</th>
																	<th>Title</th>    
																	<th>Updated At</th>   
																	<th>Action</th>
															</tr>
														</thead>
														<tbody>
														<?php   
													foreach ($ticket_closed as $data) {  
														$message = \App\Messages::where('ticket_id',$data->id)->orderBy('id','desc')->first(); 
														$menu = \App\MenuTicket::where('id',$data->type)->first(); 
														$user = \App\User::where('id',$data->uid)->first(); 
															?>  
															
															<tr>
																<td>{{$data->id}}</td> 
																<td>{{$user->username}} </td> 
																<td>{{$menu->title}} </td> 
																<td>{{$data->updated_at}}</td> 
																<td> 
																
																<div id="closedModal{{$data->id}}" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
																<div class="modal-dialog">
																<div class="modal-content">
																<form role="form" action="{{route('admin.support.closed')}}" method="post">
																{{csrf_field()}}
																<div class="modal-header">
																	<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
																	<h2 class="modal-title" style="color: red;">Are you sure to CLOSED?</h2>
																	<input type="hidden" value="{{$data->id}}" name="id">  
																</div>
																<div class="modal-footer">
																	<button type="button" data-dismiss="modal" class="btn default">Cancel</button>
																	<button type="submit" class="btn red" id="delete"><i class="fa fa-trash"></i> Closed</button>
																</div>
																</form>
																</div>
																</div>
																</div> 
																
																<a href="{{ route('admin.support.edit',$data->id)}}" title="Edit"><i class="fas fa-edit"></i></a> 
																<?php if($data->status!='Closed'){ ?>
																<a class="btn blue" href="#closedModal{{$data->id}}" data-toggle="modal"><i class="fas fa-lock"></i></a>   
																<?php } ?>
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
                    </div>
                </div>
                 
</div>
 

@endsection