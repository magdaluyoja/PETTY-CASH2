<div id="divremarks"></div>
<script>
	getREIMlist();
   	$("#divremarks").dialog({
   		dialogClass: "no-close",
		closeOnEscape: false,
		bgiframe:true, 
		resizable:false, 
		title: "Purpose Details",
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