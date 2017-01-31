<?php
session_start();
set_time_limit(0);
$action = $_GET["action"];
include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/config/session.php");
include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/config/config.php");
include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/config/class_db.php");
$TODAY	=	date("Y-m-d h:i:s");
if ($action == 'SEARCHUSER') 
{
	$user		=	$_GET['USER'];
	$sel	 	=	"SELECT * from ".PCDBASE.".USERS  where USERNAME LIKE '%{$user}%' OR NAME LIKE '%{$user}%' OR USERNAME LIKE '%{$user}%' limit 1";
	$rssel		=	$conn_172->Execute($sel);
	if ($rssel == false) 
	{
		$errmsg	=	($conn_172->ErrorMsg()."::".__LINE__); 
		$DATASOURCE->logError($errmsg,$selapp,$_SESSION["PC"]["USERNAME"],"APPROVER HR","GETAPP");
		$DATASOURCE->displayError();
	}
	$cnt	=	$rssel->RecordCount();
	while (!$rssel->EOF) 
	{
		$userid		= $rssel->fields['USERID'];
		$name		= $rssel->fields['NAME'];
		$position 	= $rssel->fields['POSITION'];
		$days		= $rssel->fields['DAYSLIMIT'];
		
		$rssel->MoveNext();
	}
	if ($cnt > 0)
	{
		echo "<script> 
				$('#txtname').val('$name');
				$('#txtposition').val('$position');
				$('#txtdays').val('$days');
				$('#hiduserid').val('$userid');
			  </script>";
	}
	else 
	{
		echo "<script> 
				MessageType.infoMsg('No records found.');
				$('#txtname').val('');
				$('#txtposition').val('');
				$('#txtdays').val('');
			  </script>";
	}
	exit();
}
if($action == 'UPDATE')
{
	$dayslimit	=	$_POST['txtdays'];
	$userid		=	$_POST['hiduserid'];
	
	$updays		=	"UPDATE ".PCDBASE.".USERS SET DAYSLIMIT = '{$dayslimit}' WHERE USERID = '{$userid}'";
	$rsupdays	=	$DATASOURCE->execQUERY($conn_172,$updays,$_SESSION["PC"]["USERNAME"],"DAYS LIMIT MAINTENANCE","UPDATE");
	echo "<script>
			MessageType.successMsg('Record has been successfully updated.');
			$('#txtname').val('');
			$('#txtposition').val('');
			$('#txtdays').val('');
		  </script>";
	exit();
}
include("days_limit.html");
?>