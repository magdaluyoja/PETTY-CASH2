$("document").ready(function(){
	$("#btnsearch").click(function(){
		var CAnum	=	$("#txtCAnum").val();
		var date	=	$("#txtdate").val();
		getUserList($("#txtsearch").val());
	});
	$("#txtsearch").keyup(function(e){ 
	    var code = e.which;
	    if(code==13)e.preventDefault();
	    if(code==13){
	      getUserList(this.value);
	    }
	});
	$("#btnnew").click(function(){
		$("#divtrxuser").toggle("fade");
	});
	$("#txtcpassword").change(function(){
		if(this.value != $("#txtpassword").val())
		{
			MessageType.infoMsg('Password did not match.');
			$(this).val("");
		}
	});
	$("#txtusername").change(function(){
		$.ajax({
			url			:	'user.php?action=CHECKUSERNAME&USERNAME='+this.value,
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
	$("#btnsave").click(function(){
		if(validate())
		{
			MessageType.confirmmsg(saveUser,"Do you want to save this user information?","");
		}
		else
		{
			MessageType.infoMsg('Fill in the empty fields.');
		}
	});
	$("#btncancel").click(function(){
		cancel();
	});
	$("#btnupdate").click(function(){
		if(validate("updatemode"))
		{
			MessageType.confirmmsg(saveUser,"Do you want to update this user information?","updatemode");
		}
		else
		{
			MessageType.infoMsg('Fill in the blank fields.');
		}
	});
	$("tr td").on("click",".btnmenu",function(){
		var username	=	$(this).attr("data-username");
		$.ajax({
			url			:	'user.php?action=GETMODULES&USERNAME='+username,
			beforeSend	:	function()
						{
							$('#divloader').dialog("open");
						},
			success		:	function(response)
						{
							$("#divEmodules").html(response);
							$("#divEmodules").dialog("open");
							$('#divloader').dialog("close");
							$("#menutab").tabs();
						}
			});
	});
	$("tr td").on("click",".btnedit",function(){
		var userid	=	$(this).attr("data-id");
		$.ajax({
			url			:	'user.php?action=EDITUSER&USERID='+userid,
			beforeSend	:	function()
						{
							$('#divloader').dialog("open");
						},
			success		:	function(response)
						{
							$('#divdebug').html(response);
							$('#divloader').dialog("close");
							var element = document.getElementById("btnnew");
								element.scrollIntoView();
								element.scrollIntoView(false);
								element.scrollIntoView({block: "end"});
								element.scrollIntoView({block: "end", behavior: "smooth"});
						}
			});
	});
});
function getUserList(userval,mainquery,pageno)
{
	$.ajax({
			url			:	'user.php?action=SEARCHUSER&USER='+userval+"&MAINQUERY="+mainquery+"&PAGENO="+pageno,
			beforeSend	:	function()
						{
							$('#divloader').dialog("open");
						},
			success		:	function(response)
						{
							$('#divusers').html(response);
							$('#divloader').dialog("close");
							$("#tbluserlist").tablesorter({sortList: [[1,0]]}); 
							$('#tbluserlist').paging({limit:15});
							$(".tooltips").tooltip();
							$(".buttons").button();
						}
			});
}
function validate(updatemode)
{
	var valid	=	true;
	if($("#txtname").val() == "")		{	$("#txtname").addClass("errpurpose"); valid = false;		}else{	$("#txtname").removeClass("errpurpose");}
	if($("#txtusername").val() == "")	{	$("#txtusername").addClass("errpurpose"); valid = false;	}else{	$("#txtusername").removeClass("errpurpose");}
	if($("#txteadd").val() == "")		{	$("#txteadd").addClass("errpurpose"); valid = false;		}else{	$("#txteadd").removeClass("errpurpose");}
	if($("#seldep").val() == "")		{	$("#seldep").addClass("errpurpose"); valid = false;			}else{	$("#seldep").removeClass("errpurpose");}
	if($("#txtposition").val() == "")	{	$("#txtposition").addClass("errpurpose"); valid = false;	}else{	$("#txtposition").removeClass("errpurpose");}
	if($("#sellevel").val() == "")		{	$("#sellevel").addClass("errpurpose"); valid = false;		}else{	$("#sellevel").removeClass("errpurpose");}
	if($("#selstatus").val() == "")		{	$("#selstatus").addClass("errpurpose"); valid = false;		}else{	$("#selstatus").removeClass("errpurpose");}
	
	if(updatemode != "updatemode"){
		if($("#txtpassword").val() == "")	{	$("#txtpassword").addClass("errpurpose"); valid = false;	}else{	$("#txtpassword").removeClass("errpurpose");}
		if($("#txtcpassword").val() == "")	{	$("#txtcpassword").addClass("errpurpose"); valid = false;	}else{	$("#txtcpassword").removeClass("errpurpose");}
	}
	if($('input:radio[name=optcustodian]').is(':checked') == false){	
		$("#tdcustodian").addClass("errpurpose"); valid = false;
	}else{	
		$("#tdcustodian").removeClass("errpurpose");
	}
	return valid;
}
function saveUser(updatemode)
{
	$('#txtusername').removeAttr('disabled');
	$('.radioset').buttonset('refresh');
	var frmuser = $("#frmuser").serialize();
	$.ajax({
			type		:	'POST',
			data		:	frmuser,
			url			:	'user.php?action=SAVEUSER&UPDATEMODE='+updatemode,
			beforeSend	:	function()
						{
							$('#divloader').dialog("open");
						},
			success		:	function(response)
						{
							$('#divdebug').html(response);
							$('#divloader').dialog("close");
							cancel();
						}
		 });
}
function cancel()
{
	$('#frmuser *').filter(':input').each(function(){
		if($(this).attr('type') != "radio" && $(this).attr('type') != "button")
		{
	    	$(this).val("");
	    	$(this).removeClass("errpurpose");
		}
		else
		{
			$("#tdcustodian").removeClass("errpurpose");
		}
	});
	$('.radioset input').removeAttr('checked');
	$('.radioset').buttonset('refresh');
	$('#txtusername').removeAttr('disabled');
	$("#divtrxuser").hide();
	$('#btnupdate').hide();
	$('#btnsave').show();
}
function toggleG(classG)
{
	var arrClasses	=	classG.split(" ");
	var arrLength	=	arrClasses.length;
	var x;
	var checkall	=	true;
	var classname;
	for(x=0; x<arrLength; x++)
	{
		classname	=	arrClasses[x];
		checkall	=	true;
		$( "."+classname ).each(function( index ) 
		{
			if(checkall)
			{
				$("#"+classname).prop("checked", true);
			}
			else
			{
				$("#"+classname).prop("checked", false);
			}
		});
	}
}
function toggleD(classG)
{
	if($("#"+classG).is(':checked'))
	{
		$("."+classG).prop("checked", true);
	}
	else
	{
		$("."+classG).prop("checked", false);
	}
}
function validateEmodules()
{
	var notempty	=	false;
	$('#frmmodules input[type="checkbox"]').each(function(){
   	 	if($(this).is(":checked"))
   	 	{
   	 		notempty	=	true;
   	 	}
	});
	if(notempty == false)
	{
		MessageType.infoMsg('Please select module/modules.');
		return false;
	}
	else
	{
		return true;
	}
}
function submitEmodules()
{
	var frmmodules		=	$("#frmmodules").serialize();
	var txtEusername	=	$("#tdEMuser").text();
	$.ajax({
			data		:	frmmodules,
			type		:	"POST",
			url			:	'user.php?action=SAVEMODULES&USERNAME='+txtEusername,
			beforeSend	:	function()
						{
							$('#divloader').dialog("open");
						},
			success		:	function(response)
						{
							$("#divdebug").html(response);
							$('#divloader').dialog("close");
						}
			});
}