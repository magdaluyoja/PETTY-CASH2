<?php
session_start();
set_time_limit(0);
$action = $_GET["action"];
include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/config/session.php");
include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/config/config.php");
include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/config/class_db.php");
$TODAY	=	date("Y-m-d h:i:s");

if($action == "GETREIMLIST")
{
	$REIMNUM	=	$_GET["REIMNUM"];
	$DATE		=	$_GET["DATE"];
	$SDATE		=	"$DATE 00:00:00";
	$EDATE		=	"$DATE 23:59:59";
	
	if($REIMNUM != undefined AND $REIMNUM != "")
	{
		$REIM_Q = " AND REIM_NO LIKE '%{$REIMNUM}%' ";
	}
	if($DATE != undefined and $DATE != "")
	{
		$DATE_Q	=	" AND REQUESTEDDT BETWEEN  '{$SDATE}' AND '{$EDATE}'";
	}
	
	$ISAPPROVER	=	$DATASOURCE->selval($conn_172,PCDBASE,"USERS","ISAPPROVER","USERID = '{$_SESSION["PC"]["ID"]}'");
	$APPROVERFOR=	$DATASOURCE->selval($conn_172,PCDBASE,"USERS","ISAPPROVERFOR","USERID = '{$_SESSION["PC"]["ID"]}'");
	if($ISAPPROVER == "YES")
	{
		$APPROVERFOR_Q = "OR U.DEPT_INITIAL = '{$APPROVERFOR}'";
	}
	if($_SESSION["PC"]['USERLEVEL'] != "Supervisor")
	{
		$SUPERVISOR	=	" OR U.USERLEVEL = 'Supervisor'";
	}
	
	$fill 	=	"SELECT R.*, U.NAME FROM  FDCFINANCIALS_PC.REIMBURSEMENTREQ_HDR R
				 LEFT JOIN  FDCFINANCIALS_PC.USERS U ON U.USERID = R.REQUESTEDBY
				 WHERE R.STATUS = 'SUBMITTED' AND (U.DEPT_INITIAL = '{$_SESSION["PC"]['DEP']}' $APPROVERFOR_Q)
			 	 AND (U.USERLEVEL = 'Custodian' OR U.USERLEVEL = 'Employee' $SUPERVISOR) $REIM_Q $DATE_Q";
	$rsfill	=	$conn_172->Execute($fill);
	if ($rsfill == false) 
	{
		$errmsg	=	($conn_172->ErrorMsg()."::".__LINE__); 
		$DATASOURCE->logError($errmsg,$fill,$_SESSION["PC"]["USERNAME"],"REIMBURSEMENT REQUEST","GETREIMLIST");
		$DATASOURCE->displayError();
	}
	$table	=	"<table border='0' width='100%' class='tblCAlist tablesorter' id='tblREIMlists'>
					<thead>
						<tr>
							<td class='td-ca-list-title curved30px' colspan='7'>Reimbursement Request List</td>
						</tr>
						<tr align='center' class='trl-ca-list-hdr header'>
							<th class='padding'>Trx. No.</th>
							<th class='padding'>Reimbursement Amount</th>
							<th class='padding'>Requested By</th>
							<th class='padding'>Requested Date</th>
							<th class='padding'>Age(Day/s)</th>
							<th class='padding'>Status</th>
							<td class='padding'>Action</td>
						</tr>
					</thead>
					<tbody>";
	if($rsfill->RecordCount() == 0)
	{	
		echo $table	.=	"<tr align='center' class=''>
							<td class='header no-record tr-ca-list-dtls' colspan='7'>No records found.</td>
						</tr>
					</table>";
		exit();
	}
	else 
	{
		while (!$rsfill->EOF) {
			$REIM_NO	=	$rsfill->fields['REIM_NO'];
			$AMOUNT		=	$rsfill->fields['AMOUNT'];
			$NAME		=	$rsfill->fields['NAME'];
			$STATUS		=	$rsfill->fields["STATUS"];
			$REQUESTEDDT=	$rsfill->fields['REQUESTEDDT'];
			if($REQUESTEDDT != "0000-00-00 00:00:00"){
				$REQUESTEDDT	= date('Y-m-d', strtotime($REQUESTEDDT));
				$age		=	number_format($diff = (abs(strtotime(date('Y-m-d H:i:s')) - (strtotime($REQUESTEDDT))))/86400, 2);
			}else {$REQUESTEDDT = "";}
			
			$approvebtn	 	= "<img src='/PETTY_CASH/images/approve_button.png' class='smallimgbuttons btnapprove tooltips' 	alt='Approve' 		title='Approve'		data-reimno='$REIM_NO'>";
			$disapprovebtn 	= "<img src='/PETTY_CASH/images/reject_button.png' 	class='smallimgbuttons btndisapprove tooltips' 	alt='Disapprove' 	title='Disapprove'	data-reimno='$REIM_NO'>";
			$table	.=	"<tr align='center'>
							<td class='ca-dtls-lnk padding tooltips tr-ca-list-dtls' title='Click to view details.' data-trxno='$REIM_NO'>$REIM_NO</td>
							<td class='tr-ca-list-dtls'>".number_format($AMOUNT,2)."</td>
							<td class='tr-ca-list-dtls'>$NAME</td>
							<td class='tr-ca-list-dtls'>$REQUESTEDDT</td>
							<td class='tr-ca-list-dtls'>$age</td>
							<td class='tr-ca-list-dtls'>$STATUS</td>
							<td class='tr-ca-list-dtls'>$approvebtn $disapprovebtn</td>
						</tr>";
			$rsfill->MoveNext();
		}
		$table	.=	"</tbody>
				</table>";
		echo $table;
	}
	exit();
}
if ($action == "VIEWREIMBURSEMENTDTLS") 
{
	$TRXNO	=	$_GET["TRXNO"];
	echo $DATASOURCE->getREIMdtlsparticulars($TRXNO,$conn_172);
	exit();
}
if ($action == "APPROVEREIM") 
{
	$REIMNO	=	$_GET["REIMNO"];
	$conn_172->StartTrans();
		$approveREIM	=	"UPDATE ".PCDBASE.".REIMBURSEMENTREQ_HDR SET STATUS = 'APPROVED', APPROVEDDT = '{$TODAY}',APPROVEDBY = '{$_SESSION['PC']['ID']}'
							 WHERE REIM_NO = '{$REIMNO}'";
		$rsapproveREIM = 	$DATASOURCE->execQUERY($conn_172,$approveREIM,$_SESSION["PC"]["USERNAME"],"REIMBURSEMENT APPROVAL","APPROVEREIM");
		if($rsapproveREIM)
		{
			echo "<script>
					$('document').ready(function(){
						MessageType.successMsg('Reimbursement request with transaction number $REIMNO has been successfully approved.');
						getREIMlist();
					});
				  </script>";
		}
	$conn_172->CompleteTrans();
	exit();
}
if ($action == "DISAPPROVEREIM") 
{
	$REIMNO	=	$_GET["REIMNO"];
	$conn_172->StartTrans();
		$approveREIM	=	"UPDATE ".PCDBASE.".REIMBURSEMENTREQ_HDR SET STATUS = 'DISAPPROVED', DISAPPROVEDDT = '{$TODAY}',DISAPPROVEDBY = '{$_SESSION['PC']['ID']}'
							 WHERE REIM_NO = '{$REIMNO}'";
		$rsapproveREIM = 	$DATASOURCE->execQUERY($conn_172,$approveREIM,$_SESSION["PC"]["USERNAME"],"REIMBURSEMENT APPROVAL","DISAPPROVEREIM");
		if($rsapproveREIM)
		{
			echo "<script>
					$('document').ready(function(){
						MessageType.successMsg('Reimbursement request with transaction number $REIMNO has been successfully disapproved.');
						getREIMlist();
					});
				  </script>";
		}
	$conn_172->CompleteTrans();
	exit();
}
include("approval.html");
?>