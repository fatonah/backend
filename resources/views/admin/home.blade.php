
@extends('admin.layouts.template')

@section('content')

            <div class="container-fluid">
                <!-- ============================================================== -->
                <!-- Welcome back  -->
                <!-- ============================================================== -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card  bg-light no-card-border">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                   
                                    <div>
                                        <h3 class="m-b-0">Hi <?php echo Auth::guard('admin')->user()->name;?>, Have a Good Day !</h3>
                                        <span>
                                                <?php                        
    date_default_timezone_set("Asia/Kuala_Lumpur");
    echo date("l");
    echo '<br>';
    echo date("j F, Y, g:i a");
?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ============================================================== -->
                <!-- Sales Summery -->
                <!-- ============================================================== -->

             
                <div class="card-group">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <h3>{{sprintf('%f', $balanceBTC)}} </h3>
                                    <h6 class="card-subtitle">Balance BTC</h6>
                                </div>
                                <div class="col-12">
                                    <div class="progress">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: 100%; height: 6px;" aria-valuenow="25" aria-valuemin="0"
                                            aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row">

                                <div class="col-12 text-center">
                                     <h6 class="card-subtitle">{{$addressBTC}}</h6>
                                    <img width="50%" src="https://api.qrserver.com/v1/create-qr-code/?data=<?php echo $addressBTC; ?>&amp;size=100x100"> 
                                </div>
                            </div>
                        </div>

                    </div>
                    <!-- Column -->

                   
                    <!-- Column -->
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <h3>{{sprintf('%f', $balanceBCH)}}</h3>
                                    <h6 class="card-subtitle">BCH</h6>
                                </div>
                                <div class="col-12">
                                    <div class="progress">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: 100%; height: 6px;" aria-valuenow="25" aria-valuemin="0"
                                            aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row">

                                <div class="col-12 text-center">
                                    <h6 class="card-subtitle">{{$addressBCH}}</h6>
                                   <img width="50%" src="https://api.qrserver.com/v1/create-qr-code/?data=<?php echo $addressBCH; ?>&amp;size=100x100"> 
                               </div>
                           </div>
                       </div>

                    </div>
                    <!-- Column -->
                    <!-- Column -->
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <h3>{{sprintf('%f', $balanceDOGE)}}</h3>
                                    <h6 class="card-subtitle">DOGE</h6>
                                </div>
                                <div class="col-12">
                                    <div class="progress">
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: 100%; height: 6px;" aria-valuenow="25" aria-valuemin="0"
                                            aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
  <div class="card-body">
                            <div class="row">

                                <div class="col-12 text-center">
                                    <h6 class="card-subtitle">{{$addressDOGE}}</h6>
                                   <img width="50%" src="https://api.qrserver.com/v1/create-qr-code/?data=<?php echo $addressDOGE; ?>&amp;size=100x100"> 
                               </div>
                           </div>
                       </div>


                    </div>
                    <!-- Column -->
                    <!-- Column -->
                
                </div>
         
           
                <!-- ============================================================== -->
                <!-- Task, Feeds -->
                <!-- ============================================================== -->
                <div class="row">
                   
                </div>
                
            </div>
            <!-- ============================================================== -->
            <!-- Trade history / Exchange -->
            <!-- ============================================================== -->
        </div>

        @endsection