<div id="divliquidationdtls"></div>
<script>
	getCAlist();
	$("#divliquidationdtls").dialog({
		bgiframe:true, 
		resizable:false, 
		title:'Liquidation Details',
		height: "auto",
		width: 800, 
		modal:true, 
		autoOpen: false,	
		draggable: true,
		overlay: { backgroundColor: '#000', opacity: 0.5 },
		buttons: {
				OK : function()
				{	
						$(this).dialog("close");
				}
			 }
       	});
</script>