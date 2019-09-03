
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
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Send {{$crypto}}</h4>
                                <br>
                                 
                                <div class="col-lg-12 col-md-8">

                                  
 
                       </div>
                               <h6 class="card-title">Your Balance :  <b>{{sprintf('%f', $balance)}}</b></h6>
							   
                              @include('partials.errors')
							  
                               <form action="{{route('admin.send.submit')}}" method="POST" class="m-t-30" >
                        {{csrf_field()}}
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">From Address</label>
                                        <input type="text" class="form-control" name="from" aria-describedby="emailHelp" value="{{$fromaddress->address}}" readonly>
                                        
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">To Address</label>
                                        <input type="text" class="form-control" name="to" required>
                                    </div>

                                     <div class="form-group">
                                        <label for="exampleInputPassword1">Amount</label>
                                        <input type="text" class="form-control" name="amount" required>
                                    </div>

                                     <div class="form-group">
                                        <label for="exampleInputPassword1">Verify Two Factor Authenticator</label>
                                        <input type="text" class="form-control" name="code" required>
                                    </div>

                                   
                                        <input type="hidden" class="form-control" name="crypto" value="{{$crypto}}" required>
                                    <button type="submit" class="btn btn-info">Send</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                        </div>
                    </div>
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