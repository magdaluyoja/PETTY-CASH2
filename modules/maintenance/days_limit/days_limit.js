$("document").ready(function(){
	$("#btnsearch").click(function(){
		var CAnum	=	$("#txtCAnum").val();
		var date	=	$("#txtdate").val();
		searchuser();
	});
	$("#txtsearch").keyup(function(e){ 
	    var code = e.which;
	    if(code==13)e.preventDefault();
	    if(code==13){
	      searchuser();
	    }
	});
	$("#btncancel").click(function(){
		location.reload();
	});
	$("#btnupdate").click(function(){
		var user		=	$('#txtname').val();
		if(user != "")
		{
			MessageType.confirmmsg(updateLimit,"Do you want to update the days limit data of "+$("#txtname").val()+" ?");
		}
	});
});
function searchuser()
{
	var user		=	$('#txtsearch').val();
	if(user == "")
	{
		MessageType.infoMsg('Nothing to search..');
	}
	else
	{
		$.ajax({
			url			:	'days_limit.php?action=SEARCHUSER&USER='+user,
			beforeSend	:	function()
						{
							$('#divloader').dialog("open");
						},
			success		:	function(response)
						{
							$('#divdebug').html(response);
							$('#divloader').dialog("close");
						}
			});
	}
}
function updateLimit()
{
	var days		=	$('#txtdays').val();
	var frmuser		=	$("#frmuser").serialize();
	if(days != "")
	{
		$.ajax({
			type		:	'POST',
			data		:	frmuser,
			url			:	'days_limit.php?action=UPDATE',
			beforeSend	:	function()
						{
							$('#divloader').dialog("open");
						},
			success		:	function(response)
						{
							$('#divdebug').html(response);
							$('#divloader').dialog("close");
						}
			});
	}
	else
	{
		MessageType.infoMsg('Please input number of days limit.');
	}
}