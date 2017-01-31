$("document").ready(function(){
	$("#seldep").change(function(){
		var seldep	=	$('#seldep').val();
		$("#selapp > option").remove();
		$.ajax({
				url			:	'approverHR.php?action=GETAPP&seldep='+seldep,
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
	});
	$("#btnset").click(function(){
		var app		=	$('#selapp').val();
		var	dep		=	$('#seldep').val();
		var appname	=	$('#selapp option:selected').text();
		var depname	=	$('#seldep option:selected').text();
		if(dep == '')
		{
			MessageType.infoMsg('Please select a department.');
		}
		else if(app == '')
		{
			MessageType.infoMsg('Please select an approver.');
		}
		else
		{
			MessageType.confirmmsg(setapprover,"Do you want to set "+appname+" as an approver for the "+depname+" department?");
		}
	});
	$("#btnunset").click(function(){
		var app		=	$('#selapp').val();
		var	dep		=	$('#seldep').val();
		var appname	=	$('#selapp option:selected').text();
		var depname	=	$('#seldep option:selected').text();
		if(dep == '')
		{
			MessageType.infoMsg('Please select a department.');
		}
		else if(app == '')
		{
			MessageType.infoMsg('Please select an approver.');
		}
		else
		{
			MessageType.confirmmsg(unsetapprover,"Do you want to unset "+appname+" as an approver for the "+depname+" department?");
		}
	});
});
function loadApps()
{
	$.ajax({
			url			:	'approverHR.php?action=LOADAPPS',
			beforeSend	:	function()
						{
							$('#divloader').dialog("open");
						},
			success		:	function(response)
						{
							$('#divloadapps').html(response);
							$('#divloader').dialog("close");
							$("#tblapplists").tablesorter({sortList: [[0,0]]});
						}
		});
}
function setapprover()
{
	var app		=	$('#selapp').val();
	var	dep		=	$('#seldep').val();
	var appname	=	$('#selapp option:selected').text();
	var depname	=	$('#seldep option:selected').text();
	$.ajax({
			url			:	'approverHR.php?action=SETAPP&APP='+app+"&APPNAME="+appname+"&DEP="+dep+"&DEPNAME="+depname,
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
	var	dep		=	$('#seldep').val();
	var appname	=	$('#selapp option:selected').text();
	var depname	=	$('#seldep option:selected').text();
	$.ajax({
			url			:	'approverHR.php?action=UNSETAPP&APP='+app+"&APPNAME="+appname+"&DEP="+dep+"&DEPNAME="+depname,
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