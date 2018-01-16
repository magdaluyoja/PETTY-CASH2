<?php
session_start();
set_time_limit(0);
$action = $_GET["action"];
include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/config/session.php");
include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/config/config.php");
include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/config/class_db.php");
$TODAY	=	date("Y-m-d h:i:s");
if($action == 'VALIDATE_PASS')
{
	$opassword	=	$_POST['txtoldpassword'];
	$oldpass 	=	$DATASOURCE->selval($conn_172,PCDBASE,"USERS","PASSWORD"," USERNAME = '{$_SESSION["PC"]["USERNAME"]}' AND PASSWORD = '{$opassword}'");
	if($oldpass == "")
	{
		echo "<script>
				MessageType.infoMsg('Incorrect password.');
				$('#txtoldpassword').val('');
			  </script>";
	}
	exit();
}
if($action == "UPDATEPASS")
{
	$newpassword	=	$_POST['txtnewpassword'];
	$new 	=	"UPDATE ".PCDBASE.".USERS SET PASSWORD = '{$newpassword}' WHERE USERID = '{$_SESSION["PC"]['ID']}'";
	$rsnew	=	$DATASOURCE->execQUERY($conn_172,$new,$_SESSION["PC"]["USERNAME"],"PASSWORD AMINTENANCE","UPDATEPASS");
	echo "<script>
			MessageType.successMsg('Password has been successfully updated.');
			$('.input_text').val('');
		  </script>";
	exit();
}
include("password.html");
?>