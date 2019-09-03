<!DOCTYPE html>
<html dir="ltr" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset('asset/assets/images/favicon.png')}}">
    <title>DORADO</title>
 
    <!-- notify -->  
	 <link href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css" rel="stylesheet" />
   <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
	   
    <link href="{{asset('asset/assets/extra-libs/c3/c3.min.css')}}" rel="stylesheet"> 
    <!-- Custom CSS -->
    <link href="{{asset('asset/dist/css/style.min.css')}}" rel="stylesheet">  
   
    <!--datatable-->     
    <link href="{{asset('asset/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.css')}}" rel="stylesheet">
        <!-- CORE JS FRAMEWORK - START -->  
 
</head>

<body>
    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->
    <div class="preloader">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div>

    <div id="main-wrapper">
        <header class="topbar">
            <nav class="navbar top-navbar navbar-expand-md navbar-dark">
             
            <!-- ============================================================== -->
            <!-- End Logo -->
            <!-- ============================================================== -->
            <div class="navbar-collapse collapse" id="navbarSupportedContent">
                <!-- ============================================================== -->
                <!-- toggle and nav items -->
                <!-- ============================================================== -->
                <ul class="navbar-nav float-left mr-auto">
                    <li class="nav-item d-none d-md-block">
                         
                    </li> 
                                                <!-- ============================================================== -->
                                                <!-- End Comment -->
                                                <!-- ============================================================== -->
                                            </ul>
                                            <!-- ============================================================== -->
                                            <!-- Right side toggle and nav items -->
                                            <!-- ============================================================== -->
                                            <ul class="navbar-nav float-right"> 
                                                <li class="nav-item dropdown">
                                                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown2" role="button" data-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false"> 
                                                    <!--<i class="flag-icon flag-icon-my font-18"></i>-->
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right  animated bounceInDown" aria-labelledby="navbarDropdown2">
                                                    <a class="dropdown-item" href="#">
                                                        <i class="flag-icon flag-icon-us"></i> English</a> 
                                                                </div>
                                                            </li>


                                                            <li class="nav-item dropdown">
                                                                <a class="nav-link dropdown-toggle text-muted waves-effect waves-dark pro-pic" href="" data-toggle="dropdown" aria-haspopup="true"
                                                                aria-expanded="false">
                                                                <img src="{{asset('asset/user.png')}}" alt="user" class="rounded-circle" width="31">
                                                            </a>
                                                            <div class="dropdown-menu dropdown-menu-right user-dd animated flipInY">
                                                                <span class="with-arrow">
                                                                    <span class="bg-primary"></span>
                                                                </span>
                                                                <div class="d-flex no-block align-items-center p-15 bg-primary text-white m-b-10">
                                                                     
                                                                    <div class="m-l-10">
																	<?php 
																	$auth_id = Auth::id();
																	$user = \App\User::where('id',$auth_id)->first(); ?>
                                                                        <h4 class="m-b-0"> {{$user->name}}</h4>
                                                                        <p class=" m-b-0"> {{$user->username}}</p> 
                                                                    </div>

                                                                </div> 
                                                                                <div class="dropdown-divider"></div>
                                                                                <a class="dropdown-item" href="{{route('logout')}}" >
                                                                                    <i class="fa fa-power-off m-r-5 m-l-5"></i> Logout</a> 
                                                                                </div>
                                                                            </li>
                                                                            <!-- ============================================================== -->
                                                                            <!-- User profile and search -->
                                                                            <!-- ============================================================== -->
                                                                        </ul>
                                                                    </div>
                                                                </nav>
                                                            </header>
												
												@yield('contents')  

                                                            <footer class="footer text-center">
                                                                Powered by 
                                                                <a href="https://pinkexc.biz">www.pinkexc.biz</a>.
                                                            </footer>
                                                        </div>
                                                        <!-- ============================================================== -->
                                                        <!-- End Page wrapper  -->
                                                        <!-- ============================================================== -->
                                                    </div>
                                                    <!-- ============================================================== -->
                                                    <!-- All Jquery -->
                                                    <!-- ============================================================== -->
                                                    <script src="{{asset('asset/assets/libs/jquery/dist/jquery.min.js')}}"></script>
                                                    <!-- Bootstrap tether Core JavaScript -->
                                                    <script src="{{asset('asset/assets/libs/popper.js/dist/umd/popper.min.js')}}"></script>
                                                    <script src="{{asset('asset/assets/libs/bootstrap/dist/js/bootstrap.min.js')}}"></script>
                                                    <!-- apps -->
                                                    <script src="{{asset('asset/dist/js/app.min.js')}}"></script>
                                                    <!-- box layout -->
    <script>
    $(function() {
        "use strict";
        $("#main-wrapper").AdminSettings({
            Theme: false, // this can be true or false ( true means dark and false means light ),
            Layout: 'vertical',
            LogoBg: 'skin1', // You can change the Value to be skin1/skin2/skin3/skin4/skin5/skin6 
            NavbarBg: 'skin6', // You can change the Value to be skin1/skin2/skin3/skin4/skin5/skin6
            SidebarType: 'overlay', // You can change it full / mini-sidebar / iconbar / overlay
            SidebarColor: 'skin1', // You can change the Value to be skin1/skin2/skin3/skin4/skin5/skin6
            SidebarPosition: false, // it can be true / false ( true means Fixed and false means absolute )
            HeaderPosition: false, // it can be true / false ( true means Fixed and false means absolute )
            BoxedLayout: true, // it can be true / false ( true means Boxed and false means Fluid ) 
        });
    });
    </script>
                                                
                                                    <!-- slimscrollbar scrollbar JavaScript -->
                                                    <script src="{{asset('asset/assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js')}}"></script>
                                                    <script src="{{asset('asset/assets/extra-libs/sparkline/sparkline.js')}}"></script>
                                                    <!--Wave Effects -->
                                                    <script src="{{asset('asset/dist/js/waves.js')}}"></script>
                                                    <!--Menu sidebar -->
                                                    <script src="{{asset('asset/dist/js/sidebarmenu.js')}}"></script>
                                                    <!--Custom JavaScript -->
                                                    <script src="{{asset('asset/dist/js/custom.min.js')}}"></script>  
                                                    
                                                    <script src="{{asset('asset/dist/js/pages/dashboards/dashboard1.js')}}"></script>
													
													<!--form validation -->
													<script src="{{asset('asset/assets/extra-libs/jqbootstrapvalidation/validation.js')}}"></script>

                                                    <!----datatabel---->

                                                    <!--This page plugins --> 
                                                    <script src="{{asset('asset/assets/extra-libs/DataTables/datatables.min.js')}}"></script>
                                                    <script src="{{asset('asset/dist/js/pages/datatable/datatable-basic.init.js')}}"></script>
												 
	
	 <script src="{{asset('asset/assets/libs/tinymce/tinymce.min.js')}}"></script>
    <script>
    $(document).ready(function() {

        if ($("#mymce").length > 0) {
            tinymce.init({
                selector: "textarea#mymce",
                theme: "modern",
                height: 300,
                plugins: [
                    "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
                    "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
                    "save table contextmenu directionality template paste textcolor"
                ],
                toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | l      ink image | print preview media fullpage | forecolor backcolor emoticons",

            });
			
			//make tinymce function in modal dialog
			$(document).on('focusin', function(e) {
		if ($(event.target).closest(".mce-window").length) {
			e.stopImmediatePropagation();
		}
	});

        }
    });
	 
    </script>
	
	@if (notify()->ready())
    <script>
        swal({
            title: "{!! notify()->message() !!}",
            text: "{!! notify()->option('text') !!}",
            type: "{{ notify()->type() }}",
            @if (notify()->option('timer'))
                timer: {{ notify()->option('timer') }},
                showConfirmButton: false
            @endif
        });
    </script>
	@endif

	 
                                                </body>

                                                </html>