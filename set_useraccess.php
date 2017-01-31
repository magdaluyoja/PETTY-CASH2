<?php
	include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/config/config.php");
	include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/config/class_db.php");
	
//	$getusers	=	"SELECT USERNAME,NAME FROM ".PCDBASE.". USERS WHERE STATUS = 'Active'";
//	$getusers	=	"SELECT USERNAME,NAME FROM ".PCDBASE.". USERS WHERE STATUS = 'Active' AND USERLEVEL = 'Manager'";
	$getusers	=	"SELECT USERNAME,NAME FROM ".PCDBASE.". USERS WHERE STATUS = 'Active' AND DEPT_INITIAL = 'HR'";
	$rsgetusers	=	$conn_172->Execute($getusers);
	if($rsgetusers == false)
	{
		$errmsg	=	($conn_172->ErrorMsg()."::".__LINE__); 
		$DATASOURCE->logError($errmsg,$getusers,$_SESSION["PC"]["USERNAME"],"","");
		$DATASOURCE->displayError();
	}
	else 
	{
		while (!$rsgetusers->EOF) {
			$USERNAME	=	$rsgetusers->fields["USERNAME"];
			$NAME		=	$rsgetusers->fields["NAME"];
//			$arrModules	=	array("cash_advance","ca_recall","ca_request","liquidation","li_filing","maintenance","mn_password","mn_theme","reimbursement","re_filing","re_recall");
//			$arrModules	=	array("ca_approval","li_approval","mn_approver","re_approval");
			$arrModules	=	array("mn_approverHR","mn_dayslimit","mn_holidays","mn_user");
			for ($a = 0; $a < count($arrModules); $a++)
			{
				$modname = $arrModules[$a];
				echo $inserAccess	=	"INSERT INTO ".PCDBASE.".USER_ACCESS(`USERNAME`, `LINK_ID`) VALUES('{$USERNAME}','{$modname}')";
				echo "-->$NAME<br>";
				$rsinserAccess	=	$DATASOURCE->execQUERY($conn_172,$inserAccess,"","","");
			}
			$rsgetusers->MoveNext();
		}
	}
?>