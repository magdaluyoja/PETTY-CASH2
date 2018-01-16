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
	$fill 	=	"SELECT LI.*,U.NAME, CA.AMOUNT FROM ".PCDBASE.". LIQUIDATION_HDR LI 
				 LEFT JOIN ".PCDBASE.".USERS U ON U.USERID = LI.REQUESTEDBY
				 LEFT JOIN ".PCDBASE.".CASH_ADVANCE_HDR CA ON CA.CA_NO = LI.CA_NO
				 WHERE  LI.STATUS = 'APPROVED' $CA_Q $DATE_Q ORDER BY LI.REQUESTEDDT DESC";
		
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
			$BTNLIQUIDATE 	= 	"<img src='/PETTY_CASH/images/liquidate.png' 	class='smallimgbuttons btnliquidate	tooltips' alt='Liquidate' title='Liquidate'	data-cano='$CA_NO' data-amt='$AMOUNT'>";
							
			$table	.=	"<tr align='center'>
							<td class='ca-dtls-lnk padding tooltips tr-ca-list-dtls' title='Click to view details.' data-cano='$CA_NO'>$CA_NO</td>
							<td class='tr-ca-list-dtls'>$REQUESTEDBY</td>
							<td class='tr-ca-list-dtls'>$REQUESTEDDT</td>
							<td class='tr-ca-list-dtls'>".number_format($AMOUNT,2)."</td>
							<td class='tr-ca-list-dtls'>".number_format($LIQUIDATIONAMT,2)."</td>
							<td class='tr-ca-list-dtls'>".number_format($REIMBURSEMENTAMT,2)."</td>
							<td class='tr-ca-list-dtls'>".number_format($RETURNINGAMT,2)."</td>
							<td class='tr-ca-list-dtls'>$BTNLIQUIDATE</td>
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
if ($action == "LIQUIDATE") 
{
	$CANO		=	$_GET["CANO"];
	$CAAMOUNT	=	$_GET["CAAMOUNT"];
	
	$GETDTLS	=	"SELECT HDR.CA_NO, P.PARTICULARDESC, R.REMARKS, DTL.AMOUNT, DTL.PARTICULAR, DTL.REMARK, CHDR.AMOUNT AS CAAMOUNT FROM ".PCDBASE.".LIQUIDATION_HDR HDR
					 LEFT JOIN ".PCDBASE.".LIQUIDATION_DTL DTL on DTL.CA_NO = HDR.CA_NO
					 LEFT JOIN ".PCDBASE.".PARTICULARS P ON P.PARTICULARCODE = DTL.PARTICULAR
					 LEFT JOIN ".PCDBASE.".REMARKS R ON R.REMARKS_ID = DTL.REMARK
					 LEFT JOIN ".PCDBASE.".CASH_ADVANCE_HDR CHDR ON CHDR.CA_NO  = DTL.CA_NO
					 WHERE HDR.CA_NO = '{$CANO}'";
	$RSGETDTLS	=	$conn_172->Execute($GETDTLS);
	if ($RSGETDTLS == false) 
	{
		$errmsg	=	($conn_172->ErrorMsg()."::".__LINE__); 
		$DATASOURCE->logError($errmsg,$GETDTLS,$_SESSION["PC"]["USERNAME"],"LIQUIDATION LIQUIDATE","LIQUIDATE");
		$DATASOURCE->displayError();
	}
	else 
	{
		$table	=	"<form id='frmliquidationdtls'>
					 <table border='0' class='dialogtable curved5px shadowed' width='100%'>
						<tr>
							<td class='dialogdtltitle padding' colspan='20'>C.A. No: <a  id='tdCANO' class='td-ca-list-title'>$CANO</a> &nbsp; Amount: <a id='tdCAamount'>$CAAMOUNT</a></td>
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
		$partcode	=	$RSGETDTLS->fields['PARTICULAR'];
		$remcode	=	$RSGETDTLS->fields['REMARK'];
		if($partcode != '0005')
		{
			$rem = $rem;
		}else 
		{
			$rem = $remcode;
		}
		$table	.=	"<tr  class='dialogdtldetails'>
						<td>".ucwords(strtolower($part))."</td>
						<td>".ucwords(strtolower($rem))."</td>
						<td class=' amount'>".number_format($amount,2)."</td>
						<td>
							<input type = 'text' id = 'txtNamount_$cnt' name = 'txtNamount_$cnt' value = '$amount' class = 'input_text curved5px amount txtnewamount' size='15'>
						</td>
						<td id='dialogdtldetails$cnt'>
							<input type = 'checkbox' id = 'vat_$cnt' name = 'vat_$cnt' value = '$part' data-curcnt = '$cnt' data-remarks = '$rem' class='chktaxup tooltips pointer'>
							<label for='rdovatG_$cnt'><input type = 'radio' id = 'rdovatG_$cnt' name = 'rdovat_$cnt' value = 'GOODS' 	disabled> Goods</label>
							<label for='rdovatS_$cnt'><input type = 'radio' id = 'rdovatS_$cnt' name = 'rdovat_$cnt' value = 'SERVICES' disabled> Services</label>
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
				<tr class='dialogdtltitle'>
					<td class='bold padding amount'>Reimbursement Amount :</td>
					<td class='bold padding' id = 'Rbamount' colspan='2'>".number_format($RB,2)."</td>
					<td class='bold padding amount'>Returning Amount :</td>
					<td class='bold padding' id = 'Rtamount'>".number_format($RT,2)."</td>
				</tr>
		</table>
		</form>";
		echo $table;
	}
	exit();
}
if($action == "LIQUIDATECA")
{
	
	$CANO 		=	$_GET['CANO'];
	$user		=	$_SESSION["PC"]['NAME'];
	$appamt		=	$_POST['txtTNamount'];
	$reimburse 	= 	$_POST['hidreimburse'];
	$return 	=	$_POST['hidreturn'];
	$vattotamt	=	$_POST['hidvatamt'];
	$cnt		=	$_POST['hidcnt'];
	
	$conn_172->StartTrans();
	$LIQUIDATECA	=	"UPDATE ".PCDBASE.".CASH_ADVANCE_HDR SET ISLIQUIDATED = 'Y', LIQUIDATEDDT = '{$TODAY}' WHERE CA_NO = '{$CANO}'";
	$RSLIQUIDATECA	=	$DATASOURCE->execQUERY($conn_172,$LIQUIDATECA,$user,"LIQUIDATION","LIQUIDATECA");
	if ($RSLIQUIDATECA) 
	{
		$LIQUIDATELI		=	"UPDATE ".PCDBASE.".LIQUIDATION_HDR SET STATUS = 'LIQUIDATED',LIQUIDATEDBY = '{$user}', LIQUIDATEDDT = '{$TODAY}', 
								 APPROVED_AMT = '{$appamt}', VAT_AMT = '{$vattotamt}', REIMBURSEMENT_AMT = '{$reimburse}', RETURNING_AMT = '{$return}' 
								 WHERE CA_NO = '{$CANO}'";
		$RSLIQUIDATELI	=	$DATASOURCE->execQUERY($conn_172,$LIQUIDATELI,$user,"LIQUIDATION","LIQUIDATECA");
		if ($RSLIQUIDATELI) 
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
				
				$LIQUIDATEDTL	=	"UPDATE ".PCDBASE.".LIQUIDATION_DTL SET APRROVEDAMT = '{$appamount}', VATABLE = '{$vatable}', VAT_TYPE = '{$vattype}', 
									 COMP_NAME = '{$comname}', COMP_ADD = '{$comadd}', COMP_TIN = '{$comtin}', VAT_AMT = '{$vatamt}' 
									 WHERE CA_NO = '{$CANO}' AND PARTICULAR = '{$par}' AND REMARK = '{$rem}'";
				$rsliquidateDTL	=	$DATASOURCE->execQUERY($conn_172,$LIQUIDATEDTL,$user,"LIQUIDATION","LIQUIDATECA");
			}
		}
	}
	$conn_172->CompleteTrans();
	
	echo "<script>
				$('document').ready(function(){
					MessageType.successMsg('Cash Advance liquidation request with transaction number $CANO has been successfully liquidated.');
					$('#divliquidationdtls').dialog('close');
					getCAlist();
				});
		  </script>";
	exit();
}
include("liquidation.html");
?>