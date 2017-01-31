<?php
session_start();
set_time_limit(0);
$action = $_GET["action"];
include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/config/session.php");
include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/config/config.php");
include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/config/class_db.php");
$TODAY	=	date("Y-m-d h:i:s");
if($action == "SETAPP")
{
	$user	=	$_GET['APP'];
	$APPNAME=	$_GET['APPNAME'];
	$setapp		=	"UPDATE ".PCDBASE.".USERS SET ISAPPROVER = 'YES' WHERE USERID = '{$user}'";
	$rssetapp	=	$DATASOURCE->execQUERY($conn_172,$setapp,$_SESSION["PC"]["USERNAME"],"APPROVER AMINTENANCE","SETAPP");
	echo "<script>
			MessageType.successMsg('$APPNAME has been successfully set as an approver.');
		  </script>";
exit();
}
if($action == "UNSETAPP")
{
	$user	=	$_GET['APP'];
	$APPNAME=	$_GET['APPNAME'];
	$setapp		=	"UPDATE ".PCDBASE.".USERS SET ISAPPROVER = 'NO' WHERE USERID = '{$user}'";
	$rssetapp	=	$DATASOURCE->execQUERY($conn_172,$setapp,$_SESSION["PC"]["USERNAME"],"APPROVER AMINTENANCE","SETAPP");
	echo "<script>
			MessageType.successMsg('$APPNAME has been successfully unset as an approver.');
		  </script>";
exit();
}
include("approver.html");
?>