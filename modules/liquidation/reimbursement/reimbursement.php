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
		$DATE_Q	=	"AND LI.LIQUIDATEDDT BETWEEN  '{$SDATE}' AND '{$EDATE}'";
	}
	$fill 	=	"SELECT LI.*,U.NAME, CA.AMOUNT FROM ".PCDBASE.".LIQUIDATION_HDR LI 
				 LEFT JOIN ".PCDBASE.".USERS U ON U.USERID = LI.REQUESTEDBY
				 LEFT JOIN ".PCDBASE.".CASH_ADVANCE_HDR CA ON CA.CA_NO = LI.CA_NO
				 WHERE  (LI.STATUS = 'LIQUIDATED') AND REIMBURSEMENT_AMT  > 0 $CA_Q $DATE_Q 
				 ORDER BY LI.REQUESTEDDT DESC";
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
						<tr align='center' class='trl-ca-list-hdr header'>
							<th class='padding'>C.A. No.</th>
							<th class='padding'>Requested by</th>
							<th class='padding'>C.A. Amount</th>
							<th class='padding'>Filed Liquidation<br>Amount</th>
							<th class='padding'>Approved <br>Amount</th>
							<th class='padding'>Reimbursement <br>Amount</th>
							<th class='padding'>Liquidated Date</th>
							<th class='padding'>Age(Day/s)</th>
							<th class='padding'>Status</th>
							<td class='padding'>Action</td>
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
			$CA_NO		=	$rsfill->fields['CA_NO'];
			$AMOUNT		=	$rsfill->fields['AMOUNT'];
			$REQUESTEDBY=	$rsfill->fields['REQUESTEDBY'];
			$REQUESTEDBY = $DATASOURCE->selval($conn_172,PCDBASE,"USERS","NAME","USERID = '{$REQUESTEDBY}'");
			if($rsfill->fields['REQUESTEDDT'] != "0000-00-00 00:00:00"){
				$REQUESTEDDT	= date('Y-m-d', strtotime($rsfill->fields['REQUESTEDDT']));
			}else {$REQUESTEDDT = "";}
			
			$LIQUIDATIONAMT		=	$rsfill->fields["LIQUI_AMOUNT"];
			$APPROVED_AMT 		=	$rsfill->fields["APPROVED_AMT"];
			$REIMBURSEMENTAMT	=	$rsfill->fields["REIMBURSEMENT_AMT"];
			$RETURNINGAMT		=	$rsfill->fields["RETURNING_AMT"];
			$STATUS				=	$rsfill->fields["STATUS"];
			$LIQUIDATEDDT 		=	$rsfill->fields["LIQUIDATEDDT"];
			if($LIQUIDATEDDT != ""){
					$LIQUIDATEDDT	= date('Y-m-d', strtotime($LIQUIDATEDDT));
			}else {	$LIQUIDATEDDT 	= "";}
			$age		=	number_format((abs(strtotime(date('Y-m-d H:i:s')) - (strtotime($LIQUIDATEDDT))))/86400, 2);
			
			$BTNREIMBURSE 	= 	"<img src='/PETTY_CASH/images/liquidate.png' class='smallimgbuttons btnreimburse	tooltips' alt='Reimburse'	title='Reimburse'	data-cano='$CA_NO' data-reimamt='$REIMBURSEMENTAMT'>";
							
			$table	.=	"<tr align='center'>
							<td class='ca-dtls-lnk padding tooltips tr-ca-list-dtls' title='Click to view details.' data-cano='$CA_NO'>$CA_NO</td>
							<td class='tr-ca-list-dtls'>$REQUESTEDBY</td>
							<td class='tr-ca-list-dtls'>".number_format($AMOUNT,2)."</td>
							<td class='tr-ca-list-dtls'>".number_format($LIQUIDATIONAMT,2)."</td>
							<td class='tr-ca-list-dtls'>".number_format($APPROVED_AMT,2)."</td>
							<td class='tr-ca-list-dtls'>".number_format($REIMBURSEMENTAMT,2)."</td>
							<td class='tr-ca-list-dtls'>$LIQUIDATEDDT</td>
							<td class='tr-ca-list-dtls'>$age</td>
							<td class='tr-ca-list-dtls'>$STATUS</td>
							<td class='tr-ca-list-dtls'>$BTNREIMBURSE</td>
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
if($action == "LOADREMARKS")
{
	$particular 	= $_GET['particular'];
	$purposecntcur	= $_GET['purposecntcur'];	
	$BTNCLICKED		= $_GET["BTNCLICKED"];
	
	$getremark		=	"SELECT	R.*, P.* FROM  ".PCDBASE.".REMARKS R
						 LEFT JOIN  ".PCDBASE.".PARTICULARS P ON P.PARTICULARCODE = R.PARTICULARCODE
						 WHERE R.PARTICULARCODE = '{$particular}'
						 ORDER BY R.REMARKS";
	$rsgetremark	=	$conn_172->Execute($getremark);
	if ($rsgetremark == false) 
	{
		$errmsg	=	($conn_172->ErrorMsg()."::".__LINE__); 
		$DATASOURCE->logError($errmsg,$getremark,$_SESSION["PC"]["USERNAME"],"CA REQUEST","LOADREMARKS");
		$DATASOURCE->displayError();
	}
	$particulardesc	=	$DATASOURCE->selval($conn_172,PCDBASE," PARTICULARS","PARTICULARDESC","PARTICULARCODE = '{$particular}'");
	$cnt = 1;
	$tblremarks		=	"<form id='frmremarkamt'>
						 <table width='100%' class='dialogtable curved5px shadowed'>
							<tr>
								<td align='center' id='tdparticulartitle'class='dialogdtltitle'colspan='2'></td>
							</tr>
							<tr>
								<td class='dialogdtlheader centered'>Remarks</td>
								<td class='dialogdtlheader centered' width='15px'>Amount</td>
							</tr>";
	if($particular != '0005')
	{
		
		while (!$rsgetremark->EOF) 
		{
			$remark_id		=	$rsgetremark->fields['REMARKS_ID'];
			$remark_name	=	ucwords(strtolower($rsgetremark->fields['REMARKS']));
			
			$tblremarks	.=	"<tr>
								<td class = 'tr-ca-list-dtls  bold'>$remark_name</td>
								<td class = 'tr-ca-list-dtls  bold amount'>
									<input type = 'text' id = 'val_$cnt' name = 'val_$cnt' class='input_text curved5px amount remarksamt' size='15'>
									<input type = 'hidden' id = 'name_$cnt' name = 'name_$cnt' value = '$remark_id'>
								</td>
							</tr>";
			
			$cnt++;
			$rsgetremark->MoveNext();
		}
		if($BTNCLICKED == "btnview")
		{
			$tblremarks	.=	"<script>
								var values 	= $('#hidval_'+$purposecntcur).val();
								var b		= 1;
								values		= values.split(',');
								for(var a = 0; a < values.length; a++)
								{
									$('#val_'+b).val(values[a]);
									b++;
								}
							 </script>";
		}
	}
	else
	{
		
		$tblremarks	.="	<tr>
							<td class = 'tr-ca-list-dtls  bold'>
								<input type = 'text' id = 'name_1' name = 'name_1' size = '40' placeholder='Miscellaneous Description'>
							</td>
							<td class = 'tr-ca-list-dtls amount'>
								<input type = 'text' id = 'val_1' name = 'val_1' class='input_text curved5px amount remarksamt' size='15'>
							</td>
						</tr>";
		if($BTNCLICKED == "btnview")
		{
			$tblremarks	.="<script>
								$('#val_1').val($('#hidval_'+$purposecntcur).val());
								$('#name_1').val($('#hidname_'+$purposecntcur).val());
						   </script>";
		}
		$cnt++;
	}
	$tblremarks	.=	"<tr>
						<td class = 'tr-ca-list-dtls bold centered'>Total Amount</td>
						<td class = 'tr-ca-list-dtls amount  bold'>
							<input type = 'text' id = 'Ptotal' name = 'Ptotal' readonly size='15'class='input_text curved5px amount'>
							<input type='hidden' id='hdnremcnt' name='hdnremcnt' value='$cnt'>
						</td>
					 </tr>";
	$tblremarks	.=	"</table>
					</form>";
	$tblremarks	.=	"<script>$('#tdparticulartitle').text('$particulardesc');</script>";
	
	echo $tblremarks;
	if($BTNCLICKED == "btnview")
	{
		 echo "<script>
					sumupremarksamt('');
			  </script>";
	}
	exit();
}
if($action == "SAVEREIMBURSEMENT")
{
	$CANO		=	$_GET["CANO"];
	$REIMamt	=	$_GET['REIM'];
	$reimdate	=	date('Y-m-d h:i:s');
	$reimby		=	$_SESSION["PC"]['NAME'];
	$cnt		=	$_POST["hdnpurposecnt"];
	
	$conn_172->StartTrans();
	$SAVEREIMBURSEMENT	=	"INSERT INTO ".PCDBASE.".REIMBURSEMENTCA_HDR (CA_NO, REIMBURSEMENTDT, REIMBURSED_BY, REIMBURSEMENT_AMT) 
							 VALUES('{$CANO}', '{$reimdate}', '{$reimby}', '{$REIMamt}')";
	$RSSAVEREIMBURSEMENT=	$DATASOURCE->execQUERY($conn_172,$SAVEREIMBURSEMENT,$_SESSION["PC"]["USERNAME"],"LIQUIDATION REIMBURSEMENT","SAVEREIMBURSEMENT");
	for($b = 1; $b <= $cnt; $b++)
	{
		$selpurpose	=	$_POST['selpurpose_'.$b];
		$value		=	$_POST['hidval_'.$b];
		$name		=	$_POST['hidname_'.$b];
		$value		=	explode(',',$value);
		$name		=	explode(',',$name);
		if($value != undefined and $value != "")
		{
			$arrlength	=	count($value);
			for($a = 0; $a < $arrlength; $a++)
			{
				if($value[$a] != 0)
				{
					$remname	=	$name[$a];
					$remval	=	$value[$a];
					$REIMDTL	=	"INSERT INTO ".PCDBASE.".REIMBURSEMENTCA_DTL (CA_NO,PURPOSE, REMARK_NO, AMOUNT) 
									 VALUES('{$CANO}','{$selpurpose}', '{$remname}', '{$remval}')";
					$RSREIMDTL	=	$DATASOURCE->execQUERY($conn_172,$REIMDTL,$_SESSION["PC"]["USERNAME"],"LIQUIDATION REIMBURSEMENT","SAVEREIMBURSEMENT");
				}
			}
		}
	}
	$REIM	=	"UPDATE ".PCDBASE.".LIQUIDATION_HDR  SET  REIMBURSEMENT_AMT = 0 WHERE CA_NO = '{$CANO}'";
	$RSREIM	=	$DATASOURCE->execQUERY($conn_172,$REIM,$_SESSION["PC"]["USERNAME"],"LIQUIDATION REIMBURSEMENT","SAVEREIMBURSEMENT");
	
	$conn_172->CompleteTrans();
	echo "<script>
			$('document').ready(function(){
				MessageType.successMsg('Cash Advance liquidation reimbursement with transaction number $CANO has been successfully saved.');
				getCAlist();
			});
	  	 </script>";
	exit();
}
include("reimbursement.html");
?>