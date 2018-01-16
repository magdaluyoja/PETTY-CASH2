<?php
session_start();
set_time_limit(0);
$action = $_GET["action"];
include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/config/session.php");
include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/config/config.php");
include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/config/class_db.php");
$TODAY	=	date("Y-m-d h:i:s");
if($action == "GETCALIST")
{
	$CANUM	=	$_GET["CANUM"];
	$DATE	=	$_GET["DATE"];
	$SDATE	=	"$DATE 00:00:00";
	$EDATE	=	"$DATE 23:59:59";
	
	if($CANUM != undefined AND $CANUM != "")
	{
		$CA_Q = " AND CA_NO LIKE '%{$CANUM}%' ";
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
	$fill 	=	"SELECT CA.*,U.NAME FROM ".PCDBASE.".CASH_ADVANCE_HDR CA 
				 LEFT JOIN ".PCDBASE.".USERS U ON U.USERID = CA.REQUESTEDBY
				 WHERE  CA.STATUS = 'SUBMITTED' AND (U.DEPT_INITIAL = '{$_SESSION["PC"]['DEP']}' $APPROVERFOR_Q)
				 AND (U.USERLEVEL = 'Custodian' OR U.USERLEVEL = 'Employee' $SUPERVISOR) $CA_Q $DATE_Q 
				 ORDER BY SAVEDDT DESC";
	$rsfill	=	$conn_172->Execute($fill);
	if ($rsfill == false) 
	{
		$errmsg	=	($conn_172->ErrorMsg()."::".__LINE__); 
		$DATASOURCE->logError($errmsg,$fill,$_SESSION["PC"]["USERNAME"],"CA APPROVAL","GETCALIST");
		$DATASOURCE->displayError();
	}
	$table	=	"<table border='0' width='100%' class='tblCAlist tablesorter' id='tblCAlists'>
					<thead>
						<tr>
							<td class='td-ca-list-title curved30px' colspan='6'>Cash Advance Request List</td>
						</tr>
						<tr align='center' class='trl-ca-list-hdr header'>
							<th>C.A. No.</th>
							<th>Amount</th>
							<th>Status</th>
							<th>Requested By</th>
							<th>Requested Date</th>
							<td>Actions</td>
						</tr>
					</thead>
					<tbody>";
	if($rsfill->RecordCount() == 0)
	{	
		echo $table	.=	"<tr align='center' class=''>
							<td class='header no-record tr-ca-list-dtls' colspan='6'>No records found.</td>
						</tr>
					</table>";
		exit();
	}
	else 
	{
		while (!$rsfill->EOF) {
			$CA_NO			=	$rsfill->fields['CA_NO'];
			$AMOUNT			=	$rsfill->fields['AMOUNT'];
			$STATUS			=	$rsfill->fields['STATUS'];
			$REQUESTEDBY 	= 	$rsfill->fields['NAME'];
			
			if($rsfill->fields['REQUESTEDDT'] != 0){
					$REQUESTEDDT	= date('Y-m-d', strtotime($rsfill->fields['REQUESTEDDT']));
			}else {	$REQUESTEDDT 	= "";}
			
			$approvebtn	 	= "<img src='/PETTY_CASH/images/approve_button.png' class='smallimgbuttons btnapprove tooltips' 	alt='Approve' 		title='Approve'		data-cano='$CA_NO'>";
			$disapprovebtn 	= "<img src='/PETTY_CASH/images/reject_button.png' 	class='smallimgbuttons btndisapprove tooltips' 	alt='Disapprove' 	title='Disapprove'	data-cano='$CA_NO'>";
			$table	.=	"<tr align='center'>
							<td class='ca-dtls-lnk padding tooltips tr-ca-list-dtls' title='Click to view details.' data-cano='$CA_NO'>$CA_NO</td>
							<td class='tr-ca-list-dtls'>".number_format($AMOUNT, 2)."</td>
							<td class='tr-ca-list-dtls'>$STATUS</td>
							<td class='tr-ca-list-dtls'>$REQUESTEDBY</td>
							<td class='tr-ca-list-dtls'>$REQUESTEDDT</td>
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
if($action == "VIEWCADTLS")
{
	$CANO		=	$_GET["CANO"];
	echo $DATASOURCE->getCAdtlsparticulars($CANO,$conn_172);
	echo "<script>
				$('document').ready(function(){
					$('#divparticulars').dialog('open');
				});
		  </script>";
	exit();
}
if ($action == "APPROVECA") 
{
	$CANO	=	$_GET["CANO"];
	$conn_172->StartTrans();
		$approveCA	=	"UPDATE ".PCDBASE.".CASH_ADVANCE_HDR SET STATUS = 'APPROVED', APPROVEDDT = '{$TODAY}',APPROVEDBY = '{$_SESSION['PC']['ID']}'
						 WHERE CA_NO = '{$CANO}'";
		$rsapproveCA = 	$DATASOURCE->execQUERY($conn_172,$approveCA,$_SESSION["PC"]["USERNAME"],"CA APPROVAL","APPROVECA");
		if($rsapproveCA)
		{
			echo "<script>
					$('document').ready(function(){
						MessageType.successMsg('Cash Advance Request with C.A. number $CANO has been successfully approved.');
						getCAlist();
					});
				  </script>";
		}
	$conn_172->CompleteTrans();
	exit();
}
if ($action == "DISAPPROVECA") 
{
	$CANO	=	$_GET["CANO"];
	$conn_172->StartTrans();
		$disapproveCA	=	"UPDATE ".PCDBASE.".CASH_ADVANCE_HDR SET STATUS = 'DISAPPROVED', DISAPPROVEDDT = '{$TODAY}',DISAPPROVEDBY = '{$_SESSION['PC']['ID']}'
							 WHERE CA_NO = '{$CANO}'";
		$rsdisapproveCA = 	$DATASOURCE->execQUERY($conn_172,$disapproveCA,$_SESSION["PC"]["USERNAME"],"CA APPROVAL","DISAPPROVECA");
		if($rsdisapproveCA)
		{
			echo "<script>
					$('document').ready(function(){
						MessageType.successMsg('Cash Advance Request with C.A. number $CANO has been successfully disapproved.');
						getCAlist();
					});
				  </script>";
		}
	$conn_172->CompleteTrans();
	exit();
}
include("approval.html");
?>
