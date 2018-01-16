<?php
session_start();
set_time_limit(0);
$action = $_GET["action"];
include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/config/session.php");
include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/config/config.php");
include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/config/class_db.php");
$TODAY	=	date("Y-m-d h:i:s");

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
	$trxno			=	$DATASOURCE->newTRXno($conn_172,"R");
	$cnt			=	$_POST["hdnpurposecnt"];
	$saveddate		=	date('Y-m-d h:i:s');
	$savedby		=	$_SESSION["PC"]['ID'];
	$totamount		=	$_GET["TOTALAMOUNT"];
	
	$conn_172->StartTrans();
	$REIMHDR	=	"INSERT INTO ".PCDBASE.".REIMBURSEMENTREQ_HDR(REIM_NO, REQUESTEDBY, AMOUNT, STATUS, SAVEDDT) 
					 VALUES('{$trxno}','{$savedby}','{$totamount}','SAVED', '{$saveddate}')";
	$RSREIMHDR	=	$DATASOURCE->execQUERY($conn_172,$REIMHDR,$_SESSION["PC"]["USERNAME"],"REIMBURSEMENT REQUEST","SAVEREIMBURSEMENT");
	for($b = 1; $b <= $cnt; $b++)
	{
		$purpose	=	$_POST['selpurpose_'.$b];
		$values		=	$_POST['hidval_'.$b];
		$names		=	$_POST['hidname_'.$b];
		if($values != undefined and $values != "")
		{
			if($purpose != '0005')
			{
				$value		=	explode(',',$values);
				$name		=	explode(',',$names);
			}
			else 
			{
				$values		=	strtoupper($values);
				$names		=	strtoupper($names);
				$value 		= 	array($values);
				$name		=	array($names);
							}
			$arrlength	=	count($value);
			for($a = 0; $a < $arrlength; $a++)
			{
				if($value[$a] != 0)
				{
					$remname	=	$name[$a];
					$remval		=	$value[$a];
					$REIMDTL	=	"INSERT INTO ".PCDBASE.".REIMBURSEMENTREQ_DTL (REIM_NO, PURPOSE, REMARKS,AMOUNT) VALUES('{$trxno}', '{$purpose}', '{$remname}', '{$remval}')";
					$RSREIMDTL	=	$DATASOURCE->execQUERY($conn_172,$REIMDTL,$_SESSION["PC"]["USERNAME"],"REIMBURSEMENT REQUEST","SAVEREIMBURSEMENT");
				}
			}
		}
	}
	$conn_172->CompleteTrans();
	echo "<script>
			$('document').ready(function(){
				MessageType.successMsg('Reimbursement request with transaction number $trxno has been successfully saved.');
				getREIMlist();
			});
	  	 </script>";
	exit();
}
if($action == "UPDATEREIMBURSEMENT")
{
	$trxno			=	$_GET["LBLREIMNUMBER"];
	$cnt			=	$_POST["hdnpurposecnt"];
	$saveddate		=	date('Y-m-d h:i:s');
	$savedby		=	$_SESSION["PC"]['ID'];
	$totamount		=	$_GET["TOTALAMOUNT"];
	
	$conn_172->StartTrans();
	$DELREIM	=	"DELETE FROM ".PCDBASE.".REIMBURSEMENTREQ_DTL WHERE REIM_NO = '{$trxno}'";
	$RSDELREIM	=	$DATASOURCE->execQUERY($conn_172,$DELREIM,$_SESSION["PC"]["USERNAME"],"REIMBURSEMENT REQUEST","UPDATEREIMBURSEMENT");
	
	$REIMHDR	=	"UPDATE ".PCDBASE.".REIMBURSEMENTREQ_HDR SET AMOUNT = '{$totamount}' WHERE REIM_NO = '{$trxno}'";
	$RSREIMHDR	=	$DATASOURCE->execQUERY($conn_172,$REIMHDR,$_SESSION["PC"]["USERNAME"],"REIMBURSEMENT REQUEST","UPDATEREIMBURSEMENT");
	for($b = 1; $b <= $cnt; $b++)
	{
		$purpose	=	$_POST['selpurpose_'.$b];
		$values		=	$_POST['hidval_'.$b];
		$names		=	$_POST['hidname_'.$b];
		if($values != undefined and $values != "")
		{
			if($purpose != '0005')
			{
				$value		=	explode(',',$values);
				$name		=	explode(',',$names);
			}
			else 
			{
				$values		=	strtoupper($values);
				$names		=	strtoupper($names);
				$value 		= 	array($values);
				$name		=	array($names);
							}
			$arrlength	=	count($value);
			for($a = 0; $a < $arrlength; $a++)
			{
				if($value[$a] != 0)
				{
					$remname	=	$name[$a];
					$remval		=	$value[$a];
					$REIMDTL	=	"INSERT INTO ".PCDBASE.".REIMBURSEMENTREQ_DTL (REIM_NO, PURPOSE, REMARKS,AMOUNT) VALUES('{$trxno}', '{$purpose}', '{$remname}', '{$remval}')";
					$RSREIMDTL	=	$DATASOURCE->execQUERY($conn_172,$REIMDTL,$_SESSION["PC"]["USERNAME"],"REIMBURSEMENT REQUEST","SAVEREIMBURSEMENT");
				}
			}
		}
	}
	$conn_172->CompleteTrans();
	echo "<script>
			$('document').ready(function(){
				MessageType.successMsg('Reimbursement request with transaction number $trxno has been successfully updated.');
				getREIMlist();
			});
	  	 </script>";
	exit();
}
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
				 WHERE REQUESTEDBY = '{$_SESSION["PC"]['ID']}' $REIM_Q $DATE_Q
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
							<th class='padding'>Issued Date</th>
							<td class='padding'>Action</td>
						</tr>
					</thead>
					<tbody>";
	if($rsfill->RecordCount() == 0)
	{	
		echo $table	.=	"<tr align='center' class='tr-ca-list-dtls'>
							<td class='header no-record' colspan='8'>No records found.</td>
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
			if($REQUESTEDDT != "0000-00-00 00:00:00"){
				$REQUESTEDDT	= date('Y-m-d', strtotime($REQUESTEDDT));
				$age		=	number_format($diff = (abs(strtotime(date('Y-m-d H:i:s')) - (strtotime($REQUESTEDDT))))/86400, 2);
			}else {$REQUESTEDDT = "";}
			if($rsfill->fields['APPROVEDDT'] != "0000-00-00 00:00:00"){
				$APPROVEDDT	= date('Y-m-d', strtotime($rsfill->fields['APPROVEDDT']));
				$age		=	"";
			}else {$APPROVEDDT = "";}
			if($rsfill->fields['RELEASEDDT'] != "0000-00-00 00:00:00"){
				$RELEASEDDT	= date('Y-m-d', strtotime($rsfill->fields['RELEASEDDT']));
			}else {$RELEASEDDT = "";}
			if($STATUS == "SAVED"){
				$savebtn 	= "<img src='/PETTY_CASH/images/submit_button.png' 	class='smallimgbuttons btnsubmit	tooltips' alt='Submit' 	title='Submit' 	data-reimno='$REIM_NO' data-reimamt = '$AMOUNT'>";
				$editbtn 	= "<img src='/PETTY_CASH/images/edit3.png' 			class='smallimgbuttons btnedit		tooltips' alt='Edit' 	title='Edit'	data-reimno='$REIM_NO' data-reimamt = '$AMOUNT'>";
				$deletebtn 	= "<img src='/PETTY_CASH/images/delete_button.png' 	class='smallimgbuttons btndelete	tooltips' alt='Delete' 	title='Cancel'	data-reimno='$REIM_NO'>";
			}	
			else { $savebtn = $editbtn = $deletebtn =""; }		
			$table	.=	"<tr align='center' class='tr-ca-list-dtls'>
							<td class='ca-dtls-lnk padding tooltips' title='Click to view details.' data-trxno='$REIM_NO'>$REIM_NO</td>
							<td align='right'>".number_format($AMOUNT,2)."</td>
							<td>$REQUESTEDDT</td>
							<td>$age</td>
							<td>$STATUS</td>
							<td>$APPROVEDDT</td>
							<td>$RELEASEDDT</td>
							<td>$savebtn $editbtn $deletebtn</td>
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
if($action == "SUBMITREIM")
{
	$REIM_NO	=	$_GET["REIMNO"];
	$conn_172->StartTrans();
		$submitREIM	=	"UPDATE ".PCDBASE.".REIMBURSEMENTREQ_HDR SET STATUS = 'SUBMITTED',REQUESTEDDT='{$TODAY}'
						 WHERE REIM_NO = '{$REIM_NO}'";
		$rssubmitREIM =	$DATASOURCE->execQUERY($conn_172,$submitREIM,$_SESSION["PC"]["USERNAME"],"REIMBURSEMENT REQUEST","SUBMITREIM");
		if($rssubmitREIM)
		{
			echo "<script>
					$('document').ready(function(){
						MessageType.successMsg('Reimbursement request with TRX No: $REIM_NO has been successfully submitted.');
						getREIMlist();
					});
				  </script>";
		}
	$conn_172->CompleteTrans();
	exit();
}
if($action == "DELETEREIM")
{
	$REIMNO	=	$_GET["REIMNO"];
	$conn_172->StartTrans();
		$deleteREIM	=	"UPDATE ".PCDBASE.".REIMBURSEMENTREQ_HDR SET STATUS = 'CANCELLED'
						 WHERE REIM_NO = '{$REIMNO}'";
		$rsdeleteREIM = 	$DATASOURCE->execQUERY($conn_172,$deleteREIM,$_SESSION["PC"]["USERNAME"],"REIMBURSEMENT REQUEST","DELETEREIM");
		if($rsdeleteREIM)
		{
			echo "<script>
					$('document').ready(function(){
						MessageType.successMsg('Reimbursment request with TRX No: $REIMNO has been successfully cancelled.');
						getREIMlist();
					});
				  </script>";
		}
	$conn_172->CompleteTrans();
	exit();
}
if($action == "EDITREIM")
{
	$REIMNO		=	$_GET["REIMNO"];	
	$EDITREIM	=	"SELECT dtl.*, p.* FROM ".PCDBASE.".REIMBURSEMENTREQ_DTL dtl
					 LEFT JOIN ".PCDBASE.".PARTICULARS p ON p.PARTICULARCODE = dtl.PURPOSE
					 WHERE dtl.REIM_NO = '{$REIMNO}' ORDER BY PARTICULARCODE";
	$RSEDITREIM		=	$conn_172->Execute($EDITREIM);
	if ($RSEDITREIM == false) 
	{
		$errmsg	=	($conn_172->ErrorMsg()."::".__LINE__); 
		$DATASOURCE->logError($errmsg,$fill,$_SESSION["PC"]["USERNAME"],"REIMBURSEMENT REQUEST","EDITREIM");
		$DATASOURCE->displayError();
	}
	else 
	{
		$aData	=	array();
		$y = 0;
		while (!$RSEDITREIM->EOF) 
		{
			$particularsval	=	$RSEDITREIM->fields['PARTICULARCODE'];
			$particulars	=	$RSEDITREIM->fields['PARTICULARDESC'].'-'.$RSEDITREIM->fields['PARTICULARCODE'];
			$val			=	$RSEDITREIM->fields['AMOUNT'];
			$name			=	$RSEDITREIM->fields['REMARKS'];
			if($y == 0)
			{
				$remnames	=	$name;
				$remvals	=	$val;
			}
			else 
			{
				if($oldpart == $particularsval)
				{
					$remnames	=	$name.','.$remnames;
					$remvals	=	$val.','.$remvals;
				}
				else 
				{
					$remnames	=	'';
					$remvals	=	'';
					$remnames	=	$name.','.$remnames;
					$remvals	=	$val.','.$remvals;
				}
			}
			$oldpart = $particularsval;
			$aData[$particularsval]['particulars'] 	= $particulars;
			$aData[$particularsval]['remname']		= $remnames;
			$aData[$particularsval]['remval'] 		= $remvals;
		$y++;	
		$RSEDITREIM->MoveNext();
		}
		$x = 1;
		foreach ($aData as $part=>$val1)
		{
			$remname	=	$val1['remname'];
			$remval		=	$val1['remval'];
			if($x == 1)
			{
				 echo "<script>
						$('#selpurpose_1').val('$part');
						$('#hidval_1').val('$remval');
						$('#hidname_1').val('$remname');
					  </script>";
			}
			else 
			{
				 echo "<script>
						$('#btnaddpurpose').click();
						$('#selpurpose_$x').val('$part');
						$('#hidval_$x').val('$remval');
						$('#hidname_$x').val('$remname');
					  </script>";
			}
		$x++;
		}
	}
	exit();
}
include("filing.html");
?>