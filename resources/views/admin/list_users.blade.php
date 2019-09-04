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
                                <h4 class="card-title">List Transactions {{$crypto}}</h4>
                                
                                <div class="table-responsive">
                                      <table id="file_export" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                            	<th>No</th>
                                                      <th>From</th>
                                                    <th>To</th>
                                                    <th>Category</th>
                                                    <th>Amount</th> 
                                                    <th>Comment</th> 
                                                    <th>Confirmations</th>
                                                    <th>TXID</th>
                                                    <th>Date and Time</th>
                                                
                                            </tr>
                                        </thead>
                                        <tbody>
              <?php 
									 
                                        $i=1; 
                                        if($trans!=null){

                                    foreach ( $trans as $key => $arr_datas) { 
                                        
									 if($crypto!='BTC'){
										 $user_acc = $arr_datas['account'];
									 }else{
										 $user_acc = '';
                                     }
                                      
                                            ?>  
											
                                            <tr>
                                                <td><?php echo $i; ?></td>  
                                    <td><?php if($user_acc !=''){ ?><a href="{{route('users.transactions',[$crypto,$user_acc])}}"><?php echo $user_acc; ?></a><?php } ?></td>
                                            <td><?php if(isset($arr_datas['label'])){echo $arr_datas['label'];}elseif(isset($arr_datas['otheraccount'])){echo $arr_datas['otheraccount'];}?></td>
										  <td><?php if($arr_datas['category']=='move'){echo 'fees';}else{echo $arr_datas['category'];} ?></td>
                                             <td><?php echo $arr_datas['amount']; ?></td>
                                             <td><?php if(isset($arr_datas['comment'])){echo $arr_datas['comment'];} ?></td>
                                              <td><?php if(isset($arr_datas['confirmations'])){echo $arr_datas['confirmations'];} ?></td>
                                               <td><?php if(isset($arr_datas['txid'])){
												     if($crypto!='BCH'){ ?>
								  <a target="_blank" href="https://live.blockcypher.com/{{strtolower($crypto)}}/tx/{{ $arr_datas['txid'] }}">{{ $arr_datas['txid'] }}</a>
												<?php }else{ ?>
								  <a target="_blank" href="https://www.blockchain.com/bch/tx/{{ $arr_datas['txid'] }}">{{ $arr_datas['txid'] }}</a>
												<?php } 
												    } ?></td>
                                               <td><?php if(isset($arr_datas['timereceived'])){echo date('d-m-Y H:i:s',$arr_datas['timereceived']);}elseif(isset($arr_datas['time'])){echo date('d-m-Y H:i:s',$arr_datas['time']);} ?></td>
                                            </tr>
									<?php $i++; 
									}} ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

</div>

@endsection

 