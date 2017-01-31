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
		$CA_Q = " AND LI.CA_NO LIKE '%{$CANUM}%' ";
	}
	if($DATE != undefined and $DATE != "")
	{
		$DATE_Q	=	"AND LI.REQUESTEDDT BETWEEN  '{$SDATE}' AND '{$EDATE}'";
	}
	if($_SESSION["PC"]['USERLEVEL'] != "Supervisor")
	{
		$ISSUPERVISOR_Q	=	" OR U.USERLEVEL = 'Supervisor'  ";
	}
	$ISAPPROVER		=	$DATASOURCE->selval($conn_172,PCDBASE,"USERS","ISAPPROVER","USERID= '{$_SESSION["PC"]["ID"]}'");
	$ISAPPROVERFOR	=	$DATASOURCE->selval($conn_172,PCDBASE,"USERS","ISAPPROVERFOR","USERID= '{$_SESSION["PC"]["ID"]}'");
	if($ISAPPROVERFOR == 'YES')	
	{
		$FOR_Q	=	" OR CA.DEPARTMENT = '{$ISAPPROVERFOR}'";
	}
	else 
	{
		$FOR_Q	=	"";
	}
	$fill 	=	"SELECT LI.*,U.NAME, CA.AMOUNT FROM ".PCDBASE.".LIQUIDATION_HDR LI 
				 LEFT JOIN ".PCDBASE.".USERS U ON U.USERID = LI.REQUESTEDBY
				 LEFT JOIN ".PCDBASE.".CASH_ADVANCE_HDR CA ON CA.CA_NO = LI.CA_NO
				 WHERE  (LI.STATUS = 'SUBMITTED') AND (U.DEPT_INITIAL = '{$_SESSION["PC"]['DEP']}' $FOR_Q)
				 AND (U.USERLEVEL = 'Custodian' OR U.USERLEVEL = 'Employee' $ISSUPERVISOR_Q)  $CA_Q $DATE_Q ORDER BY LI.REQUESTEDDT DESC";
		
	$rsfill	=	$conn_172->Execute($fill);
	if ($rsfill == false) 
	{
		$errmsg	=	($conn_172->ErrorMsg()."::".__LINE__); 
		$DATASOURCE->logError($errmsg,$fill,$_SESSION["PC"]["USERNAME"],"CA ISSUANCE","GETCALIST");
		$DATASOURCE->displayError();
	}
	$table	=	"<table border='0' width='100%' class='tblCAlist tablesorter' id='tblCAlists'>
					<thead>
						<tr>
							<td class='td-ca-list-title curved30px' colspan='8'>Cash Advance Request List</td>
						</tr>
						<tr align='center' class='trl-ca-list-hdr header'>
							<th class='padding'>C.A. No.</th>
							<th class='padding'>Requested by</th>
							<th class='padding'>Requested Date</th>
							<th class='padding'>C.A. Amount</th>
							<th class='padding'>Liquidation Amount</th>
							<th class='padding'>Reimbursement Amount</th>
							<th class='padding'>Returning Amount</th>
							<td class='padding' width='100px'>Action</td>
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
			$CA_NO				=	$rsfill->fields['CA_NO'];
			$AMOUNT				=	$rsfill->fields['AMOUNT'];
			$REQUESTEDBY		=	$rsfill->fields['NAME'];
			$LIQUIDATIONAMT		=	$rsfill->fields["LIQUI_AMOUNT"];
			$REIMBURSEMENTAMT	=	$rsfill->fields["REIMBURSEMENT_AMT"];
			$RETURNINGAMT		=	$rsfill->fields["RETURNING_AMT"];
			$STATUS				=	$rsfill->fields["STATUS"];
			$REQUESTEDDT		=	$rsfill->fields["REQUESTEDDT"];
			
			if($REQUESTEDDT != ""){
					$REQUESTEDDT	= date('Y-m-d', strtotime($REQUESTEDDT));
			}else {	$REQUESTEDDT 	= "";}
			if($STATUS == "SUBMITTED"){
				$BTNDETAILS		= 	"<img src='/PETTY_CASH/images/viewliqui.png' 		class='smallimgbuttons btndetails		tooltips' alt='Liquidation Details' 	title='Liquidation Details'	data-cano='$CA_NO' data-amt='$AMOUNT'>";
				$BTNAPPROVE 	= 	"<img src='/PETTY_CASH/images/approve_button.png' 	class='smallimgbuttons btnapprove		tooltips' alt='Approve'				 	title='Approve'				data-cano='$CA_NO' data-amt='$AMOUNT'>";
				$BTNDISAPPROVE 	= 	"<img src='/PETTY_CASH/images/reject_button.png' 	class='smallimgbuttons btndisapprove	tooltips' alt='Disapprove' 				title='Disapprove'			data-cano='$CA_NO' data-amt='$AMOUNT'>";
			}
							
			$table	.=	"<tr align='center'>
							<td class='ca-dtls-lnk padding tooltips tr-ca-list-dtls' title='Click to view details.' data-cano='$CA_NO'>$CA_NO</td>
							<td class='tr-ca-list-dtls'>$REQUESTEDBY</td>
							<td class='tr-ca-list-dtls'>$REQUESTEDDT</td>
							<td class='tr-ca-list-dtls'>".number_format($AMOUNT,2)."</td>
							<td class='tr-ca-list-dtls'>".number_format($LIQUIDATIONAMT,2)."</td>
							<td class='tr-ca-list-dtls'>".number_format($REIMBURSEMENTAMT,2)."</td>
							<td class='tr-ca-list-dtls'>".number_format($RETURNINGAMT,2)."</td>
							<td class='tr-ca-list-dtls'>$BTNDETAILS $BTNAPPROVE $BTNDISAPPROVE</td>
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
if ($action == "VIEWLIQUIDATIONDTLS") 
{
	$CANO		=	$_GET["CANO"];
	$CAAMOUNT	=	$_GET["CAAMOUNT"];
	
	$GETDTLS	=	"SELECT HDR.CA_NO, P.PARTICULARDESC, R.REMARKS, DTL.AMOUNT, DTL.PARTICULAR, DTL.REMARK FROM ".PCDBASE.".LIQUIDATION_HDR HDR
					 LEFT JOIN ".PCDBASE.".LIQUIDATION_DTL DTL on DTL.CA_NO = HDR.CA_NO
					 LEFT JOIN ".PCDBASE.".PARTICULARS P ON P.PARTICULARCODE = DTL.PARTICULAR
					 LEFT JOIN ".PCDBASE.".REMARKS R ON R.REMARKS_ID = DTL.REMARK
					 WHERE HDR.CA_NO = '{$CANO}'";
	$RSGETDTLS	=	$conn_172->Execute($GETDTLS);
	if ($RSGETDTLS == false) 
	{
		$errmsg	=	($conn_172->ErrorMsg()."::".__LINE__); 
		$DATASOURCE->logError($errmsg,$GETDTLS,$_SESSION["PC"]["USERNAME"],"LIQUIDATION REQUEST","LIQUIDATE");
		$DATASOURCE->displayError();
	}
	else 
	{
		$table	=	"<table border='0' class='dialogtable curved5px shadowed' width='100%'>
						<tr>
							<td class='dialogdtltitle' colspan='20'>C.A. No: <a  id='tdCANO' class='td-ca-list-title'>$CANO</a> &nbsp; Amount: <a id='tdCAamount'>$CAAMOUNT</a></td>
						</tr>
						<tr class='dialogdtlheader'>
							<td class='padding'>Purpose</td>
							<td class='padding'>Remarks</td>
							<td class='padding'>Amount</td>
						</tr>";
	$c		=	1;
	while (!$RSGETDTLS->EOF) 
	{
		$PARTICULAR	=	$RSGETDTLS->fields['PARTICULAR'];
		$part		=	$RSGETDTLS->fields['PARTICULARDESC'];
		$rem		=	$RSGETDTLS->fields['REMARKS'];
		if($PARTICULAR == "0005")
		{
			$rem		=	$RSGETDTLS->fields['REMARK'];
		}
		$amount		=	$RSGETDTLS->fields['AMOUNT'];
		$totamount	+=	$amount;
		$table	.=	"<tr>
						<td class='dialogdtldetails'>$part</td>
						<td class='dialogdtldetails'>$rem</td>
						<td class='dialogdtldetails amount'>".number_format($amount,2)."</td>
					</tr>";
		$RSGETDTLS->MoveNext();
	}
		
		$table	.=	"<tr>
						<td class='dialogdtldetails centered' colspan='2'><b>TOTAL</td>
						<td class='dialogdtldetails amount bold padding' id='totalliquiamount'>".number_format($totamount,2)."</td>
			</table>";
		echo $table;
	}
	exit();
}
if($action == "APPROVELIQUIDATION")
{
	$CANO = $_GET["CANO"];
	$RSAPPLIQUI	=	$DATASOURCE->execQUERY($conn_172,"UPDATE ".PCDBASE.".LIQUIDATION_HDR SET STATUS = 'APPROVED', APPROVEDDT = '{$TODAY}',APPROVEDBY = '{$_SESSION["PC"]["ID"]}' WHERE CA_NO  = '{$CANO}'",$_SESSION["PC"]["USERNAME"],"LIQUIDATION REQUEST","APPROVELIQUIDATION");
	if ($RSAPPLIQUI)
	{
		echo "<script>
				$('document').ready(function(){
					MessageType.successMsg('Cash Advance liquidation request with transaction number $CANO has been successfully approved.');
					getCAlist();
				});
			  </script>";
	}
	exit();
}
if($action == "DISAPPROVELIQUIDATION")
{
	$CANO = $_GET["CANO"];
	$RSDISAPPLIQUI	=	$DATASOURCE->execQUERY($conn_172,"UPDATE ".PCDBASE.".LIQUIDATION_HDR SET STATUS = 'DISAPPROVED', DISAPPROVEDDT = '{$TODAY}',DISAPPROVEDBY = '{$_SESSION["PC"]["ID"]}' WHERE CA_NO  = '{$CANO}'",$_SESSION["PC"]["USERNAME"],"LIQUIDATION REQUEST","DISAPPROVELIQUIDATION");
	if ($RSDISAPPLIQUI)
	{
		echo "<script>
				$('document').ready(function(){
					MessageType.successMsg('Cash Advance liquidation request with transaction number $CANO has been successfully disapproved.');
					getCAlist();
				});
			  </script>";
	}
	exit();
}
include("approval.html");
?>