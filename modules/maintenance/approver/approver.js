$("document").ready(function(){
	$("#btnset").click(function(){
		var app		=	$('#selapp').val();
		var appname	=	$('#selapp option:selected').text();
		if(app == '')
		{
			MessageType.infoMsg('Please select an approver.');
		}
		else
		{
			MessageType.confirmmsg(setapprover,"Do you want to set "+appname+" as an approver?");
		}
	});
	$("#btnunset").click(function(){
		var app		=	$('#selapp').val();
		var appname	=	$('#selapp option:selected').text();
		if(app == '')
		{
			MessageType.infoMsg('Please select an approver.');
		}
		else
		{
			MessageType.confirmmsg(unsetapprover,"Do you want to unset "+appname+" as an approver?");
		}
	});
});
function setapprover()
{
	var app		=	$('#selapp').val();
	var appname	=	$('#selapp option:selected').text();
	$.ajax({
			url			:	'approver.php?action=SETAPP&APP='+app+"&APPNAME="+appname,
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
function unsetapprover()
{
	var app		=	$('#selapp').val();
	var appname	=	$('#selapp option:selected').text();
	$.ajax({
			url			:	'approver.php?action=UNSETAPP&APP='+app+"&APPNAME="+appname,
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
