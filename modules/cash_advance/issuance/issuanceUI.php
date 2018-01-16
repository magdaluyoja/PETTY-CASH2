<div id="divdenomination" title="Denomination Details">
<form id="frmdenomination">
	<table border="0" class='dialogtable curved5px shadowed' width="100%">
		<tr>
			<td class='dialogdtltitle centered' colspan='20'>C.A. No: <a  id="tdCANO" class='td-ca-list-title'></a>  &nbsp;  Amount: <a id="tdCAamount"></a></td>
		</tr>
		<tr class='dialogdtlheader centered'>
			<td class="padding">Denomination</td>
			<td class="padding">Pieces</td>
			<td class="padding">Amount</td>
		</tr>
		<tr class='dialogdtldetails'>
			<td class="centered">1000</td>
			<td><input type="text" class="input_text curved5px width10 centered denomination" id="txtpcs1" name="txtpcs1" data-multiplier="1000"></td>
			<td class="amount" id="am1">0.00</td>
		</tr>
		<tr class='dialogdtldetails'>
			<td class="centered">500</td>
			<td><input type="text" class="input_text curved5px width10 centered denomination" id="txtpcs2" name="txtpcs2" data-multiplier="500"></td>
			<td class="amount" id="am2">0.00</td>
		</tr>
		<tr class='dialogdtldetails'>
			<td class="centered">200</td>
			<td><input type="text" class="input_text curved5px width10 centered denomination" id="txtpcs3" name="txtpcs3" data-multiplier="200"></td>
			<td class="amount" id="am3">0.00</td>
		</tr>
		<tr class='dialogdtldetails'>
			<td class="centered">100</td>
			<td><input type="text" class="input_text curved5px width10 centered denomination" id="txtpcs4" name="txtpcs4" data-multiplier="100"></td>
			<td class="amount" id="am4">0.00</td>
		</tr>
		<tr class='dialogdtldetails'>
			<td class="centered">50</td>
			<td><input type="text" class="input_text curved5px width10 centered denomination" id="txtpcs5" name="txtpcs5" data-multiplier="50"></td>
			<td class="amount" id="am5">0.00</td>
		</tr>
		<tr class='dialogdtldetails'>
			<td class="centered">20</td>
			<td><input type="text" class="input_text curved5px width10 centered denomination" id="txtpcs6" name="txtpcs6" data-multiplier="20"></td>
			<td class="amount" id="am6">0.00</td>
		</tr>
		<tr class='dialogdtldetails'>
			<td class="centered">Coins</td>
			<td><input type="text" class="input_text curved5px width10 centered denomination" id="txtpcs7" name="txtpcs7" data-multiplier="1">
			</td>
			<td class="amount" id="am7">0.00</td>
		</tr>
		
		<tr class='dialogdtldetails'>
			<td class="curvedleft">&nbsp;</td>
			<td class="centered"><b>Total</td>
			<td class="curvedright">
				<input type="text" id="txttotal" name="txttotal" class="input_text curved5px amount" readonly>
				<input type="hidden" id="hdncaamount" name="hdncaamount">
			</td>
		</tr>
	</table>
</form>
</div>
<script>
	getCAlist();
	
	$("#divdenomination").dialog({
	bgiframe:true, 
	resizable:false, 
	height: "auto",
	width: "auto", 
	modal:true, 
	autoOpen: false,	
	draggable: true,
	overlay: { backgroundColor: '#000', opacity: 0.5 },
	buttons: {
				'Clear': function()
				{	
					clearForm();
				},'Save & Print': function()
				{	
					var totalamount = + ($('#txttotal').val()).replace(/,/g, ''); 
					var caamount	=	$('#hdncaamount').val();
					if(totalamount == caamount)
					{
						MessageType.confirmmsg(saveDenomination,"Do you want to save this denomination details?");
					}
					else
					{
						MessageType.infoMsg("Total amount is not equal to the requested Cash Advance amount.");
					}
				}
			 },
	close:	clearForm
   	});
</script>