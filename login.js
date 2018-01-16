$("document").ready(function(){
	$("#username").focus();
	$("#submit").click(function(){
		var username	=	$("#username").val();
		var password	=	$("#password").val();
		var errormsg	=	"";
		if(username == "" && password != "")
		{
			errormsg	=	"Username is empty.";
		}
		if(password == "" && username != "")
		{
			errormsg	=	"Password is empty.";
		}
		if(password == "" && username == "")
		{
			errormsg	=	"Username and Password are empty.";
		}
		
		if(errormsg == "")
		{
			$.ajax({
					type:	"GET",
					url	:	"login.php?action=LOGIN&username="+username+"&password="+password,
					success:function(result)
					{
						$("#divlogin").html(result);
					}
			});
		}
		else
		{
			MessageType.infoMsg(errormsg);
		}
	});
	$("#login-link").click(function(){
		$("#login-panel").toggle("blind");
	});
});
