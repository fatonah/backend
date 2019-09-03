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
                               
                                <ul class="nav nav-pills m-t-30 m-b-30"> 
									<li class=" nav-item"> <a href="#level-1" class="nav-link active" data-toggle="tab" aria-expanded="false">Main</a> </li>
									<li class=" nav-item"> <a href="#level-2" class="nav-link" data-toggle="tab" aria-expanded="false">Crypto</a> </li>   
                                </ul>
								
								 @include('partials.errors')
								 
								<form action="{{route('admin.setting.update')}}" method="post" class="m-t-40" novalidate>  
									{{csrf_field()}} 
									
                                <div class="tab-content br-n pn">
								
								<!--Level 1--> 
                                    <div id="level-1" class="tab-pane active">
                                        <div class="row">
                                            <div class="col-md-12">  
											 <div class="form-group">
												<h5>Title </h5>
												<div class="controls">
												<input type="text" class="form-control" name="title" value="{{$settings->title}}">
		                                	    </div>
												</div>
												
												<div class="form-group">
												<h5>Description </h5>
												<div class="controls">
												<textarea class="form-control" name="description" rows="2">{{$settings->description}}</textarea>
												</div>
												</div>
												
												<div class="form-group">
												<h5>Keywords </h5>
												<div class="controls">
												<textarea class="form-control" name="keywords" rows="2">{{$settings->keywords}}</textarea>
		                                	    </div>
												</div>
												
												<div class="form-group">
												<h5>Site Name </h5>
												<div class="controls">
												<input type="text" class="form-control" name="name" value="{{$settings->name}}">
		                                	    </div>
												</div> 
												
												<div class="form-group">
												<h5>Site URL Address </h5>
												<div class="controls">
												<input type="text" class="form-control" name="url" value="{{$settings->url}}">
		                                	    </div>
												</div> 
												
												<div class="form-group">
												<h5>Info Email Address </h5>
												<div class="controls">
												<input type="email" class="form-control" name="infoemail" value="{{$settings->infoemail}}" data-validation-regex-regex="([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})" data-validation-regex-message="Enter Valid Email">
		                                	    </div>
												</div> 
												
												<div class="form-group">
												<h5>Support Email Address </h5>
												<div class="controls">
												<input type="email" class="form-control" name="supportemail" value="{{$settings->supportemail}}" data-validation-regex-regex="([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})" data-validation-regex-message="Enter Valid Email">
		                                    	</div>
												</div>
									
											</div>
										</div>
									</div>
									
									
								<!--Level 2--> 
                                    <div id="level-2" class="tab-pane">
                                        <div class="row">
                                            <div class="col-md-12">  
											 <div class="form-group"> 
												<h5>Commission Withdrawal </h5>
												<div class="controls">
												<div class="input-group mb-12">
												<div class="input-group-append">
												<button class="btn btn-info" type="button"> RM </button>
											    </div>
												<input type="text" class="form-control" placeholder="" aria-label="" aria-describedby="basic-addon1" name="commission_withdraw" value="{{$settings->commission_withdraw}}">
												</div> 
		                                	    </div>
												</div>
												 
												<div class="form-group"> 
												<h5>Network Fee Crypto </h5>
												<div class="controls">
												<div class="input-group mb-12">
												<div class="input-group-append">
												<button class="btn btn-info" type="button"> BTC </button>
											    </div>
												<input type="text" class="form-control" placeholder="" aria-label="" aria-describedby="basic-addon1" name="fee_btc" value="{{$settings->fee_btc}}">
												</div>
												</div>
												</div>
												
												<div class="form-group"> 
												<div class="controls">
												<div class="input-group mb-12">
												<div class="input-group-append">
												<button class="btn btn-info" type="button"> BCH </button>
											    </div>
												<input type="text" class="form-control" placeholder="" aria-label="" aria-describedby="basic-addon1" name="fee_bch" value="{{$settings->fee_bch}}">
												</div>
												</div>
												</div>
												
												<div class="form-group"> 
												<div class="controls">
												<div class="input-group mb-12">
												<div class="input-group-append">
												<button class="btn btn-info" type="button"> DOGE </button>
											    </div>
												<input type="text" class="form-control" placeholder="" aria-label="" aria-describedby="basic-addon1" name="fee_doge" value="{{$settings->fee_doge}}">
												</div>
												</div>
												</div>
									
											</div>
										</div>
									</div>
									 
									
									
								</div>  <!-- end tab -->
								
										<div class="text-xs-right">
												<button type="submit" class="btn btn-info">Submit Verify</button> 
										</div>
									
								</form>
	
	
								
							</div>	
								 
								
                            </div>
                        </div>
                    </div>
                </div>
                 
</div>
 

@endsection