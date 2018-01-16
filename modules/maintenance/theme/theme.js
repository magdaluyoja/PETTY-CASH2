$("document").ready(function(){
	$("#btnsubmit").click(function(){
    	var theme			=	$("#seltheme").val();
    	var selmainback		=	$("#selmainback").val();
    	var selcontentback	=	$("#selcontentback").val();
    	 $.ajax({
                	type	:	"GET",
                	url		:	"theme.php?action=SETTHEME&THEMENAME="+theme+"&MAINBACK="+selmainback+"&CONTENTBACK="+selcontentback,
                	beforeSend	:function()
                	{
                		$("#divloader").dialog("open");
                	},
                	success	:	function(response)
                	{
                		$("#divtheme").html(response);
						$("#divloader").dialog("close");
                	}
                });
	});
});
