 @extends('admin.layouts.template')

@section('content') 
 
 <div class="page-breadcrumb">
    <div class="row">
        <div class="col-12">
            <h4 class="page-title"></h4>  
            <div class="d-flex align-items-center">
			<div id="create" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog">
            <div class="modal-content">
            <form role="form" action="{{route('admin.member.new')}}" method="post">
            {{csrf_field()}}
            <div class="modal-header">
            <h4 class="modal-title">Add New</h4>
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>  
            </div>
            <div class="modal-body">  
            <label for="username" class="control-label">Username:</label>
            <input id="username" type="text" class="form-control" name="username" required> 
             
            <label for="username" class="control-label">Fullname:</label>
            <input id="username" type="text" class="form-control" name="name" required> 
			
            <label for="email" class="control-label">Email:</label>
            <input id="email" type="text" class="form-control" name="email" required data-validation-regex-regex="([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})" data-validation-regex-message="Enter Valid Email">
					   
            <label for="password" class="control-label">Password:</label>
            <input id="password" type="text" class="form-control" name="password" required> 
			
            <label for="status" class="control-label">Role:</label>
            <select name="role" id="role" class="form-control">
            <option value="">Please Select</option>
            <option value="Super Admin">Super Admin</option>
            <option value="Administrator">Administrator</option> 
            <option value="Supervisor">Supervisor</option> 
            </select>  
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-danger waves-effect waves-light">Save</button> 
            </div>
            </form>
            </div>
            </div>
            </div>
            <a class="btn dark btn-md pull-right" href="#create" data-toggle="modal">
            <i class="fa fa-plus"></i>   ADD NEW
            </a>  
			</div> 
        </div> 
    </div>
</div>

<div class="container-fluid">
     
<!-- ============================================================== -->
<!-- User Pending Review -->
<!-- ============================================================== --> 

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Member</h4>
								<div class="table-responsive">  
									<table id="zero_config" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                    <th>ID</th>
                                                    <th>Fullname</th>
                                                    <th>Username</th> 
                                                    <th>Role</th> 
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
												<td>{{$data->role}} </td> 
												<td>{{$data->email}}</td>  
												<td>{{$data->google_auth_code}}</td>  
												<td>{{$data->updated_at}}</td> 
												<td> 
												 
												<div id="passwordModal{{$data->id}}" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;"> 
												<div class="modal-dialog">
												<div class="modal-content">
												<form role="form" action="{{route('admin.member.password')}}" method="post">
												{{csrf_field()}}
												<div class="modal-header">
												<h4 class="modal-title">Change Password</h4>
												<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>  
												</div>
												<div class="modal-body">  
												<label for="username" class="control-label">New Password:</label>
												<input id="username" type="password" class="form-control" name="password" required> 
												 
												<label for="username" class="control-label">Confirmation Password:</label>
												<input id="username" type="password" class="form-control" name="confirm_password" required> 
												 
												</div>
												<div class="modal-footer">												
												<input id="id" type="hidden" class="form-control" name="id" value="{{$data->id}}">
												<button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
												<button type="submit" class="btn btn-danger waves-effect waves-light">Save</button> 
												</div>
												</form>
												</div>
												</div>
												</div> 
												 
												<div id="editModal{{$data->id}}" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;"> 
												<div class="modal-dialog">
												<div class="modal-content">
												<form role="form" action="{{route('admin.member.update')}}" method="post">
												{{csrf_field()}}
												<div class="modal-header">
												<h4 class="modal-title">Update</h4>
												<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>  
												</div>
												<div class="modal-body">  
												<label for="username" class="control-label">Username:</label>
												<input id="username" type="text" class="form-control" name="username" value="{{$data->username}}" required> 
												 
												<label for="username" class="control-label">Fullname:</label>
												<input id="username" type="text" class="form-control" name="name" value="{{$data->name}}" required> 
												
												<label for="email" class="control-label">Email:</label>
												<input id="email" type="text" class="form-control" name="email" value="{{$data->email}}" required data-validation-regex-regex="([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})" data-validation-regex-message="Enter Valid Email">
														   
												<label for="status" class="control-label">Role:</label>
												<select name="role" id="role" class="form-control">
												<option value="">Please Select</option>
												<option value="Super Admin" <?php if($data->role=='Super Admin'){ echo 'selected';} ?> >Super Admin</option>
												<option value="Administrator" <?php if($data->role=='Administrator'){ echo 'selected';} ?> >Administrator</option> 
												<option value="Supervisor" <?php if($data->role=='Supervisor'){ echo 'selected';} ?> >Supervisor</option> 
												</select>  
												</div>
												<div class="modal-footer">												
												<input id="id" type="hidden" class="form-control" name="id" value="{{$data->id}}">
												<button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
												<button type="submit" class="btn btn-danger waves-effect waves-light">Save</button> 
												</div>
												</form>
												</div>
												</div>
												</div> 
												 
												<div id="deleteModal{{$data->id}}" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
												<div class="modal-dialog">
												<div class="modal-content">
												<form role="form" action="{{route('admin.member.delete')}}" method="post">
                                                {{csrf_field()}}
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                                    <h2 class="modal-title" style="color: red;">Are you sure to DELETE?</h2>
													<input type="hidden" value="{{$data->id}}" name="id">  
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" data-dismiss="modal" class="btn default">Cancel</button>
                                                    <button type="submit" class="btn red" id="delete"><i class="fa fa-trash"></i> Delete</button>
                                                </div>
												</form>
												</div>
												</div>
												</div>  
												
												<a class="btn blue" href="#passwordModal{{$data->id}}" data-toggle="modal"><i class="fas fa-key"></i></a> 
												<a class="btn blue" href="#editModal{{$data->id}}" data-toggle="modal"><i class="fas fa-edit"></i></a> 
												<?php if($data->status=='active'){ ?> 
												<a class="btn blue" href="#deleteModal{{$data->id}}" data-toggle="modal"><i class="fas fa-trash"></i></a>  
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
 

@endsection