<div id="divliquidate"></div>
<script>
	getCAlist();
	$("#divliquidate").dialog({
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
					'Clear': function()
					{	
						clear();
					},
					'Save': function()
					{	
						saveliquidation();		
					}
				 }
       	});
</script>