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
                                <h4 class="card-title">Admin | List Transactions {{$crypto}}</h4>
                                
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
                                                    <th>Confirmations</th>
                                                    <th>TXID</th>
                                                    <th>Date and Time</th>
                                            </tr>
                                        </thead>
                                        <tbody>
										<?php  
                                        $i=1; $no=0;
                                        if($trans!=null){ 
                                            
                                            if(isset($trans[0])){ 
                                    foreach ( $trans as $key => $arr_datas) {
 
                                            ?>  
											
                                            <tr>
                                                <td><?php echo $i; ?></td> 
                                         <td><?php echo $arr_datas['account']; ?></td> 
                                            <td><?php echo $arr_datas['label']; ?></td>
                                            <td><?php echo $arr_datas['category']; ?></td>
                                             <td><?php echo $arr_datas['fees']; ?></td>
                                             <td><?php echo $arr_datas['amount']; ?></td> 
                                              <td><?php echo $arr_datas['confirmations']; ?></td>
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
                                    
                                }else{ 
                                    
									if(isset($trans['fee'])){$fee = $trans['fee'];}else{$fee=0;}
									 
                                            ?>  
											
                                            <tr>
                                                <td><?php echo $i; ?></td> 
                                          <?php if($trans['category']=='receive'){ 
					 $transer = gettransaction_crypto(strtoupper($crypto), $trans['txid']); 
					 ?>
                                         <td><?php echo $transer['details'][0]['account']; ?></td>
                     <?php }else{ ?>
                                         <td><?php echo $trans['account']; ?></td>
					 <?php } ?>

					
                                            <td><?php if(isset($trans['label'])){echo $trans['label'];}elseif(isset($trans['otheraccount'])){echo $trans['otheraccount'];}else{echo $trans['address'];} ?></td>
                                            <td><?php echo $trans['category']; ?></td>
                                             <td><?php echo $fee; ?></td>
                                             <td><?php echo $trans['amount']; ?></td> 
                                              <td><?php if(isset($trans['confirmations'])){echo $trans['confirmations'];} ?></td>
                                               <td><?php if(isset($trans['txid'])){
												   if($crypto!='BCH'){ ?>
								  <a target="_blank" href="https://live.blockcypher.com/{{strtolower($crypto)}}/tx/{{ $trans['txid'] }}">{{ $trans['txid'] }}</a>
												<?php }else{ ?>
								  <a target="_blank" href="https://www.blockchain.com/bch/tx/{{ $trans['txid'] }}">{{ $trans['txid'] }}</a>
												<?php }  
												   } ?></td>
                                               <td><?php if(isset($trans['timereceived'])){echo date('Y-m-d H:i:s',$trans['timereceived']);}elseif(isset($trans['time'])){echo date('Y-m-d H:i:s',$trans['time']);} ?></td>
                                            </tr>
                                <?php
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

 