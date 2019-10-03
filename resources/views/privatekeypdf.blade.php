<!DOCTYPE html> 
<?php
$type = 'JOMPAY';
$priceapi = \App\PriceApi::where('crypto',$jompay->crypto)->first();
$myr_adminS = $jompay->rate*$jompay->fee;
$myr_userS = $jompay->rate*$jompay->crypto_amount;
$myr_total = $myr_adminS+$myr_userS
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
			<img src="{{asset('assets/assets/images/dashboard/pinkexc_logo_new_pink.png')}}" style="width:200px;"> 
        </td> 
      </tr> 
      <tr>
        <td colspan="5" align="center">   
          <font size="15px"><b>BLOCKCHAIN AND CRYPTOCURRENCY SOLUTION</b> </font> <br>
		  <b>Pinkexc (M). Sdn. Bhd. (1194957-P)</b><br>
		   1, Jln Meru Bestari A14, Medan Meru Bestari,<br> 30020 Ipoh, Perak Darul Ridzuan, Malaysia <br><br> Phone: +605 525 1866 Email: admin@pinkexc.com
        </td> 
      </tr>
      <tr>
        <td colspan="5" >   
          <hr size="3">
        </td> 
      </tr>
      <tr>
        <td colspan="5" align="center">     
          Service Type : Colony DAX
        </td> 
      </tr>
      <tr>
        <td colspan="5" align="center">   
          TxnID : {{$jompay->txid}}
        </td> 
      </tr>
      <tr>
        <td>   
          Receipt No : 
        </td>
        <td align="center">   
         <?php if(isset($receipt)){ $txt_no = sprintf('%08d',$receipt->id); echo $txt_no;}?>
        </td>
        <td colspan="3" align="center">   
          Digital Currency {{$type}} Receipt
        </td> 
      </tr>
      <tr>
        <td>   
          Date : 
        </td>
        <td align="center">   
		{{$jompay->updated_at}}
        </td>
        <td colspan="2" align="center">   
          Txn Type:
        </td> 
        <td align="center">   
          {{$type}}
        </td>
      </tr>
      <tr>
        <td colspan="5" >   
          <hr size="3">
        </td> 
      </tr>
      <tr>
        <td width="20%">   
          Digital Currency : 
        </td>
        <td align="center" width="30%">   
          {{strtoupper($priceapi->name)}}
        </td>
        <td width="20%">   
          Rate:
        </td> 
        <td align="center" width="15%">   
          RM
        </td>
        <td align="center" width="15%">   
          {{number_format(round($jompay->rate,2),2)}}
        </td>
      </tr>
       <tr>
        <td>   
          Amount : 
        </td>
        <td align="center">   
          {{$jompay->crypto_amount-$jompay->fee}}
        </td>
        <td>   
          RM:
        </td> 
        <td align="center">   
          RM
        </td>
        <td align="center">   
		{{number_format(round($myr_userS,2),2)}}
        </td>
      </tr>
      <tr>
        <td colspan="5" >   
          <hr size="3">
        </td> 
      </tr> 
       <tr>
        <td colspan="2">   
        </td> 
        <td>   
          Subtotal:
        </td> 
        <td align="center">   
          RM
        </td>
        <td align="center">   
          {{number_format(round($myr_userS,2),2)}}
        </td>
      </tr>
       <tr>
        <td colspan="2">    
        </td> 
        <td>   
          Fees:
        </td> 
        <td align="center">   
          RM
        </td>
        <td align="center">   
		{{number_format($myr_adminS,2)}}
        </td>
      </tr>
       <tr>
        <td colspan="2">   
        </td> 
        <td>   
          Other:
        </td> 
        <td align="center">   
          RM
        </td>
        <td align="center">   
          -
        </td>
      </tr>
       <tr>
        <td colspan="2">   
        </td> 
        <td>   
          Total:
        </td> 
        <td align="center">   
          RM
        </td>
        <td align="center">   
          {{number_format(round($myr_total,2),2)}}
        </td>
      </tr>
      <tr>
        <td colspan="5" >   
          <hr size="3">
        </td> 
      </tr> 
    </table>
	 
    <table class="table table-bordered" width="100%">
      <tr>
        <td align="right">   
          <font size="15px">Notes:</font>
        </td> 
        <td colspan="2"> </td> 
      </tr> 
      <tr>
        <td width="15%" align="right"> 1.</td> 
        <td width="80%">    
		   <font size="15px">All confirmed buy, sell, deposits and withdrawal on Colony are final.</font> 
        </td> 
        <td width="5%"> </td> 
      </tr>
      <tr>
        <td align="right"> 2.</td> 
        <td>    
		   <font size="15px">Please notifiy is if any discrepancy within seven (7) days otherwise this receipt will be</font>
        </td> 
        <td> </td> 
      </tr>
      <tr>
        <td> </td> 
        <td>    
		   <font size="15px">considered as correct.</font> 
        </td> 
        <td> </td> 
      </tr>
      <tr>
        <td colspan="3" align="center">    
		  <font size="15px"><b>***** This is computer generated receipt, no signature required *****</b></font>
        </td>   
      </tr>
    </table>
  </body>
</html>
