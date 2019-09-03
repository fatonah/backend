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
									<li class=" nav-item"> <a href="#level-1" class="nav-link active" data-toggle="tab" aria-expanded="false">BTC</a> </li>
									<li class=" nav-item"> <a href="#level-2" class="nav-link" data-toggle="tab" aria-expanded="false">BCH</a> </li>
									<li class=" nav-item"> <a href="#level-3" class="nav-link" data-toggle="tab" aria-expanded="false">DOGE</a> </li>   
                                </ul>
	
								
                                <div class="tab-content br-n pn">
								
								<!--Level 1--> 
                                    <div id="level-1" class="tab-pane active">
                                        <div class="row">
                                            <div class="col-md-12">  
											<h4 class="card-title">BTC Transaction</h4>
											
												<?php $crypto='BTC';$trans = getransaction($crypto,$label); ?>
											<div class="table-responsive">  
											<table id="file_export" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                      <th>From</th>
                                                    <th>To</th>
                                                    <th>Category</th>
                                                    <th>Amount BTC</th> 
                                                    <th>Balance</th>
                                                    <th>Confirmations</th>
                                                    <th>TXID</th>
                                                    <th>Date and Time</th>
                                            </tr>
                                        </thead>
                                        <tbody>
										<?php  
										$i=1; 
										if(isset($trans[0]->amount)){$balafter = $trans[0]->amount;}
                                    foreach ( $trans as $key => $arr_datas) { 
									if(isset($arr_datas->fee)){$fee = $arr_datas->fee;}else{$fee=0;}
									if($i==1){
										$balafter = $arr_datas->amount;	
										}else{
                                       if($arr_datas->category=='send'){
										$balafter = $balafter +($arr_datas->amount) + $fee;
									   }elseif($arr_datas->category=='receive'){
										 $balafter = $balafter +($arr_datas->amount) + $fee;  
									   }elseif($arr_datas->category=='move'){ 										   
										  $val = substr($arr_datas->amount, 0, 1);  
										  if($val=='-'){$balafter = $balafter +($arr_datas->amount) + $fee;}
										  else{$balafter = $balafter +($arr_datas->amount) + $fee;  }
									   }
										}
                                            ?>  
											
                                            <tr>
                                                <td><?php echo $i; ?></td> 
											<?php if($arr_datas->category=='receive'){ 
											$trans = gettransaction_crypto($crypto, $arr_datas->txid); 
											?>
                                         <td><?php echo $trans->details[0]->account; ?></td>
											<?php }else{ ?>
                                         <td><?php echo $arr_datas->account; ?></td>
											<?php } ?>
                                            <td><?php if(isset($arr_datas->label)){echo $arr_datas->label;}elseif(isset($arr_datas->otheraccount)){echo $arr_datas->otheraccount;} ?></td>
                                            <td><?php if($arr_datas->category=='move'){ echo 'fees';}else{ echo $arr_datas->category;} ?></td>
                                             <td><?php echo $arr_datas->amount; ?></td>
                                             <td><?php echo $balafter; ?></td>
                                              <td><?php if(isset($arr_datas->confirmations)){echo $arr_datas->confirmations;} ?></td>
                                               <td><?php if(isset($arr_datas->txid)){echo '<a target="_blank" href="https://live.blockcypher.com/'.strtolower($crypto).'/tx/'.$arr_datas->txid.'">'.$arr_datas->txid.'</a>';} ?></td>
                                               <td><?php if(isset($arr_datas->timereceived)){echo date('Y-m-d H:i:s',$arr_datas->timereceived);}elseif(isset($arr_datas->time)){echo date('Y-m-d H:i:s',$arr_datas->time);} ?></td>
                                            </tr>
									<?php $i++; 
									} ?> 
                                        </tbody> 
                                    </table> 
									</div>
									
											</div>
										</div>
									</div>
									
									
								<!--Level 2--> 
                                    <div id="level-2" class="tab-pane">
                                        <div class="row">
                                            <div class="col-md-12">  
											<h4 class="card-title">BCH Transaction</h4>
											
												<?php $crypto='BCH';$trans = getransaction($crypto,$label); ?>
											<div class="table-responsive">  
											<table id="file_export2" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                      <th>From</th>
                                                    <th>To</th>
                                                    <th>Category</th>
                                                    <th>Amount BCH</th> 
                                                    <th>Balance</th>
                                                    <th>Confirmations</th>
                                                    <th>TXID</th>
                                                    <th>Date and Time</th>
                                            </tr>
                                        </thead>
                                        <tbody>
										<?php  
										$i=1; 
										if(isset($trans[0]->amount)){$balafter = $trans[0]->amount;}
                                    foreach ( $trans as $key => $arr_datas) { 
									if(isset($arr_datas->fee)){$fee = $arr_datas->fee;}else{$fee=0;}
									if($i==1){
										$balafter = $arr_datas->amount;	
										}else{
                                       if($arr_datas->category=='send'){
										$balafter = $balafter +($arr_datas->amount) + $fee;
									   }elseif($arr_datas->category=='receive'){
										 $balafter = $balafter +($arr_datas->amount) + $fee;  
									   }elseif($arr_datas->category=='move'){ 										   
										  $val = substr($arr_datas->amount, 0, 1);  
										  if($val=='-'){$balafter = $balafter +($arr_datas->amount) + $fee;}
										  else{$balafter = $balafter +($arr_datas->amount) + $fee;  }
									   }
										}
                                            ?>  
											
                                            <tr>
                                                <td><?php echo $i; ?></td> 
											<?php if($arr_datas->category=='receive'){ 
											$trans = gettransaction_crypto($crypto, $arr_datas->txid); 
											?>
                                         <td><?php echo $trans->details[0]->account; ?></td>
											<?php }else{ ?>
                                         <td><?php echo $arr_datas->account; ?></td>
											<?php } ?>
                                            <td><?php if(isset($arr_datas->label)){echo $arr_datas->label;}elseif(isset($arr_datas->otheraccount)){echo $arr_datas->otheraccount;} ?></td>
                                            <td><?php if($arr_datas->category=='move'){ echo 'fees';}else{ echo $arr_datas->category;} ?></td>
                                             <td><?php echo $arr_datas->amount; ?></td>
                                             <td><?php echo $balafter; ?></td>
                                              <td><?php if(isset($arr_datas->confirmations)){echo $arr_datas->confirmations;} ?></td>
                                               <td><?php if(isset($arr_datas->txid)){echo '<a target="_blank" href="https://www.blockchain.com/'.strtolower($crypto).'/tx/'.$arr_datas->txid.'">'.$arr_datas->txid.'</a>';} ?></td>
                                               <td><?php if(isset($arr_datas->timereceived)){echo date('Y-m-d H:i:s',$arr_datas->timereceived);}elseif(isset($arr_datas->time)){echo date('Y-m-d H:i:s',$arr_datas->time);} ?></td>
                                            </tr>
									<?php $i++; 
									} ?> 
                                        </tbody> 
                                    </table> 
									</div>
									
											</div>
										</div>
									</div>
									
									
								<!--Level 3--> 
                                    <div id="level-3" class="tab-pane">
                                        <div class="row">
                                            <div class="col-md-12">  
											<h4 class="card-title">DOGE Transaction</h4>
											
												<?php $crypto='DOGE';$trans = getransaction($crypto,$label); ?>
											<div class="table-responsive">  
											<table id="file_export3" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                      <th>From</th>
                                                    <th>To</th>
                                                    <th>Category</th>
                                                    <th>Amount DOGE</th> 
                                                    <th>Balance</th>
                                                    <th>Confirmations</th>
                                                    <th>TXID</th>
                                                    <th>Date and Time</th>
                                            </tr>
                                        </thead>
                                        <tbody>
										<?php  
										$i=1; 
										if(isset($trans[0]->amount)){$balafter = $trans[0]->amount;}
                                    foreach ( $trans as $key => $arr_datas) { 
									if(isset($arr_datas->fee)){$fee = $arr_datas->fee;}else{$fee=0;}
									if($i==1){
										$balafter = $arr_datas->amount;	
										}else{
                                       if($arr_datas->category=='send'){
										$balafter = $balafter +($arr_datas->amount) + $fee;
									   }elseif($arr_datas->category=='receive'){
										 $balafter = $balafter +($arr_datas->amount) + $fee;  
									   }elseif($arr_datas->category=='move'){ 										   
										  $val = substr($arr_datas->amount, 0, 1);  
										  if($val=='-'){$balafter = $balafter +($arr_datas->amount) + $fee;}
										  else{$balafter = $balafter +($arr_datas->amount) + $fee;  }
									   }
										}
                                            ?>  
											
                                            <tr>
                                                <td><?php echo $i; ?></td> 
											<?php if($arr_datas->category=='receive'){ 
											$trans = gettransaction_crypto($crypto, $arr_datas->txid); 
											?>
                                         <td><?php echo $trans->details[0]->account; ?></td>
											<?php }else{ ?>
                                         <td><?php echo $arr_datas->account; ?></td>
											<?php } ?>
                                            <td><?php if(isset($arr_datas->label)){echo $arr_datas->label;}elseif(isset($arr_datas->otheraccount)){echo $arr_datas->otheraccount;} ?></td>
                                            <td><?php if($arr_datas->category=='move'){ echo 'fees';}else{ echo $arr_datas->category;} ?></td>
                                             <td><?php echo $arr_datas->amount; ?></td>
                                             <td><?php echo $balafter; ?></td>
                                              <td><?php if(isset($arr_datas->confirmations)){echo $arr_datas->confirmations;} ?></td>
                                               <td><?php if(isset($arr_datas->txid)){echo '<a target="_blank" href="https://live.blockcypher.com/'.strtolower($crypto).'/tx/'.$arr_datas->txid.'">'.$arr_datas->txid.'</a>';} ?></td>
                                               <td><?php if(isset($arr_datas->timereceived)){echo date('Y-m-d H:i:s',$arr_datas->timereceived);}elseif(isset($arr_datas->time)){echo date('Y-m-d H:i:s',$arr_datas->time);} ?></td>
                                            </tr>
									<?php $i++; 
									} ?> 
                                        </tbody> 
                                    </table> 
									</div>
									
											</div>
										</div>
									</div>
									
									
									
									
								</div>
	
	
								
							</div>	
								 
								
                            </div>
                        </div>
                    </div>
                </div>
                 
</div>
 

@endsection