<div id="divreimbursement" align="center">
	<form id = 'frmreimbursement'>
		<table border="0" width="100%"class="div-new-trx">
			<tr>
				<td>
					<table width="100%" border="0" id="tblpurpose">
						<tr>
							<td  class="td-content-title" colspan="4">
								Particulars
							</td>
						</tr>
						</tr>
							<td colspan="4"></td>
						</tr>
						<tr id="trpurpose_1">
							<td class="label_text">Purpose</td>
							<td class="label_text">
								:<?php echo $DATASOURCE->getDropDown($conn_172,PCDBASE," PARTICULARS","input_text curved5px selpurposes","selpurpose_1","*","PARTICULARCODE","PARTICULARDESC","1","data-cnt='1'"); ?>
								<input type='hidden' id='hidval_1' name='hidval_1'>
								<input type='hidden' id='hidname_1' name='hidname_1'>
							</td>
							<td>
								<img src="/PETTY_CASH/images/viewrem.png" id="btnviewrem_1" title="View Remarks" class="smallimgbuttons viewrem tooltips" data-curcnt='1'><img src="/PETTY_CASH/images/addtxt.png" id="btnaddpurpose" title="Add Purpose"	class="smallimgbuttons tooltips">
								<input type="hidden" id="hdnpurposecnt" name="hdnpurposecnt" value="1">
							</td>
						</tr>
					</table>
					<br>
				</td>
			</tr>
		</table>
	</form>	
	<table class="div-new-trx" width="80%" border="0">
		<tr>
			<td  class="td-content-title" colspan="4">Summary</td>
		</tr>
		<tr class="label_text tr-ca-list-dtls">
			<td class="" width="50%">Reimbursement Amount</td>
			<td class="amount" width="40%"id='REIM'>0.00</td>
		</tr>
		<tr class="label_text tr-ca-list-dtls">
			<td class="" width="50%">Total Amount</td>
			<td class=" amount" width="30%"id='totalAmount'>0.00</td>
		</tr>	
		<tr class="label_text tr-ca-list-dtls">
			<td class="" width="50%">Remaining Amount</td>
			<td class=" amount" width="30%"id='REMamt'>0.00</td>
		</tr>		
	</table>
</div>
<div id="divremarks"></div>
<script>
	getCAlist();
	$("#divreimbursement").dialog({
		dialogClass: "no-close",
		closeOnEscape: false,
		bgiframe:true, 
		resizable:false, 
		title: "Reimbursement Details",
		height: "auto",
		width: "auto", 
		modal:true, 
		autoOpen: false,	
		draggable: true,
		overlay: { backgroundColor: '#000', opacity: 0.5 },
		buttons:
		{
			'Close':function()
			{
				$(this).dialog("close");
				closeparticulars()
			},
			'Save':function()
			{
				savereimbursement();
			}
		}
   	});
   	$("#divremarks").dialog({
   		dialogClass: "no-close",
		closeOnEscape: false,
		bgiframe:true, 
		resizable:false, 
		title: "Remarks Amount",
		height: "auto",
		width:"auto", 
		modal:true, 
		autoOpen: false,	
		draggable: true,
		overlay: { backgroundColor: '#000', opacity: 0.5 },
		buttons:{
					'Close':function()
					{
						$(this).dialog("close");
					},
					'Save':function()
					{
						var counter = $(this).data('curcnt');	
						saveTotal(counter);
					}
				}
   	});
</script>