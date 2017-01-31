<?php
session_start();
set_time_limit(0);
$action = $_GET["action"];
include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/config/session.php");
include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/config/config.php");
include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/config/class_db.php");
$TODAY	=	date("Y-m-d h:i:s");
if ($action=='Q_SEARCHEMP') 
	{
		$empid		=	addslashes($_GET['EMPID']);
		$name		=	addslashes($_GET['NAME']);
			
		$sel	 =	"SELECT * FROM FDCFINANCIALS_PC.USERS WHERE 1";
		
		if (!empty($empid)) 
		{
			$sel	.=	" AND USERNAME  like '%{$empid}%' ";
		}
		if (!empty($name)) 
		{
			$sel	.=	" AND NAME like '%{$name}%' ";
		}
		$sel	.=	" limit 20 ";
//		echo "$sel"; exit();
		$rssel	=	$conn_172->Execute($sel);
		if ($rssel == false)
		{
			$errmsg	=	($conn_172->ErrorMsg()."::".__LINE__); 
			exit();
		}
		$cnt	=	$rssel->RecordCount();
		if ($cnt > 0) 
		{
			echo "<select id='selemp' style='width:500px;height:auto;' onkeypress='smartsel(event);' multiple>";
			while (!$rssel->EOF) 
			{
				$q_emp		=	$rssel->fields['USERNAME'];
				$q_name		=	preg_replace('/[^A-Za-z0-9. \-]/', '', ucwords(strtolower($rssel->fields['NAME'])));
				$cValue		=	$q_emp."|".$q_name;
				echo "<option value=\"$cValue\" onclick=\"smartsel('click');\">".$q_name."</option>";
				$rssel->MoveNext();
			}
			echo "</select>";
		}
		else
		{
			echo "";
		}
		exit();
	}
include("ca_detailed.html");
?>