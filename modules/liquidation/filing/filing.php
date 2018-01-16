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
		$DATE_Q	=	"AND RELEASEDDT BETWEEN  '{$SDATE}' AND '{$EDATE}'";
	}
	$fill 	=	"SELECT * FROM ".PCDBASE.".CASH_ADVANCE_HDR WHERE REQUESTEDBY = '{$_SESSION['PC']['ID']}' AND STATUS = 'ISSUED' $CA_Q $DATE_Q 
				 ORDER BY SAVEDDT DESC";
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
							<td class='td-ca-list-title curved30px' colspan='10'>Cash Advance Request List</td>
						</tr>
						<tr align='center' class='trl-ca-list-hdr'>
							<th>C.A. No.</th>
							<th>C.A. Amount</th>
							<th>Date Issued</th>
							<th>Age(Day/s)</th>
							<th>Liquidation Amount</th>
							<th>Reimbursement Amount</th>
							<th>Returning Amount</th>
							<th>Requested Date</th>
							<th>Status</th>
							<td>Action</td>
						</tr>
					</thead>
					<tbody>";
	if($rsfill->RecordCount() == 0)
	{	
		echo $table	.=	"<tr align='center' class=''>
							<td class='header no-record tr-ca-list-dtls' colspan='10'>No records found.</td>
						</tr>
					</table>";
		exit();
	}
	else 
	{
		while (!$rsfill->EOF) {
			$CA_NO			=	$rsfill->fields['CA_NO'];
			$AMOUNT			=	$rsfill->fields['AMOUNT'];
			$AGE			=	number_format($diff = (abs(strtotime(date('Y-m-d H:i:s')) - (strtotime($rsfill->fields['RELEASEDDT']))))/86400, 2);
			
			if($rsfill->fields['RELEASEDDT'] != "0000-00-00 00:00:00"){
					$RELEASEDT	= date('Y-m-d', strtotime($rsfill->fields['RELEASEDDT']));
			}else {	$RELEASEDT 	= "";}
			
			$LIQUIDATIONAMT		=	$DATASOURCE->selval($conn_172,PCDBASE,"LIQUIDATION_HDR","LIQUI_AMOUNT","CA_NO = '{$CA_NO}'");
			$REIMBURSEMENTAMT	=	$DATASOURCE->selval($conn_172,PCDBASE,"LIQUIDATION_HDR","REIMBURSEMENT_AMT","CA_NO = '{$CA_NO}'");
			$RETURNINGAMT		=	$DATASOURCE->selval($conn_172,PCDBASE,"LIQUIDATION_HDR","RETURNING_AMT","CA_NO = '{$CA_NO}'");
			$STATUS				=	$DATASOURCE->selval($conn_172,PCDBASE,"LIQUIDATION_HDR","STATUS","CA_NO = '{$CA_NO}'");
			$REQUESTEDDT		=	$DATASOURCE->selval($conn_172,PCDBASE,"LIQUIDATION_HDR","REQUESTEDDT","CA_NO = '{$CA_NO}'");
			if($REQUESTEDDT != ""){
					$REQUESTEDDT	= date('Y-m-d', strtotime($REQUESTEDDT));
			}else {	$REQUESTEDDT 	= "";}
			if($STATUS == ""){
					$BTNLIQUIDATE 	= 	"<img src='/PETTY_CASH/images/liquidate.png' 	class='smallimgbuttons btnliquidate	tooltips' alt='Liquidate' 	title='Liquidate'	data-cano='$CA_NO' data-amt='$AMOUNT'>";
			}else {	$BTNLIQUIDATE	=	"";}
			$table	.=	"<tr align='center'>
							<td class='ca-dtls-lnk padding tooltips tr-ca-list-dtls' title='Click to view details.' data-cano='$CA_NO'>$CA_NO</td>
							<td class='tr-ca-list-dtls'>".number_format($AMOUNT, 2)."</td>
							<td class='tr-ca-list-dtls'>$RELEASEDT</td>
							<td class='tr-ca-list-dtls'>$AGE</td>
							<td class='tr-ca-list-dtls'>".number_format($LIQUIDATIONAMT,2)."</td>
							<td class='tr-ca-list-dtls'>".number_format($REIMBURSEMENTAMT,2)."</td>
							<td class='tr-ca-list-dtls'>".number_format($RETURNINGAMT,2)."</td>
							<td class='tr-ca-list-dtls'>$REQUESTEDDT</td>
							<td class='tr-ca-list-dtls'>$STATUS</td>
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
	
	$GETDTLS	=	"SELECT DTL.*, P.* FROM ".PCDBASE.".CASH_ADVANCE_DTL DTL
					 LEFT JOIN ".PCDBASE.".PARTICULARS P ON P.PARTICULARCODE = DTL.PURPOSE 
					 WHERE CA_NO = '{$CANO}'";
	$RSGETDTLS	=	$conn_172->Execute($GETDTLS);
	if ($RSGETDTLS == false) 
	{
		$errmsg	=	($conn_172->ErrorMsg()."::".__LINE__); 
		$DATASOURCE->logError($errmsg,$GETDTLS,$_SESSION["PC"]["USERNAME"],"LIQUIDATION REQUEST","LIQUIDATE");
		$DATASOURCE->displayError();
	}
	else 
	{
		$table	=	"<form id='frmliquidation'>
					 <table border='0' class='dialogtable shadowed curved5px' width='100%'>
						<tr>
							<td class='dialogdtltitle curved5px centered' colspan='20'>C.A. No: <a  id='tdCANO' class='td-ca-list-title'>$CANO</a> &nbsp; Amount: <a id='tdCAamount'>$CAAMOUNT</a></td>
						</tr>
						<tr class='dialogdtlheader centered'>
							<td class='padding'>Purpose</td>
							<td class='padding'>Remarks</td>
							<td class='padding'>Amount</td>
						</tr>";
	$c		=	1;
	while (!$RSGETDTLS->EOF) 
	{
		$part	=	$RSGETDTLS->fields['PARTICULARDESC'];
		$partid	=	$RSGETDTLS->fields['PURPOSE'];
		$rem	=	$RSGETDTLS->fields['REMARKS'];
		$purp 	= 	$RSGETDTLS->fields['PARTICULARCODE'];
		$remark =	explode(",",$rem);
		
		foreach ($remark as $r)
		{
			if($purp != '0005')
			{
				$GETREM 	=	"SELECT REMARKS FROM ".PCDBASE.".REMARKS WHERE REMARKS_ID = '{$r}'";
				$RSGETREM	=	$conn_172->Execute($GETREM);
				if ($RSGETREM == false) 
				{
					$errmsg	=	($conn_172->ErrorMsg()."::".__LINE__); 
					$DATASOURCE->logError($errmsg,$GETREM,$_SESSION["PC"]["USERNAME"],"LIQUIDATION REQUEST","LIQUIDATE");
					$DATASOURCE->displayError();
				}
				while (!$RSGETREM->EOF) 
				{
					$rem	= $RSGETREM->fields['REMARKS'];
					$table	.=	"<tr>
									<td class='dialogdtldetails'>$part</td>
									<td class='dialogdtldetails'>$rem</td>
									<td class='dialogdtldetails'>
										<input type = 'text' id = 'txtliquiamt$c' name = 'txtliquiamt$c' class = 'input_text curved5px width10 amount txtliquiamt'  placeholder='0.00'>
										<input type = 'hidden' id = 'txtrem_$c' name = 'txtrem_$c' value = '$r'>
										<input type = 'hidden' id = 'txtpar_$c' name = 'txtpar_$c' value = '$partid'>
									</td>
								</tr>";
					$RSGETREM->MoveNext();	
				}
			}
			else 
			{
				$table	.=	"<tr>
								<td class='dialogdtldetails'>$part</td>
								<td class='dialogdtldetails'>$r</td>
								<td class='dialogdtldetails'>
									<input type = 'text' id = 'txtliquiamt$c' name = 'txtliquiamt$c' class = 'input_text curved5px width10 amount txtliquiamt' placeholder='0.00'>
									<input type = 'hidden' id = 'txtrem_$c' name = 'txtrem_$c' value = '$r'>
									<input type = 'hidden' id = 'txtpar_$c' name = 'txtpar_$c' value = '$partid'>
								</td>
							</tr>";
			}
		$c++;
		}
		$RSGETDTLS->MoveNext();
	}
		
		$table	.=	"<tr>
						<td class='dialogdtldetails centered' colspan='2'><b>TOTAL
							<input type='hidden' id='hdncount' name='hdncount' value='$c'>
							<input type='hidden' id='hdnreimamount' name='hdnreimamount'>
							<input type='hidden' id='hdnretamount' name='hdnretamount'>
						</td>
						<td class='dialogdtldetails amount bold' id='totalliquiamount'>0.00</td>
			</table>
			</form>";
		echo $table;
	}
	exit();
}
if($action == "SAVELIQUIDATION")
{
	$CANO		=	$_GET["CANO"];
	$CAAMOUNT	=	$_GET["CAAMOUNT"];
	$CNT		=	$_GET["CNT"];
	$reimAmount	=	$_POST['hdnreimamount'];
	$retAmount	=	$_POST['hdnretamount'];
	$user		=	$_SESSION["PC"]['ID'];
	$totamount	=	0;
	$conn_172->StartTrans();
	for($a = 1; $a < $CNT; $a++)
	{
		$particular	=	$_POST['txtpar_'.$a];
		$remark		=	$_POST['txtrem_'.$a];
		$amount		=	$_POST['txtliquiamt'.$a];
		$totamount	+=	$amount;
		$saveliquidtl		=	"INSERT INTO ".PCDBASE.".LIQUIDATION_DTL(CA_NO, PARTICULAR, REMARK, AMOUNT) 
								 VALUES ('{$CANO}', '{$particular}', '{$remark}', '{$amount}')";
		$rssasveliquidtl	=	$DATASOURCE->execQUERY($conn_172,$saveliquidtl,$_SESSION["PC"]["USERNAME"],"LIQUIDATION REQUEST","SAVELIQUIDATION");
	}
	$saveliquihdr 		= 	"INSERT INTO ".PCDBASE.".LIQUIDATION_HDR (CA_NO, STATUS, REQUESTEDBY, REQUESTEDDT, LIQUI_AMOUNT, REIMBURSEMENT_AMT, RETURNING_AMT) 
					 		 VALUES('{$CANO}', 'SUBMITTED', '{$user}', '{$TODAY}', '{$totamount}', '{$reimAmount}', '{$retAmount}')";
	$rssasveliquihdr	=	$DATASOURCE->execQUERY($conn_172,$saveliquihdr,$_SESSION["PC"]["USERNAME"],"LIQUIDATION REQUEST","SAVELIQUIDATION");
	
	$conn_172->CompleteTrans();
	echo "<script>
			$('document').ready(function(){
				MessageType.successMsg('Cash Advance liquidation request with transaction number $CANO has been successfully submitted.');
				getCAlist();
			});
		  </script>";
	exit();
}
include("filing.html");
?>