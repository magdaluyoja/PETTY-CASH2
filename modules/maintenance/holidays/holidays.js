$("document").ready(function(){
	$("#btncancel").click(function(){
		location.reload();
	});
	$("#btnsave").click(function(){
		var txtname	=	$('#txtname').val();
		var txtdate =	$('#txtdate').val();
		var errmsg	=	"";
		if(txtname	==	 "")
		{
			errmsg = ' - Empty holiday name. <br>';
		}
		if(txtdate == "")
		{
			errmsg += ' - Empty holiday date. <br>';
		}
		if(errmsg == "")
		{
			MessageType.confirmmsg(saveHoli,"Do you want to save this holiday data?");
		}
		else
		{
			MessageType.infoMsg(errmsg);
		}
	});
	$("#btnupdate").click(function(){
		var txtname	=	$('#txtname').val();
		var txtdate =	$('#txtdate').val();
		var errmsg	=	"";
		if(txtname	==	 "")
		{
			errmsg = '[ Empty holiday name. ]';
		}
		if(txtdate == "")
		{
			errmsg += '[ Empty holiday date. ]';
		}
		if(errmsg == "")
		{
			MessageType.confirmmsg(updateHoli,"Do you want to update this holiday data?");
		}
		else
		{
			MessageType.infoMsg(errmsg);
		}
	});
	$("tr td").on("click",".btndeactivate",function(){
		var holiid	=	$(this).attr("data-id");
		MessageType.confirmmsg(deactivateHoli,"Do you want to deactivate this holiday?",holiid);
	});
	$("tr td").on("click",".btnactivate",function(){
		var holiid	=	$(this).attr("data-id");
		MessageType.confirmmsg(activateHoli,"Do you want to activate this holiday?",holiid);
	});
	$("tr td").on("click",".btnedit",function(){
		var holiid	=	$(this).attr("data-id");
		$.ajax({
			url			:	'holidays.php?action=GETHOLIDTLS&HOLIID='+holiid,
			beforeSend	:	function()
						{
							$('#divloader').dialog("open");
						},
			success		:	function(response)
						{
							$('#divdebug').html(response);
							$("#btnsave").hide();
							$("#btnupdate").show();
							$('#divloader').dialog("close");
						}
	  		});
	});
	$("#txtdate").click(function(){
		$(".ui-datepicker-year").html("");
	});
	$("#ui-datepicker-div").hover(function(){
		$(".ui-datepicker-year").html("");
	});
});
function getHliList()
{
	$.ajax({
			url			:	'holidays.php?action=GETHOLILIST',
			beforeSend	:	function()
						{
							$('#divloader').dialog("open");
						},
			success		:	function(response)
						{
							$('#divholidays').html(response);
							$('#divloader').dialog("close");
							$("#tblholilists").tablesorter({sortList: [[0,0]]});
							$(".tooltips").tooltip();
						}
	  		});
}
function saveHoli()
{
	var frmholi	=	$('#frmholi').serialize();
	$.ajax({
			type		:	'POST',
			data		:	frmholi,
			url			:	'holidays.php?action=SAVE',
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
function updateHoli()
{
	var frmholi	=	$('#frmholi').serialize();
	$.ajax({
			type		:	'POST',
			data		:	frmholi,
			url			:	'holidays.php?action=UPDATE',
			beforeSend	:	function()
						{
							$('#divloader').dialog("open");
						},
			success		:	function(response)
						{
							$('#divdebug').html(response);
							$('#divloader').dialog("close");
							$("#btnsave").show();
							$("#btnupdate").hide();
						}
		 });
}
function activateHoli(holiid)
{
		$.ajax({
			url			:	'holidays.php?action=ACTHOLIDAY&HOLIID='+holiid,
			beforeSend	:	function()
						{
							$('#divloader').dialog("open");
						},
			success		:	function(response)
						{
							$('#divdebug').html(response);
							$("#btnsave").show();
							$("#btnupdate").hide();
							$('#divloader').dialog("close");
						}
	  		});
}
function deactivateHoli(holiid)
{
		$.ajax({
			url			:	'holidays.php?action=DEACTHOLIDAY&HOLIID='+holiid,
			beforeSend	:	function()
						{
							$('#divloader').dialog("open");
						},
			success		:	function(response)
						{
							$('#divdebug').html(response);
							$("#btnsave").show();
							$("#btnupdate").hide();
							$('#divloader').dialog("close");
						}
	  		});
}