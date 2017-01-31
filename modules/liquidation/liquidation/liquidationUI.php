<div id="divliquidationdtls"></div>
<div id="divtax">
	<form id="frmtaxcomp" name = 'frmtaxcomp'>
		<table class="dialogtable curved5px shadowed">
			<tr>
				<td class='dialogdtltitle' colspan="3">
					PURPOSE: <a id="lblpurpose" class="dialog-dtl-tbl-title"></a>
					REMARKS: <a id="lblremark" class="dialog-dtl-tbl-title"></a>
				</td>
			</tr>
			<tr>
				<td class='dialogdtlheader' colspan="3">COMPANY INFORMATION</td>
			</tr>
			<tr>
				<td class='dialogdtldetails'>Company Name</td>
				<td class='dialogdtldetails'><INPUT type="text" id="txtcomname" name="txtcomname" size="37"class="input_text curved5px"></td>
			</tr>
			<tr>
				<td class='dialogdtldetails'>Company Address</td>
				<td class='dialogdtldetails'><INPUT type="text" id="txtcomadd" name="txtcomadd" size="37"class="input_text curved5px"></td>
			</tr>
			<tr>
				<td class='dialogdtldetails'>Company TIN No.</td>
				<td class='dialogdtldetails'><INPUT type="text" id="txtcomtin" name="txtcomtin" size="37"class="input_text curved5px"></td>
			</tr>
		</table>
	</form>	
</div>
<script>
	getCAlist();
	$("#divliquidationdtls").dialog({
		bgiframe:true, 
		resizable:false, 
		title:'Liquidation Details',
		height: "auto",
		width: "auto", 
		modal:true, 
		autoOpen: false,	
		draggable: true,
		overlay: { backgroundColor: '#000', opacity: 0.5 },
		buttons: {
					'Close':function()
					{
						$(this).dialog("close");
					},
					'Save':function()
					{
						if(validatefields())
						{
							var newtotAmount	= +	$("#tdTOTamount").text();
							if(newtotAmount != "0")
							{
								MessageType.confirmmsg(liquidateCA,"Do you want to save and liquidate this Cash Advance?");
							}
							else
							{
								MessageType.infoMsg("New total amount is zero(0).");
							}
						}
						else
						{
							MessageType.infoMsg("Please supply the missing value/s.");
						}
					}
				 }
       	});
       	$("#divtax").dialog({
			dialogClass:'no-close',
			closeOnEscape: false,
			bgiframe:true, 
			resizable:false, 
			title:'VAT Information',
			height: "auto",
			width:"auto", 
			modal:true, 
			autoOpen: false,	
			draggable: true,
			overlay: { backgroundColor: '#000', opacity: 0.5 },
			buttons: {
						'Close':function()
						{
							var counter = $(this).data('curcnt');	
							$('#rdovatG_'+counter).prop('disabled',true);
							$('#rdovatS_'+counter).prop('disabled',true);
							$('#rdovatG_'+counter).prop('checked',false);
							$('#rdovatS_'+counter).prop('checked',false);
							$('#vat_'+counter).prop('checked', false)
							$(this).dialog('close');
						},
						'Save':function()
						{				
							var counter = $(this).data('curcnt');	
							savecomdtl(counter);
						}
					 }
	       	});
</script>