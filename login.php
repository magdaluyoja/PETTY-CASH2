<?php
	session_start();
	set_time_limit(0);
	include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/config/config.php");
	include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/config/class_db.php");
	$action = $_GET['action'];
	if($action == "LOGIN")
	{
		$username 	= addslashes($_GET["username"]);
		$password 	= addslashes($_GET["password"]);
		$GETUSER	= "SELECT * FROM ".PCDBASE.".USERS WHERE USERNAME = '{$username}' AND PASSWORD = '{$password}' AND STATUS ='Active'";
//		echo $GETUSER; exit();
		$RSGETUSER	= $conn_172->Execute($GETUSER);	
		if ($RSGETUSER == false) 
		{
			$errmsg	=	($conn_172->ErrorMsg()."::".__LINE__); 
			$DATASOURCE->logError($errmsg,$GETUSER,$_SESSION["PC"]["USERNAME"],"LOGIN","LOGIN");
			$DATASOURCE->displayError();
			exit();
		}
		else 
		{
			if ($RSGETUSER->RecordCount() == 0)
			{
				echo "<script>
						$('document').ready(function(){
							MessageType.infoMsg('Invalid Username or Password.');
							$('#password').val('');
						});
					  </script>";
			}
			else 
			{
				$_SESSION["PC"]["USERNAME"]			=	$RSGETUSER->fields["USERNAME"];
				$_SESSION["PC"]["NAME"]				=	$RSGETUSER->fields["NAME"];
				$_SESSION["PC"]["POSITION"]			=	$RSGETUSER->fields['POSITION'];
				$_SESSION["PC"]["DEPARTMENT"]		=	$RSGETUSER->fields['DEPARTMENT'];
				$_SESSION["PC"]["DEP"]				=	$RSGETUSER->fields['DEPT_INITIAL'];
				$_SESSION["PC"]["ID"]				=	$RSGETUSER->fields['USERID'];
				$_SESSION["PC"]["USERLEVEL"]		=	$RSGETUSER->fields['USERLEVEL'];
				$_SESSION["PC"]["ISAPPROVER"]		=	$RSGETUSER->fields['ISAPPROVER'];
				$_SESSION["PC"]["ISCUSTODIAN"]		=	$RSGETUSER->fields['ISCUSTODIAN'];
				$_SESSION["PC"]["ISSPECIALCHILD"]	=	$RSGETUSER->fields['IS_SPECIAL_CHILD'];
				$_SESSION["PC"]["THEME"]			=	$DATASOURCE->selval($conn_172,PCDBASE,"THEME","THEME","USERNAME = '{$_SESSION["PC"]["USERNAME"]}'");
				$_SESSION["PC"]["MB"]				=	$DATASOURCE->selval($conn_172,PCDBASE,"THEME","MAIN_BACKGROUND","USERNAME = '{$_SESSION["PC"]["USERNAME"]}'");
				$_SESSION["PC"]["CB"]				=	$DATASOURCE->selval($conn_172,PCDBASE,"THEME","CONTENT_BACKGROUND","USERNAME = '{$_SESSION["PC"]["USERNAME"]}'");
				echo "<script>
						location.reload();
					  </script>";
			}
		}
		exit();
	}
?>
<div id="demo-header" class="shadowed curved5px">
	<a id="login-link" title="Login"href="#login">&nbsp;&nbsp;USER LOGIN</a>
	<div id="login-panel" class="shadowed">
		<table width="100%">
			<tr>
				<td>USERNAME</td>
			</tr>
			<tr>
				<td><input name="username" id="username" type="text" onKeyDown="if (event.keyCode == 13){ $('#submit').trigger('click'); }" value=""/></td>
			</tr>
			<tr>
				<td>PASSWORD</td>
			</tr>
			<tr>
				<td><input name="password" id="password" type="password" onKeyDown="if (event.keyCode == 13){ $('#submit').trigger('click'); }" value="" /></td>
			</tr>
			<tr>
				<td><br><img id="submit" src="/PETTY_CASH/images/24b.png" style="width: 90px;border-radius:5px;cursor:pointer;"></td>
			</tr>
		</table>
	</div><!-- /login-panel -->
</div><!-- /demoheader -->
<div id="divlogin"></div>