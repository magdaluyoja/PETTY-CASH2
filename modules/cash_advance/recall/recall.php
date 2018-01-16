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
		$DATE_Q	=	"AND((APPROVEDDT BETWEEN  '{$SDATE}' AND '{$EDATE}')
					 OR 
					 (REQUESTEDDT BETWEEN  '{$SDATE}' AND '{$EDATE}')
					 OR 
					 (SAVEDDT BETWEEN  '{$SDATE}' AND '{$EDATE}'))";
	}
	$fill 	=	"SELECT * FROM ".PCDBASE.".CASH_ADVANCE_HDR WHERE REQUESTEDBY = '{$_SESSION['PC']['ID']}' AND (STATUS = 'SUBMITTED' OR STATUS = 'APPROVED' ) $CA_Q $DATE_Q 
				 ORDER BY SAVEDDT DESC";
	$rsfill	=	$conn_172->Execute($fill);
	if ($rsfill == false) 
	{
		$errmsg	=	($conn_172->ErrorMsg()."::".__LINE__); 
		$DATASOURCE->logError($errmsg,$fill,$_SESSION["PC"]["USERNAME"],"CA REQUEST","GETCALIST");
		$DATASOURCE->displayError();
	}
	$table	=	"<table border='0' width='100%' class='tblCAlist tablesorter' id='tblCAlists'>
					<thead>
						<tr>
							<td class='td-ca-list-title curved30px' colspan='7'>Cash Advance Request List</td>
						</tr>
						<tr align='center' class='trl-ca-list-hdr header'>
							<th>C.A. No.</th>
							<th>Amount</th>
							<th>Status</th>
							<th>Requested Date</th>
							<th>Approved by</th>
							<th>Approved Date</th>
							<td>Actions</td>
						</tr>
					</thead>
					<tbody>";
	if($rsfill->RecordCount() == 0)
	{	
		echo $table	.=	"<tr align='center'>
							<td class='header no-record tr-ca-list-dtls' colspan='7'>No records found.</td>
						</tr>
					</table>";
		exit();
	}
	else 
	{
		while (!$rsfill->EOF) {
			$CA_NO		=	$rsfill->fields['CA_NO'];
			$AMOUNT		=	$rsfill->fields['AMOUNT'];
			$STATUS		=	$rsfill->fields['STATUS'];
			$APPROVEDBY	=	$rsfill->fields['APPROVEDBY'];
			$APPROVEDBY	=	$REQUESTEDBY = $DATASOURCE->selval($conn_172,PCDBASE,"USERS","NAME","USERID = '{$APPROVEDBY}'");
			$ISLIQUIDATED=	$rsfill->fields['ISLIQUIDATED'];
			if($rsfill->fields['REQUESTEDDT'] != 0){
				$REQUESTEDDT	= date('Y-m-d', strtotime($rsfill->fields['REQUESTEDDT']));
			}else {$REQUESTEDDT = "";}
			if($rsfill->fields['APPROVEDDT'] != 0 and $rsfill->fields['STATUS']  == 'APPROVED'){
				$age	=	number_format($diff = (abs(strtotime(date('Y-m-d H:i:s')) - (strtotime($rsfill->fields['APPROVEDDT']))))/86400, 2);
			}
			if($rsfill->fields['APPROVEDDT'] != 0){
				$APPROVEDDT	=	 date('Y-m-d', strtotime($rsfill->fields['APPROVEDDT']));
			}else{$APPROVEDDT = "";}
			if($rsfill->fields['RELEASEDDT'] != 0){
				$RELEASEDDT	=	 date('Y-m-d', strtotime($rsfill->fields['RELEASEDDT']));
			}else {$RELEASEDDT = "";}
			$recallbtn = "<img src='/PETTY_CASH/images/btncancel.png' 	class='smallimgbuttons smallbuttonsboredered btnrecall tooltips' alt='Recall' 	title='Recall'	data-cano='$CA_NO'>";
			$table	.=	"<tr align='center'>
							<td class='ca-dtls-lnk padding tooltips tr-ca-list-dtls' title='Click to view details.' data-cano='$CA_NO'>$CA_NO</td>
							<td class='tr-ca-list-dtls'>".number_format($AMOUNT, 2)."</td>
							<td class='tr-ca-list-dtls'>$STATUS</td>
							<td class='tr-ca-list-dtls'>$REQUESTEDDT</td>
							<td class='tr-ca-list-dtls'>$APPROVEDBY</td>
							<td class='tr-ca-list-dtls'>$APPROVEDDT</td>
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
if($action == "RECALLCA")
{
	$CANO	=	$_GET["CANO"];
	$conn_172->StartTrans();
		$recallCA	=	"UPDATE ".PCDBASE.".CASH_ADVANCE_HDR SET STATUS = 'RECALLED', RECALLEDDT = '{$TODAY}'
						 WHERE CA_NO = '{$CANO}'";
		$rsrecallCA = 	$DATASOURCE->execQUERY($conn_172,$recallCA,$_SESSION["PC"]["USERNAME"],"CA RECALL","RECALLCA");
		if($rsrecallCA)
		{
			echo "<script>
					$('document').ready(function(){
						MessageType.successMsg('Cash Advance Request with C.A. number $CANO has been successfully recalled.');
						getCAlist();
					});
				  </script>";
		}
	$conn_172->CompleteTrans();
	exit();
}
include("recall.html");
?>