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
                                           
                                    foreach ( $trans as $key => $arr_datas) {
 
									if(isset($arr_datas['fee'])){$fee = $arr_datas['fee'];}else{$fee=0;}
									if($i==1){  

											if($arr_datas['category']=='move'){ 										   
										  $val = substr($arr_datas['amount'], 0, 1);  
										  if($val=='-'){$fromacc = $arr_datas['account'] ; $toacc = $arr_datas['otheraccount'] ;}
										  else{$fromacc = $arr_datas['otheraccount'] ; $toacc = $arr_datas['account']; }
									   }
									   
										}else{
                                       if($arr_datas['category']=='send'){
										$balafter = $balafter +($arr_datas['amount']) + $fee;
									   }elseif($arr_datas['category']=='receive'){
										 $balafter = $balafter +($arr_datas['amount']) + $fee;  
									   }elseif($arr_datas['category']=='move'){ 										   
										  $val = substr($arr_datas['amount'], 0, 1);  
										  if($val=='-'){$balafter = $balafter +($arr_datas['amount']) + $fee; $fromacc = $arr_datas['account'] ; $toacc = $arr_datas['otheraccount'] ;}
										  else{$balafter = $balafter +($arr_datas['amount']) + $fee;  $fromacc = $arr_datas['otheraccount'] ; $toacc = $arr_datas['account']; }
									   }
										}
                                            ?>  
											
                                            <tr>
                                                <td><?php echo $i; ?></td> 
                                          <?php if($arr_datas['category']=='receive'){ 
					 $trans = gettransaction_crypto(strtoupper($crypto), $arr_datas['txid']); 
					 ?>
                                         <td><?php echo $trans['details'][0]['account']; ?></td>
					 <?php }else if($arr_datas['category']=='move'){ ?>
										<td><?php echo $fromacc; ?></td>
					 <?php }else{ ?>
                                         <td><?php echo $arr_datas['account']; ?></td>
					 <?php } ?>

					
                                            <td><?php if($arr_datas['category']=='move'){ echo $toacc;}else if(isset($arr_datas['label'])){echo $arr_datas['label'];}elseif(isset($arr_datas['otheraccount'])){echo $arr_datas['otheraccount'];}else{echo $arr_datas['address'];} ?></td>
                                            <td><?php echo $arr_datas['category']; ?></td>
                                             <td><?php echo $fee; ?></td>
                                             <td><?php echo $arr_datas['amount']; ?></td>
                                             <td><?php echo $balafter; ?></td>
                                              <td><?php if(isset($arr_datas['confirmations'])){echo $arr_datas['confirmations'];} ?></td>
                                               <td><?php if(isset($arr_datas['txid'])){
												   if($crypto!='BCH'){ ?>
								  <a target="_blank" href="https://live.blockcypher.com/{{strtolower($crypto)}}/tx/{{ $arr_datas['txid'] }}">{{ $arr_datas['txid'] }}</a>
												<?php }else{ ?>
								  <a target="_blank" href="https://www.blockchain.com/bch/tx/{{ $arr_datas['txid'] }}">{{ $arr_datas['txid'] }}</a>
												<?php }  
												   } ?></td>
                                               <td><?php if(isset($arr_datas['timereceived'])){echo date('Y-m-d H:i:s',$arr_datas['timereceived']);}elseif(isset($arr_datas['time'])){echo date('Y-m-d H:i:s',$arr_datas['time']);} ?></td>
                                            </tr>
									<?php $i++; 
                                    } 
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

 