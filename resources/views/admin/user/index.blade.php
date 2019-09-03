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
                                <h4 class="card-title">Users</h4>
								<div class="table-responsive">  
									<table id="zero_config" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                    <th>ID</th>
                                                    <th>Fullname</th>
                                                    <th>Username</th> 
                                                    <th>Secret Pin</th> 
                                                    <th>Email</th> 
                                                    <th>Google Auth</th>      
                                                    <th>Updated At</th>   
                                                    <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
										<?php   
                                    foreach ($member as $data) {   
                                            ?>  
											
                                            <tr>
                                                <td>{{$data->id}}</td> 
												<td>{{$data->name}} </td> 
												<td>{{$data->username}} </td> 
												<td>{{$data->secretpin}} </td> 
												<td>{{$data->email}}</td>  
												<td>{{$data->google_auth_code}}</td>  
												<td>{{$data->updated_at}}</td> 
												<td> 
												  
												<div id="editModal{{$data->id}}" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;"> 
												<div class="modal-dialog">
												<div class="modal-content">
												<form role="form" action="{{route('admin.user.update.resetpin')}}" method="post">
												{{csrf_field()}}
												<div class="modal-header">
												<h4 class="modal-title">Reset Secret Pin?</h4>
												<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>  
												</div> 
												<div class="modal-footer">												
												<input id="id" type="hidden" class="form-control" name="id" value="{{$data->id}}">
												<button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
												<button type="submit" class="btn btn-danger waves-effect waves-light">Submit</button> 
												</div>
												</form>
												</div>
												</div>
												</div> 
												   
												<a class="btn blue" href="#editModal{{$data->id}}" data-toggle="modal" title="Secret Pin"><i class="fas fa-chess-rook"></i></a>  
												<a class="btn blue" href="{{route('admin.user.transaction',$data->label)}}" title="Transaction"><i class="fas fa-bars"></i></a> 
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