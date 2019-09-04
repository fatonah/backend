@extends('admin.layouts.template')
 
@section('content')

     <div class="container-fluid">
                <!-- ============================================================== -->
                <!-- Start Page Content -->
                <!-- ============================================================== -->
                <!-- basic table -->
               <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">{{$label}} | List Transactions {{$crypto}}</h4>
                                
                                <div class="table-responsive">
                                      <table id="file_export" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                      <th>From</th>
                                                    <th>To</th>
                                                    <th>Category</th>
                                                    <th>Fees</th>
                                                    <th>Amount</th> 
                                                    <th>Balance</th> 
                                                    <th>Confirmations</th>
                                                    <th>TXID</th>
                                                    <th>Date and Time</th>
                                            </tr>
                                        </thead>
                                         <tbody>
										<?php  
                                        $i=1;   
                                        
                                        if($trans!=null){
                                            if(isset($trans[0])){
                                                $balafter = $trans[0]['amount'];
                                            }else{
                                                $balafter = $trans['amount'];
                                            }
                                           
                                  ///  foreach ( $trans as $key => $arr_datas) {
 ?>
 <tr><td colspan="10">{{$trans['category']}}</td></tr>
									 
									<?php $i++; 
                                   // } 
                                    } ?> 
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

</div>

@endsection

 