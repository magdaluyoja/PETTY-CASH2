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
		$DATE_Q	=	"AND ((SAVEDDT BETWEEN  '{$SDATE}' AND '{$EDATE}')
					 OR   (REQUESTEDDT BETWEEN  '{$SDATE}' AND '{$EDATE}')
					 OR	  (APPROVEDDT BETWEEN  '{$SDATE}' AND '{$EDATE}')
					 OR	  (RELEASEDDT BETWEEN  '{$SDATE}' AND '{$EDATE}'))";
	}
	$fill 	=	"SELECT * FROM  FDCFINANCIALS_PC.REIMBURSEMENTREQ_HDR
				 WHERE REQUESTEDBY = '{$_SESSION["PC"]['ID']}' AND (STATUS = 'SUBMITTED' OR STATUS = 'APPROVED' )$REIM_Q $DATE_Q
				 ORDER BY SAVEDDT DESC";
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
							<td class='td-ca-list-title curved30px' colspan='8'>Reimbursement Request List</td>
						</tr>
						<tr align='center' class='trl-ca-list-hdr header'>
							<th class='padding'>Trx. No.</th>
							<th class='padding'>Reimbursement Amount</th>
							<th class='padding'>Requested Date</th>
							<th class='padding'>Age(Day/s)</th>
							<th class='padding'>Status</th>
							<th class='padding'>Approved Date</th>
							<th class='padding'>Approved by</th>
							<td class='padding'>Action</td>
						</tr>
					</thead>
					<tbody>";
	if($rsfill->RecordCount() == 0)
	{	
		echo $table	.=	"<tr align='center' class=''>
							<td class='header no-record tr-ca-list-dtls' colspan='8'>No records found.</td>
						</tr>
					</table>";
		exit();
	}
	else 
	{
		while (!$rsfill->EOF) {
			$REIM_NO	=	$rsfill->fields['REIM_NO'];
			$AMOUNT		=	$rsfill->fields['AMOUNT'];
			$STATUS		=	$rsfill->fields["STATUS"];
			$REQUESTEDDT=	$rsfill->fields['REQUESTEDDT'];
			$APPROVEDBY	=	$rsfill->fields['APPROVEDBY'];
			$APPROVEDBY	=	$REQUESTEDBY = $DATASOURCE->selval($conn_172,PCDBASE,"USERS","NAME","USERID = '{$APPROVEDBY}'");
			if($REQUESTEDDT != "0000-00-00 00:00:00"){
				$REQUESTEDDT	= date('Y-m-d', strtotime($REQUESTEDDT));
				$age		=	number_format($diff = (abs(strtotime(date('Y-m-d H:i:s')) - (strtotime($REQUESTEDDT))))/86400, 2);
			}else {$REQUESTEDDT = "";}
			if($rsfill->fields['APPROVEDDT'] != "0000-00-00 00:00:00"){
				$APPROVEDDT	= date('Y-m-d', strtotime($rsfill->fields['APPROVEDDT']));
				$age		=	"";
			}else {$APPROVEDDT = "";}
			$recallbtn = "<img src='/PETTY_CASH/images/btncancel.png' 	class='smallimgbuttons smallbuttonsboredered btnrecall tooltips' alt='Recall' 	title='Recall'	data-reimno='$REIM_NO'>";
			$table	.=	"<tr align='center'>
							<td class='ca-dtls-lnk padding tooltips tr-ca-list-dtls' title='Click to view details.' data-trxno='$REIM_NO'>$REIM_NO</td>
							<td class='tr-ca-list-dtls'>".number_format($AMOUNT,2)."</td>
							<td class='tr-ca-list-dtls'>$REQUESTEDDT</td>
							<td class='tr-ca-list-dtls'>$age</td>
							<td class='tr-ca-list-dtls'>$STATUS</td>
							<td class='tr-ca-list-dtls'>$APPROVEDDT</td>
							<td class='tr-ca-list-dtls'>$APPROVEDBY</td>
							<td class='tr-ca-list-dtls'>$recallbtn</td>
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
if ($action == "RECALLREIM") 
{
	$REIMNO	=	$_GET["REIMNO"];
	$conn_172->StartTrans();
		$RECALLREIM	=	"UPDATE ".PCDBASE.".REIMBURSEMENTREQ_HDR SET STATUS = 'RECALLED', RECALLEDDT = '{$TODAY}'
							 WHERE REIM_NO = '{$REIMNO}'";
		$rsRECALLREIM = 	$DATASOURCE->execQUERY($conn_172,$RECALLREIM,$_SESSION["PC"]["USERNAME"],"REIMBURSEMENT APPROVAL","RECALL");
		if($rsRECALLREIM)
		{
			echo "<script>
					$('document').ready(function(){
						MessageType.successMsg('Reimbursement request with transaction number $REIMNO has been successfully recalled.');
						getREIMlist();
					});
				  </script>";
		}
	$conn_172->CompleteTrans();
	exit();
}
include("recall.html");
?>