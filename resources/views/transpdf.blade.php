<!DOCTYPE html> 
<?php 
$trans = \App\TransLND::where('uid',$userid)->orderBy('id','desc')->get(); 
$wallet = \App\WalletAddress::where('id',$walletid)->first(); 
?>
<html>
  <head>
    <meta charset="utf-8">
    <title></title>
  </head>
  <body>
    <table class="table table-bordered" width="100%">
      <tr>
        <td colspan="5" align="center">  
			<img src="{{asset('asset/assets/images/dorado.png')}}" style="width:200px;"> 
        </td> 
      </tr> 
      <tr>
        <td colspan="5" align="center">   
          <font size="15px"><b>DORADO WALLET</b> </font> <br>
		  <b>Pinkexc (M). Sdn. Bhd. (1194957-P)</b><br>
		   1, Jln Meru Bestari A14, Medan Meru Bestari,<br> 30020 Ipoh, Perak Darul Ridzuan, Malaysia <br><br> Phone: +605 525 1866 Email: admin@pinkexc.com
        </td> 
      </tr> 
      <tr>
        <td colspan="11">     
          Service Type : Transaction {{$wallet->title}} ( {{$crypto}} )
        </td> 
      </tr>
       <tr>
        <td align="center">Category </td> 
        <td align="center">Status </td> 
        <td align="center">Error Code </td> 
        <td align="center">Invoice </td> 
        <td align="center">Txid </td> 
        <td align="center">Amount </td> 
        <td align="center">Balance </td> 
        <td align="center">Rate </td>
        <td align="center">Fees </td>
        <td align="center">Remarks </td>
        <td align="center">Created At </td> 
      </tr> 
      <?php foreach($trans as $data){ 
          $currency = \App\Currency::where('id',$data->currency)->first(); 
          $fees = number_format($data->netfee + $data->walletfee,8,'.','');
          ?> 
       <tr>
        <td align="center">{{ucwords($data->category)}} </td> 
        <td align="center">{{ucwords($data->status)}} </td> 
        <td>{{$data->error_code}} </td> 
        <td>{{$data->recipient}} </td> 
        <td>{{$data->txid}} </td> 
        <td align="center">{{$data->amount}} </td> 
        <td align="center">{{$data->after_bal}} </td> 
        <td align="center">{{$data->rate}} {{$currency->code}}</td>
        <td align="center">{{$fees}} </td>
        <td>{{$data->remarks}} </td>
        <td align="center">{{$data->created_at}} </td> 
      </tr> 
        <?php } ?>
    </table>
	  
  </body>
</html>
