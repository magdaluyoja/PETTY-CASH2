<div id="divremarks"></div>
<script>
getCAlist();
$(".buttons").button();
$(".radioset").buttonset();
$("#divremarks").dialog({
	bgiframe:true, 
	dialogClass: "no-close",
	resizable:false, 
	title: "Purpose Details",
	height: "auto",
	width: "auto", 
	modal:true, 
	autoOpen: false,	
	draggable: true,
	buttons: {
				OK : function()
				{	
						$(this).dialog("close");
				}
			 }
   	});
</script>