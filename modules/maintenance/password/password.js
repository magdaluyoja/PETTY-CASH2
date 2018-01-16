$("document").ready(function(){
	$("#txtconpassword").change(function(){
		var password	=	$('#txtnewpassword').val();
		var cpassword	=	$('#txtconpassword').val();
		
		if(password != cpassword)
		{
			MessageType.infoMsg('Password did not match.');
			$('#txtconpassword').val('')
		}
	});
	$("#txtoldpassword").change(function(){
		var frmpass		=	$('#frmpass').serialize();
		var oldpass		=	$('#txtoldpassword').val();
		if(oldpass != "")
		{
			$.ajax({
					type		:	'POST',
					data		:	frmpass,
					url			:	'password.php?action=VALIDATE_PASS',
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
	});
	$("#btnupdate").click(function(){
		var oldpass		=	$('#txtoldpassword').val();
		var newpass		=	$('#txtnewpassword').val();
		var conpass		=	$('#txtconpassword').val();
		var errmsg 		=	"";
		if(oldpass == "")
		{
			errmsg = " - Current password is empty. <br>";
		}
		if(newpass == "")
		{
			errmsg += " - New password is empty. <br>";
		}
		if(conpass == "")
		{
			errmsg += " - Confirm password is empty. <br>";
		}
		if(errmsg == "")
		{
			MessageType.confirmmsg(updatePass,"Do you want to update your password?","");
		}
		else
		{
			MessageType.infoMsg(errmsg);
		}
	});
});
function updatePass()
{
	var frmpass		=	$('#frmpass').serialize();
	$.ajax({
			type		:	'POST',
			data		:	frmpass,
			url			:	'password.php?action=UPDATEPASS',
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