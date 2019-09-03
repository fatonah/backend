 
        <aside class="left-sidebar">
            <!-- Sidebar scroll-->
            <div class="scroll-sidebar">
                <!-- Sidebar navigation-->
                <nav class="sidebar-nav">
                    <ul id="sidebarnav">
                        <!-- User Profile-->
                        <li>
                            <!-- User Profile-->
                            <div class="user-profile dropdown m-t-20">
                                <div class="user-pic">
                                    <img src="{{asset('asset/admin.png')}}" alt="users" class="rounded-circle img-fluid" />
                                </div>
                                <div class="user-content hide-menu m-t-10">
                                    <h5 class="m-b-10 user-name font-medium"><?php echo Auth::guard('admin')->user()->email;?></h5>
                                    <a href="javascript:void(0)" class="btn btn-circle btn-sm m-r-5" id="Userdd" role="button" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false">
                                        <i class="ti-settings"></i>
                                    </a>
                                    <a href="{{ route('admin.logout') }}" title="Logout" class="btn btn-circle btn-sm">
                                        <i class="ti-power-off"></i>
                                    </a>
                                    <div class="dropdown-menu animated flipInY" aria-labelledby="Userdd">
                                        <a class="dropdown-item" href="{{ route('admin.personal') }}">
                                            <i class="ti-user m-r-5 m-l-5"></i> My Profile</a>
                                        <a class="dropdown-item" href="{{ route('admin.personal') }}">
                                            <i class="ti-key m-r-5 m-l-5"></i> Change Password</a>
                                        <a class="dropdown-item" href="{{ route('admin.personal') }}">
                                            <i class="ti-world m-r-5 m-l-5"></i> 2Fa</a>
                                        <div class="dropdown-divider"></div> 
                                        <a class="dropdown-item" href="{{ route('admin.logout') }}">
                                            <i class="fa fa-power-off m-r-5 m-l-5"></i> Logout</a>
                                    </div>
                                </div>
                            </div>
                            <!-- End User Profile-->
                        </li>
                        <!-- User Profile-->
                        <li class="nav-small-cap">
                            <i class="mdi mdi-dots-horizontal"></i>
                            <span class="hide-menu">HOME</span>
                        </li>


                           <li class="sidebar-item">
                                    <a href="{{route('admin.dashboard')}}" class="sidebar-link">
                                        <i class="icon-Record"></i>
                                        <span class="hide-menu"> Dashboard </span>
                                    </a>
                                </li>
								 

   <hr>
                                <li class="nav-small-cap">
                                    <i class="mdi mdi-dots-horizontal"></i>
                                    <span class="hide-menu">Admin Panel </span>
                                </li>
			
							   @php
							   $private = Auth::guard('admin')->user()->role;
							   if($private=='Administrator' || $private=='Super Admin'){
							   @endphp
							   
                        <li class="sidebar-item">
                                    <a href="{{route('admin.member.list')}}" class="sidebar-link">
                                        <i class="icon-Add-UserStar"></i>
                                        <span class="hide-menu"> Member </span>
                                    </a>
                                </li>

					<li class="sidebar-item">
                            <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false">
                                <i class=" icon-Arrow-Down3"></i>
                                <span class="hide-menu"> Send</span>
                            </a>
                            <ul aria-expanded="false" class="collapse  first-level">
                                <li class="sidebar-item">
                                    <a href="{{route('admin.send', 'BTC')}}" class="sidebar-link">
                                        <i class="icon-Record"></i>
                                        <span class="hide-menu"> BTC </span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="{{route('admin.send', 'BCH')}}" class="sidebar-link">
                                        <i class="icon-Record"></i>
                                        <span class="hide-menu"> BCH </span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="{{route('admin.send', 'DOGE')}}" class="sidebar-link">
                                        <i class="icon-Record"></i>
                                        <span class="hide-menu"> DOGE </span>
                                    </a>
                                </li>
                            </ul>
                        </li>
							   @php
							   }
							   @endphp
						
						
                        <li class="sidebar-item">
                            <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false">
                                <i class="icon-Car-Wheel"></i>
                                <span class="hide-menu">Transaction Admin</span>
                            </a>
                            <ul aria-expanded="false" class="collapse  first-level">
                                <li class="sidebar-item">
                                    <a href="{{route('admin.transactions', 'BTC')}}" class="sidebar-link">
                                        <i class="icon-Record"></i>
                                        <span class="hide-menu"> BTC </span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="{{route('admin.transactions', 'BCH')}}" class="sidebar-link">
                                        <i class="icon-Record"></i>
                                        <span class="hide-menu"> BCH </span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="{{route('admin.transactions', 'DOGE')}}" class="sidebar-link">
                                        <i class="icon-Record"></i>
                                        <span class="hide-menu"> DOGE </span>
                                    </a>
                                </li>

                            </ul>
                        </li>


   <hr>
                                <li class="nav-small-cap">
                                    <i class="mdi mdi-dots-horizontal"></i>
                                    <span class="hide-menu">User Panel </span>
                                </li>
						

                        <li class="sidebar-item">
                                    <a href="{{route('admin.user.list')}}" class="sidebar-link">
                                        <i class="icon-Add-UserStar"></i>
                                        <span class="hide-menu"> User </span>
                                    </a>
                                </li>
                      
    <!---------transaction user-----> 
                        <li class="sidebar-item">
                            <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false">
                                <i class="icon-Paint-Brush"></i>
                                <span class="hide-menu">Transaction Users</span>
                            </a>
                            <ul aria-expanded="false" class="collapse first-level">
                                <li class="sidebar-item">
                                    <a href="{{route('users.list', 'BTC')}}" class="sidebar-link">
                                        <i class="mdi mdi-toggle-switch"></i>
                                        <span class="hide-menu"> BTC</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="{{route('users.list', 'BCH')}}" class="sidebar-link">
                                        <i class="mdi mdi-tablet"></i>
                                        <span class="hide-menu"> BCH</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="{{route('users.list', 'DOGE')}}" class="sidebar-link">
                                        <i class="mdi mdi-sort-variant"></i>
                                        <span class="hide-menu"> DOGE</span>
                                    </a>
                                </li>
                              
                            </ul>
                        </li>

                      
    <!---------support----->
         
 <hr>
                                <li class="nav-small-cap">
                                    <i class="mdi mdi-dots-horizontal"></i>
                                    <span class="hide-menu">Other </span>
                                </li>
 
<?php
$count_msj = \App\Ticket::where('status','Open')->orWhere('status','Awaiting Reply')->count();
 
?>

                        <li class="sidebar-item">
                                    <a href="{{route('admin.support.list')}}" class="sidebar-link">
                                        <i class="icon-Support"></i>
                                        <span class="hide-menu"> Support &nbsp;&nbsp;<span class="badge" style="background-color:red;"> {{$count_msj}} </span> </span>
                                    </a>
                                </li>
                         
                        <li class="sidebar-item">
                                    <a href="{{route('admin.setting.view')}}" class="sidebar-link">
                                        <i class="icon-Gear"></i>
                                        <span class="hide-menu"> Setting </span>
                                    </a>
                                </li>
								
                    </ul>
                </nav>
                <!-- End Sidebar navigation -->
            </div>
            <!-- End Sidebar scroll-->
        </aside>
      