<div id="divEmodules"></div>
<script>
	getUserList();
	$("#divEmodules").dialog({
		modal:true,
		title:"Edit User Modules Access",
		closeOnEscape:false,
		dialogClass:"no-close",
		autoOpen: false,
		width:700,
		buttons: [
			{
				text: "Cancel",
				click: function()
				{
					$(this).dialog("close");
				}
			},
			{
				text: "Save",
				click: function() 
				{
					if(validateEmodules())
					{
						MessageType.confirmmsg(submitEmodules,"Are you sure you want to save this modules access setup?");
					}
				}
			}
		]
	});
</script>