 @extends('admin.layouts.template')

@section('content') 
  

<link rel="license" href="https://www.gnu.org/licenses/gpl-3.0.html" title="GNU GPL 3.0 or later"> 
 
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdn.rawgit.com/lrsjng/jquery-qrcode/v0.14.0/dist/jquery-qrcode.min.js" integrity="sha384-BrZGf2D/R3HVPz9JtfTVrbaUyVVaFKZWO2MTcoL80nBcGZBsRhbjCSsFXUGAEO45" crossorigin="anonymous"></script>
<script src="https://rawgit.com/emn178/hi-base32/master/build/base32.min.js"></script>


<div class="container-fluid">
     
<!-- ============================================================== -->
<!-- User Pending Review -->
<!-- ============================================================== --> 

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Profile</h4>
								<p></p>
                            <hr class="m-t-0"> 
							  
                               <form class="form-horizontal striped-rows b-form" >
                     
								 <div class="form-group row">
                                        <div class="col-sm-3">
                                            <div class="b-label">
                                                <label for="inputEmail3" class="control-label col-form-label">Fullname</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" value="{{$user->name}}" readonly>
                                        </div>
                                    </div>
									
									<div class="form-group row">
                                        <div class="col-sm-3">
                                            <div class="b-label">
                                                <label for="inputEmail3" class="control-label col-form-label">Username</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" value="{{$user->username}}" readonly>
                                        </div>
                                    </div>
									
									<div class="form-group row">
                                        <div class="col-sm-3">
                                            <div class="b-label">
                                                <label for="inputEmail3" class="control-label col-form-label">Role</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" value="{{$user->role}}" readonly>
                                        </div>
                                    </div>
									
									<div class="form-group row">
                                        <div class="col-sm-3">
                                            <div class="b-label">
                                                <label for="inputEmail3" class="control-label col-form-label">Email</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" value="{{$user->email}}" readonly>
                                        </div>
                                    </div>
									
									<div class="form-group row">
                                        <div class="col-sm-3">
                                            <div class="b-label">
                                                <label for="inputEmail3" class="control-label col-form-label">Status</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" value="{{$user->status}}" readonly>
                                        </div>
                                    </div>
								</form>
								</div>	
								
								<hr>
                                <div class="card-body">
                                    <h4 class="card-title">Change Password</h4>	
									<p></p>
                            <hr class="m-t-0">
                              @include('partials.errors')
									 
                               <form action="{{route('admin.personal.password')}}" method="POST" class="form-horizontal striped-rows b-form" >
                        {{csrf_field()}}
						
									<div class="form-group row">
                                        <div class="col-sm-3">
                                            <div class="b-label">
                                                <label for="inputEmail3" class="control-label col-form-label">New Password</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="password" >
                                        </div>
                                    </div>
									
									<div class="form-group row">
                                        <div class="col-sm-3">
                                            <div class="b-label">
                                                <label for="inputEmail3" class="control-label col-form-label">Confirmation Password</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="confirmed" >
                                        </div>
                                    </div>
									
								
								<hr>	 
                                <div class="card-body">
                                    <div class="form-group m-b-0 text-right">
                                        <button type="submit" class="btn btn-info waves-effect waves-light">Save Change</button> 
                                    </div>
                                </div>
								</form>
								</div>	
												
								<hr>
                                <div class="card-body">
                                    <h4 class="card-title">Google Authentication</h4>	
									<p></p>
                            <hr class="m-t-0"> 
							
									<div class="form-group row">
                                        <div class="col-sm-3">
                                             
                                        </div>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control" value="{{$user->google_auth_code}}" >
											<p>
											<div style="padding:10px;text-align:center;"> <div id="qrrCode"></div> </div>
						 <script>
// make a nice QR code as the favicon
var ic_code = '#qrrCode';

 $(ic_code).empty().qrcode({
    text: 'otpauth://totp/<?php echo $user->username;?>?secret=<?php echo $user->google_auth_code; ?>&issuer=<?php echo $issuer;?>'
  });

$("link[rel=icon]").prop("href", $("#qrr img").prop("src"));

</script>
											</p>
                                        </div>
                                        <div class="col-sm-3">
                                             
                                        </div>
                                    </div>
									 
									
								</div>	 
								
                            </div>
                        </div>
                    </div>
                </div>
                 
</div>
 

@endsection