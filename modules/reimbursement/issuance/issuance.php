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
		$DATE_Q	=	"AND APPROVEDDT BETWEEN  '{$SDATE}' AND '{$EDATE}'";
	}
	$fill 	=	"SELECT R.*, U.NAME FROM  FDCFINANCIALS_PC.REIMBURSEMENTREQ_HDR R
				 LEFT JOIN  FDCFINANCIALS_PC.USERS U ON U.USERID = R.REQUESTEDBY
				 WHERE R.STATUS = 'APPROVED' $REIM_Q $DATE_Q
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
							<td class='td-ca-list-title curved30px' colspan='7'>Reimbursement Request List</td>
						</tr>
						<tr align='center' class='trl-ca-list-hdr header'>
							<th class='padding'>Trx. No.</th>
							<th class='padding'>Reimbursement Amount</th>
							<th class='padding'>Requested By</th>
							<th class='padding'>Approved Date</th>
							<th class='padding'>Status</th>
							<th class='padding'>Age(Day/s)</th>
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
			$STATUS		=	$rsfill->fields["STATUS"];
			$NAME		=	$rsfill->fields["NAME"];
			if($rsfill->fields['APPROVEDDT'] != "0000-00-00 00:00:00"){
				$APPROVEDDT	= date('Y-m-d', strtotime($rsfill->fields['APPROVEDDT']));
				$age		=	number_format($diff = (abs(strtotime(date('Y-m-d H:i:s')) - (strtotime($APPROVEDDT))))/86400, 2);
				
			}else {$APPROVEDDT = "";$age		=	"";}
			$issue 	= "<img src='/PETTY_CASH/images/liquidate.png' 	class='smallimgbuttons btnissue	tooltips' alt='Issue Reimbursement' 	title='Issue Reimbursement' 	data-reimno='$REIM_NO' data-reimamt = '$AMOUNT'>";
			
			$table	.=	"<tr align='center'>
							<td class='ca-dtls-lnk padding tooltips tr-ca-list-dtls' title='Click to view details.' data-trxno='$REIM_NO'>$REIM_NO</td>
							<td class='tr-ca-list-dtls'>".number_format($AMOUNT,2)."</td>
							<td class='tr-ca-list-dtls'>$NAME</td>
							<td class='tr-ca-list-dtls'>$APPROVEDDT</td>
							<td class='tr-ca-list-dtls'>$STATUS</td>
							<td class='tr-ca-list-dtls'>$age</td>
							<td class='tr-ca-list-dtls'>$issue</td>
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
if ($action == "ISSUEREIM") 
{
	$REIMNO		=	$_GET["REIMNO"];
	$REIMAMOUNT	=	$_GET["REIMAMOUNT"];
	
	$GETDTLS	=	"SELECT HDR.REIM_NO, P.PARTICULARDESC, R.REMARKS, DTL.AMOUNT, DTL.PURPOSE, DTL.REMARKS as REMARK, HDR.AMOUNT AS REAMOUNT FROM ".PCDBASE.".REIMBURSEMENTREQ_HDR HDR
					 LEFT JOIN ".PCDBASE.". REIMBURSEMENTREQ_DTL DTL on DTL.REIM_NO = HDR.REIM_NO
					 LEFT JOIN ".PCDBASE.".PARTICULARS P ON P.PARTICULARCODE = DTL.PURPOSE
					 LEFT JOIN ".PCDBASE.".REMARKS R ON R.REMARKS_ID = DTL.REMARKS
					 WHERE HDR.REIM_NO='{$REIMNO}'";
	$RSGETDTLS	=	$conn_172->Execute($GETDTLS);
	if ($RSGETDTLS == false) 
	{
		$errmsg	=	($conn_172->ErrorMsg()."::".__LINE__); 
		$DATASOURCE->logError($errmsg,$GETDTLS,$_SESSION["PC"]["USERNAME"],"REIMBURSEMENT ISSUANCE","ISSUEREIM");
		$DATASOURCE->displayError();
	}
	else 
	{
		$table	=	"<form id='frmreimbursementdtls'>
					 <table border='0' class='dialogtable curved5px shadowed' width='100%'>
						<tr>
							<td class='dialogdtltitle padding' colspan='20'>C.A. No: <a  id='tdREIMNO' class='td-ca-list-title'>$REIMNO</a> &nbsp; Amount: <a id='tdREIMamount' class='td-ca-list-title'>$REIMAMOUNT</a></td>
						</tr>
						<tr class='dialogdtlheader centered'>
							<td class='padding'>Purpose</td>
							<td class='padding'>Remarks</td>
							<td class='padding'>Amount</td>
							<td class='padding'>New Amount</td>
							<td class='padding tooltips' title='Hover checkbox to see Vat Details'>VAT</td>
						</tr>";
	$cnt		=	1;
	while (!$RSGETDTLS->EOF) 
	{
		$part		=	$RSGETDTLS->fields['PARTICULARDESC'];
		$rem		=	$RSGETDTLS->fields['REMARKS'];
		$amount		=	$RSGETDTLS->fields['AMOUNT'];
		$totamount	+=	$amount;
		$partcode	=	$RSGETDTLS->fields['PURPOSE'];
		$remcode	=	$RSGETDTLS->fields['REMARK'];
		$reimamount	=	$RSGETDTLS->fields['REAMOUNT'];
		if($partcode != '0005')
		{
			$rem = $rem;
		}else 
		{
			$rem = $remcode;
		}
		$table	.=	"<tr  class='dialogdtldetails'>
						<td>$part</td>
						<td>$rem</td>
						<td class=' amount'>".number_format($amount,2)."</td>
						<td>
							<input type = 'text' id = 'txtNamount_$cnt' name = 'txtNamount_$cnt' value = '$amount' class = 'input_text curved5px amount txtnewamount' size='15'>
						</td>
						<td id='dialogdtldetails$cnt'>
							<input type = 'checkbox' id = 'vat_$cnt' name = 'vat_$cnt' value = '$part' data-curcnt = '$cnt' data-remarks = '$rem' class='chktaxup tooltips pointer'>
							<label for='rdovatG_$cnt'><input type = 'radio' id = 'rdovatG_$cnt' name = 'rdovat_$cnt' value = 'GOODS' 		disabled> GOODS</label>
							<label for='rdovatS_$cnt'><input type = 'radio' id = 'rdovatS_$cnt' name = 'rdovat_$cnt' value = 'SERVICES' 	disabled> SERVICES</label>
							<input type = 'hidden' id = 'rem_$cnt' name = 'rem_$cnt' value = '$remcode'>
							<input type = 'hidden' id = 'par_$cnt' name = 'par_$cnt' value = '$partcode'>
							<input type = 'hidden' id = 'comp_$cnt' name = 'comp_$cnt'>
							<input type = 'hidden' id = 'vatamt_$cnt' name = 'vatamt_$cnt'>
						</td>
					</tr>";
		$cnt++;
		$RSGETDTLS->MoveNext();
	}
	$difference	=	$CAAMOUNT - $totamount;
	if($difference > 0)
	{
		$difference = abs($difference);
		$RT = $difference;
		$RB	= 0.00;	
	}
	else 
	{
		$RT = 0.00;
		$RB	= abs($difference);
	}
	$table	.=	"<tr>
					<td class='dialogdtldetails centered' colspan='2'><b>TOTAL</td>
					<td class='dialogdtldetails amount bold padding' id='totalliquiamount'>".number_format($totamount,2)."</td>
					<td class='dialogdtldetails amount bold padding' id='tdTOTamount'>".number_format($totamount,2)."</td>
					<td class='dialogdtldetails amount bold padding'>
						<input type = 'hidden' id = 'txtTNamount' 	name = 'txtTNamount' class = 'input' value = '$totamount'>
						<input type = 'hidden' id = 'hidcnt' 		name = 'hidcnt'		 class = 'input' value = '$cnt'>
						<input type = 'hidden' id = 'hidreimburse' 	name = 'hidreimburse'class = 'input' value = '$RB'>
						<input type = 'hidden' id = 'hidreturn' 	name = 'hidreturn'	 class = 'input' value = '$RT'>
						<input type = 'hidden' id = 'hidvatamt' 	name = 'hidvatamt'>
					</td>
				</tr>
		</table>
		</form>";
		echo $table;
	}
	exit();
}
if($action == "GOISSUEREIM")
{
	$REIMNO 	=	$_GET['REIMNO'];
	$user		=	$_SESSION["PC"]['NAME'];
	$appamt		=	$_POST['txtTNamount'];
	$vattotamt	=	$_POST['hidvatamt'];
	$cnt		=	$_POST['hidcnt'];
	
	$conn_172->StartTrans();
	$ISSUEREIM		=	"UPDATE ".PCDBASE.".REIMBURSEMENTREQ_HDR SET STATUS = 'ISSUED',RELEASEDBY = '{$user}', RELEASEDDT = '{$TODAY}', 
						 APPROVED_AMT = '{$appamt}', VAT_AMT = '{$vattotamt}'
						 WHERE REIM_NO = '{$REIMNO}'";
	$RSISSUEREIM	=	$DATASOURCE->execQUERY($conn_172,$ISSUEREIM,$user,"REIMBURSEMENT ISSUANCE","ISSUEREIM");
	if ($RSISSUEREIM) 
	{
		for($a = 1; $a<$cnt; $a++)
		{
			$appamount	=	$_POST['txtNamount_'.$a];
			$rem		=	$_POST['rem_'.$a];
			$par 		=	$_POST['par_'.$a];
			if(isset($_POST['vat_'.$a]))
			{
				$vatable	=	'YES';
			}
			else 
			{
				$vatable	=	'NO';
			}
			$vattype		=	$_POST['rdovat_'.$a];
			$comp			=	$_POST['comp_'.$a];
			$company		=	explode(',', $comp);
			$comname		=	addslashes(strtoupper($company[0]));
			$comadd			=	strtoupper($company[1]);
			$comtin			=	$company[2];
			$vatamt			=	$_POST['vatamt_'.$a];
			
			$ISSUEREIMDTL	=	"UPDATE ".PCDBASE.".REIMBURSEMENTREQ_DTL SET APRROVEDAMT = '{$appamount}', VATABLE = '{$vatable}', VAT_TYPE = '{$vattype}', 
								 COMP_NAME = '{$comname}', COMP_ADD = '{$comadd}', COMP_TIN = '{$comtin}', VAT_AMT = '{$vatamt}' 
								 WHERE REIM_NO = '{$REIMNO}' AND PURPOSE = '{$par}' AND REMARKS = '{$rem}'";
			$rsISSUEREIMDTL	=	$DATASOURCE->execQUERY($conn_172,$ISSUEREIMDTL,$user,"REIMBURSEMENT ISSUANCE","ISSUEREIM");
		}
	}
	$conn_172->CompleteTrans();
	
	echo "<script>
				$('document').ready(function(){
					MessageType.successMsg('Reimbursement request with transaction number $REIMNO has been successfully issued.');
					$('#divreimbursementdtls').dialog('close');
					getREIMlist();
				});
		  </script>";
	exit();
}
include("issuance.html");
?>