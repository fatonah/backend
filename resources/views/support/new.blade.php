@extends('layouts.default')

@section('contents') 
 
<div class="page-wrapper">
 
<div class="page-breadcrumb">
    <div class="row">
        <div class="col-5 align-self-center">
            <h4 class="page-title"></h4>
            <div class="d-flex align-items-center">

            </div>
        </div>
        <div class="col-7 align-self-center">
            <div class="d-flex no-block justify-content-end align-items-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb"> 
                        <li class="breadcrumb-item active" aria-current="page"><a href="{{route('support.list',$user->hash)}}">Home</a></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>
 
<div class="container-fluid"> 
 
<div class="row"> 
                    <!-- column -->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">New Support</h4>  
									 @include('partials.errors')
                                <form role="form" action="{{route('support.store')}}" method="post" enctype="multipart/form-data">
									{{csrf_field()}}  
									<p></p>			
									<div class="row">
										<div class="col-md-12">
										<div class="form-group">
										<h5>Subject *</h5>
										<div class="controls"> 
										<input type="text" class="form-control" name="subject" id="subject" > </div>
										</div>
										</div> 
									</div> 
									<?php $url = url('/get/data');?>
									<div class="row">
										<div class="col-md-12">
										<div class="form-group">
										<h5>Type *</h5>
										<div class="controls"> 
										<select class="form-control" id="type" name="type" onchange="showTitle(this.value,'{{$url}}')">
									<option value="">Select</option> 
									<?php   
										 foreach ($menuT as $data) {  
									?> 
									<option value="{{$data->id}}">{{$data->title}}</option> 
									<?php   
										}
									?> 
									</select>
										</div>
										</div>
										</div> 
									</div> 
									
									<div id="txtHint"></div>
									 
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
						  
								  <div class="text-xs-right"> 
									 <button type="submit" class="btn btn-info" name="btn_save">Save changes</button> 
								  </div> 
								</form> 
							</div>
                        </div> 
                    </div> 
</div>

 
 <script>
          function showTitle(str,url) {


            if (str == "") {
              document.getElementById("txtHint").innerHTML = "";
              return;
            } 
            else { 
              if (window.XMLHttpRequest) {
              // code for IE7+, Firefox, Chrome, Opera, Safari
              xmlhttp = new XMLHttpRequest();
              } else {
              // code for IE6, IE5
              xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
              }

              xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                document.getElementById("txtHint").innerHTML = this.responseText;
                }
              };

             // xmlhttp.open("GET","http://localhost/loginUser/public/get/data/"+str,true);
            //xmlhttp.open("GET","https://colony.pinkexc.com:5454/get/data/"+str,true);

			xmlhttp.open("GET",url+'/'+str,true);

              xmlhttp.send();
            }
          }
</script>

@endsection