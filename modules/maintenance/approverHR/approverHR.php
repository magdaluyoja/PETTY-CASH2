<?php
session_start();
set_time_limit(0);
$action = $_GET["action"];
include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/config/session.php");
include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/config/config.php");
include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/config/class_db.php");
$TODAY	=	date("Y-m-d h:i:s");
if($action == "LOADAPPS")
{
	$loadapps	=	"SELECT NAME, POSITION, ISAPPROVER,ISAPPROVERFOR FROM ".PCDBASE.".USERS WHERE DEPARTMENT = '{$dep}' AND (USERLEVEL = 'Supervisor' OR POSITION LIKE '%OFFICER%')";
	$loadapps	.=	"union all SELECT NAME, POSITION, ISAPPROVER,ISAPPROVERFOR  FROM ".PCDBASE.".USERS WHERE (DEPARTMENT != '{$dep}') AND (USERLEVEL = 'Manager')";
	$loadapps	.=	"ORDER BY NAME";	
	$rsloadapps	=	$conn_172->Execute($loadapps);
	if ($rsloadapps == false) 
	{
		$errmsg	=	($conn_172->ErrorMsg()."::".__LINE__); 
		$DATASOURCE->logError($errmsg,$selapp,$_SESSION["PC"]["USERNAME"],"APPROVER HR","GETAPP");
		$DATASOURCE->displayError();
	}
	
	$tableapps	=	"<table id='tblapplists' class='tblCAlist tablesorter' border='0' width='100%'>
						<thead>
							<tr>
								<td class='td-ca-list-title curved30px' colspan='4'>Approver List</td>
							</tr>
							<tr class='trl-ca-list-hdr header centered'>
								<th class='padding'>Name</th>
								<th class='padding'>Position</th>
								<th class='padding'>Approver Status</th>
								<th class='padding'>For Department</th>
							</tr>
						</thead>
						<tbody>";
		while (!$rsloadapps->EOF) 
		{
			$name			=	ucwords(strtolower($rsloadapps->fields['NAME']));
			$position		= 	ucwords(strtolower($rsloadapps->fields['POSITION']));
			$ISAPPROVERFOR	= 	ucwords(strtolower($rsloadapps->fields['ISAPPROVERFOR']));
			if($rsloadapps->fields['ISAPPROVER'] == 'YES')
			{
				$isapp =  'Active';
			}
			else 
			{
				$isapp = 'Inactive';
			}
			$tableapps .= "	<tr>
								<td class='padding tr-ca-list-dtls'>$name</td>
								<td class='padding tr-ca-list-dtls'>$position</td>
								<td class='padding tr-ca-list-dtls centered'>$isapp</td>
								<td class='padding tr-ca-list-dtls'>$ISAPPROVERFOR</td>
							</tr>";
		$rsloadapps->MoveNext();
		}
	 $tableapps .= "</tbody>
	 			</table>";
	 echo $tableapps;
	exit();
}
if($action == "GETAPP")
{
	$dep		=	$_GET['seldep'];
	$selapp		=	"SELECT USERID, NAME, DEPT_INITIAL  FROM ".PCDBASE.".USERS WHERE DEPARTMENT = '{$dep}' AND (USERLEVEL = 'Supervisor' OR POSITION LIKE '%OFFICER%')
					 UNION ALL SELECT USERID, NAME, DEPT_INITIAL  FROM ".PCDBASE.".USERS WHERE (DEPARTMENT != '{$dep}') AND (USERLEVEL = 'Manager')
					 ORDER BY NAME";
//	echo $selapp; exit();
	$rsselapp	=	$conn_172->Execute($selapp);
	if ($rsselapp == false) 
	{
		$errmsg	=	($conn_172->ErrorMsg()."::".__LINE__); 
		$DATASOURCE->logError($errmsg,$selapp,$_SESSION["PC"]["USERNAME"],"APPROVER HR","GETAPP");
		$DATASOURCE->displayError();
	}
	echo "<script>
				$('#selapp').append($('<option />').attr('value', '').text('<-- Please Select -->'));
		  </script>";
	
	while (!$rsselapp->EOF) 
	{
		$val 	=	$rsselapp->fields['USERID'].'-'.$rsselapp->fields['DEPT_INITIAL'];
		$txt	=	ucwords(strtolower($rsselapp->fields['NAME']));
		echo "<script>
					$('#selapp').append($('<option />').attr('value', '$val').text('$txt'));
			  </script>";
		$rsselapp->MoveNext();
	}
	exit();
}
if($action == "SETAPP")
{
	$APP 	= $_GET["APP"];
	$APPNAME= $_GET["APPNAME"];
	$DEP 	= $_GET["DEP"];
	$DEPNAME= $_GET["DEPNAME"];
	$val	=	explode('-',$APP);
	$id		=	$val[0];
	$depI	=	$val[1];

	$setapp		=	"UPDATE ".PCDBASE.".USERS SET ISAPPROVER = 'YES', ISAPPROVERFOR = '{$DEP}' WHERE USERID = '{$id}' AND DEPT_INITIAL  = '{$depI}'";
	$rssetapp	=	$DATASOURCE->execQUERY($conn_172,$setapp,$_SESSION["PC"]["USERNAME"],"APPROVER HR AMINTENANCE","SETAPP");
	echo "<script>
			MessageType.successMsg('$APPNAME has been successfully set as an approver for the $DEPNAME department.');
			loadApps();
		  </script>";
	exit();
}
if($action == "UNSETAPP")
{
	$APP 	= $_GET["APP"];
	$APPNAME= $_GET["APPNAME"];
	$DEP 	= $_GET["DEP"];
	$DEPNAME= $_GET["DEPNAME"];
	$val	=	explode('-',$APP);
	$id		=	$val[0];
	$depI	=	$val[1];

	$setapp		=	"UPDATE ".PCDBASE.".USERS SET ISAPPROVER = 'NO', ISAPPROVERFOR = '' WHERE USERID = '{$id}' AND DEPT_INITIAL  = '{$depI}'";
	$rssetapp	=	$DATASOURCE->execQUERY($conn_172,$setapp,$_SESSION["PC"]["USERNAME"],"APPROVER HR AMINTENANCE","UNSETAPP");
	echo "<script>
			MessageType.successMsg('$APPNAME has been successfully unset as an approver for the $DEPNAME department.');
			loadApps();
		  </script>";
	exit();
}
include("approverHR.html");
?>