<?php
session_start();
set_time_limit(0);
$action = $_GET["action"];
include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/config/session.php");
include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/config/config.php");
include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/config/class_db.php");
$TODAY	=	date("Y-m-d H:i:s");

function GETCAVALSFORVALIDATION($DATASOURCE,$conn_172)
{
	$unliquidatedstartdt=	$DATASOURCE->selval($conn_172,PCDBASE,"CASH_ADVANCE_HDR","MIN(REQUESTEDDT)","REQUESTEDBY = '{$_SESSION["PC"]['ID']}' AND REQUESTEDDT != '00-00-00 00:00:00'  
						 	AND (STATUS = 'SUBMITTED' OR STATUS = 'APPROVED' OR STATUS = 'ISSUED')");
	$AGE 				=  	number_format($diff = (abs(strtotime(date('Y-m-d H:i:s')) - (strtotime($unliquidatedstartdt))))/86400, 2);

	$gettodaysamount	=	"SELECT CA_NO,REQUESTEDDT,ISLIQUIDATED,AMOUNT FROM ".PCDBASE.".CASH_ADVANCE_HDR 
							 WHERE REQUESTEDBY = '{$_SESSION["PC"]['ID']}' AND REQUESTEDDT != '00-00-00 00:00:00' 
							 AND (STATUS = 'ISSUED' OR STATUS = 'SUBMITTED' OR STATUS = 'APPROVED')  AND REQUESTEDDT >= '$unliquidatedstartdt'";
	$rsgettodaysamount	=	$conn_172->Execute($gettodaysamount);
	if ($rsgettodaysamount == false) 
	{
		$errmsg	=	($conn_172->ErrorMsg()."::".__LINE__); 
		$DATASOURCE->logError($errmsg,$gettodaysamount,$_SESSION["PC"]["USERNAME"],"CA REQUEST","GETCALIST");
		$DATASOURCE->displayError();
	}
	while (!$rsgettodaysamount->EOF)
	{
		$CA_NO			=	$rsgettodaysamount->fields['CA_NO'];
		$REQUESTEDDT	=	$rsgettodaysamount->fields['REQUESTEDDT'];
		$ISLIQUIDATED 	=	$rsgettodaysamount->fields['ISLIQUIDATED'];
		$UNLIQUIAGE		=	number_format($diff = (abs(strtotime(date('Y-m-d H:i:s')) - (strtotime($REQUESTEDDT))))/86400, 2);
		if($_SESSION['PC']['DEP'] == "WH")
		{
			if($UNLIQUIAGE <= 2)
			{
				$UPTOTODAYSAMOUNT 	+=	$rsgettodaysamount->fields['AMOUNT'];
			}
			else 
			{
				$UPTOTODAYSAMOUNT	=	0;
				$UNLIQUIDATEDAMT	=	1;
				$UNLIQUICAs			.=	",$CA_NO";
			}
		}
		else 
		{
			if($UNLIQUIAGE	<= 1)
			{
				$UPTOTODAYSAMOUNT 	+=	$rsgettodaysamount->fields['AMOUNT'];
			}
			if($ISLIQUIDATED == "N")
			{
				$UNLIQUIDATEDAMT 	+=	$rsgettodaysamount->fields['AMOUNT'];
				$UNLIQUICAs			.=	",$CA_NO";
			}
		}
		$rsgettodaysamount->MoveNext();
	}
	$REQUESTEDDT	=	$DATASOURCE->selval($conn_172,PCDBASE,"CASH_ADVANCE_HDR","REQUESTEDDT","REQUESTEDBY = '{$_SESSION["PC"]['ID']}' AND REQUESTEDDT != '00-00-00 00:00:00'  
					 	AND (STATUS = 'SUBMITTED' OR STATUS = 'APPROVED' OR STATUS = 'ISSUED') AND ISLIQUIDATED = 'Y'");
	$liquidatedage	=	(abs(strtotime(date('Y-m-d H:i:s')) - strtotime($REQUESTEDDT)))/86400;
	
	echo "<script>
			$('#hdnwhdays').val('$UNLIQUIAGE');
			$('#hdnunliquiCAs').val('$UNLIQUICAs');
			$('#hdnuptotodaysamt').val('$UPTOTODAYSAMOUNT');
			$('#hdnunliquidatedamt').val('$UNLIQUIDATEDAMT');
			$('#hdnliquidated4todaycnt').val('$liquidatedage');
		  </script>";
		exit();
}
if($action == "LOADREMARKS")
{
	$particular 		= $_GET['particular'];
	$purposecntcur		= $_GET['purposecntcur'];	
	
	
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
	$tblremarks		=	"<table width='100%' class='dialogtable curved5px shadowed onecol'>
							<tr>
								<td align='center' id='tdparticulartitle'class='dialogdtltitle'></td>
							</tr>
							<tr>
								<td class='dialogdtlheader centered'>Remarks</td>
							</tr>";
	if($particular != '0005')
	{
		while (!$rsgetremark->EOF) 
		{
			$remark_id		=	$rsgetremark->fields['REMARKS_ID'];
			$remark_name	=	ucwords(strtolower($rsgetremark->fields['REMARKS']));
			$tblremarks	.=	"<tr>
								<td class = 'tr-ca-list-dtls bold centerpadding'>
									<label for = '$remark_id'>
										<input type = 'checkbox' id = '$remark_id' name = '$remark_id' class='remarks' data-curcnt='$purposecntcur'>$remark_name
									</label>
								</td>
							</tr>";
			$rsgetremark->MoveNext();
		}
	}
	else
	{
		$tblremarks	.=	"<tr>
							<td class = 'tr-ca-list-dtls'><input type = 'text' id = 'txtmisc' name = 'txtmisc' data-curcnt='$purposecntcur' size='50' class='input_text curved5px'></td>
						</tr>";
		$particulardesc = $particulardesc." (Comma',' Delimited)";
		$particulardesc = addslashes($particulardesc);
	}
	$tblremarks	.=	"</table>";
	$tblremarks	.=	"<script>$('#tdparticulartitle').text('$particulardesc');</script>";
	
	echo $tblremarks;
	if($particular != '0005')
	{
		echo "<script>
				var remarks = $('#hidremark_'+$purposecntcur).val();
					remarks	=	remarks.split(',');
					for(var a = 0; a < remarks.length; a++)
					{
						$('#'+remarks[a]).attr('checked', true);
					}
			</script>";
	}
	else 
	{
		echo "<script>
				$('#txtmisc').val($('#hidremark_'+$purposecntcur).val());
			  </script>";
	}
	exit();
}
if($action == "SAVEREQUEST")
{
	$CANO			=	$DATASOURCE->newTRXno($conn_172,"");
	$reqby			=	$_SESSION["PC"]['ID'];
	$amount			=	$_POST['txtamount'];
	$cnt			=	$_POST['hdnpurposecnt'];
	$requesteddt	=	date('Y-m-d H:i:s');
	$conn_172->StartTrans();
	for ($a = 1; $a <= $cnt; $a++)
	{
		$purpose	=	$_POST['selpurpose_'.$a];
		$remark		=	$_POST['hidremark_'.$a];
		if($purpose == '0005')
		{
			$remark	=	strtoupper(trim($remark));
		}
		if($purpose != undefined and $purpose != "")
		{
			$savedtl	=	"INSERT INTO ".PCDBASE.".CASH_ADVANCE_DTL(CA_NO, PURPOSE, REMARKS) VALUES('{$CANO}', '{$purpose}', '{$remark}')";
			$rssavedtl	=	$DATASOURCE->execQUERY($conn_172,$savedtl,$_SESSION["PC"]["USERNAME"],"CASH ADVANCE REQUEST","SAVEREQUEST");
		}
	}
	
	$save		=	"INSERT INTO ".PCDBASE.".CASH_ADVANCE_HDR(CA_NO, REQUESTEDBY, AMOUNT, STATUS, SAVEDDT)
				 	 VALUES('{$CANO}', '{$reqby}','{$amount}', 'SAVED', '{$requesteddt}')";
	$rssave		=	$DATASOURCE->execQUERY($conn_172,$save,$_SESSION["PC"]["USERNAME"],"CASH ADVANCE REQUEST","SAVEREQUEST");
	if ($rssave) 
	{
		echo "<script>
				$('document').ready(function(){
					MessageType.successMsg('Cash Advance request with transaction number $CANO has been successfully saved.');
					$('#btncancel').trigger('click');
					getCAlist();
				});
			  </script>";
	}
	$conn_172->CompleteTrans();
	exit();
}
if($action == "UPDATEREQUEST")
{
	$CANO			=	$_GET["CANO"];
	$reqby			=	$_SESSION["PC"]['ID'];
	$amount			=	$_POST['txtamount'];
	$cnt			=	$_POST['hdnpurposecnt'];
	$requesteddt	=	date('Y-m-d H:i:s');
	$conn_172->StartTrans();
	$delCAdtls = $DATASOURCE->execQUERY($conn_172,"DELETE FROM ".PCDBASE.".CASH_ADVANCE_DTL WHERE CA_NO = '{$CANO}'",$_SESSION["PC"]["USERNAME"],"CA REQUEST","UPDATEREQUEST");
	if($delCAdtls)
	{
		for ($a = 1; $a <= $cnt; $a++)
		{
			$purpose	=	$_POST['selpurpose_'.$a];
			$remark		=	$_POST['hidremark_'.$a];
			if($purpose == '0005')
			{
				$remark	=	strtoupper(trim($remark));
			}
			if($purpose != undefined and $purpose != "")
			{
				$updatedtl	=	"INSERT INTO ".PCDBASE.".CASH_ADVANCE_DTL(CA_NO, PURPOSE, REMARKS) VALUES('{$CANO}', '{$purpose}', '{$remark}')";
				$rsupdatedtl=	$DATASOURCE->execQUERY($conn_172,$updatedtl,$_SESSION["PC"]["USERNAME"],"CASH ADVANCE REQUEST","UPDATEREQUEST");
			}
		}
		$update		=	"UPDATE ".PCDBASE.".CASH_ADVANCE_HDR SET AMOUNT = '{$amount}'
						 WHERE CA_NO = '{$CANO}'";
		$rsupdate	=	$DATASOURCE->execQUERY($conn_172,$update,$_SESSION["PC"]["USERNAME"],"CASH ADVANCE REQUEST","UPDATEREQUEST");
		$rsupdate	=	$conn_172->Execute($update);
		if ($rsupdate) 
		{
			echo "<script>
					$('document').ready(function(){
						MessageType.successMsg('Cash Advance request with transaction number $CANO has been successfully updated.');
						$('#btncancel').trigger('click');
						getCAlist();
						enablebuttons();
					});
				  </script>";
		}
	}
	$conn_172->CompleteTrans();
	exit();
}
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
		$DATE_Q	=	"AND((APPROVEDDT 	BETWEEN  '{$SDATE}' AND '{$EDATE}') OR 
					 	 (REQUESTEDDT 	BETWEEN  '{$SDATE}' AND '{$EDATE}') OR 
						 (RELEASEDDT 	BETWEEN  '{$SDATE}' AND '{$EDATE}') OR 
						 (SAVEDDT 		BETWEEN  '{$SDATE}' AND '{$EDATE}'))";
	}
	$fill 	=	"SELECT * FROM ".PCDBASE.".CASH_ADVANCE_HDR WHERE REQUESTEDBY = '{$_SESSION['PC']['ID']}' AND STATUS != 'DELETED' $CA_Q $DATE_Q 
				 ORDER BY CA_ID DESC";
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
							<td class='td-ca-list-title curved30px' colspan='10'>Cash Advance Request List</td>
						</tr>
						<tr align='center' class='trl-ca-list-hdr header'>
							<th>C.A. No.</th>
							<th>Amount</th>
							<th>Status</th>
							<th>Requested Date</th>
							<th>Age(Day/s)</th>
							<th>Approved by</th>
							<th>Approved Date</th>
							<th>Date Issued</th>
							<th>Liquidated</th>
							<td>Actions</td>
						</tr>
					</thead>
					<tbody>";
	if($rsfill->RecordCount() == 0)
	{	
		echo $table	.=	"<tr align='center'>
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
			$STATUS			=	$rsfill->fields['STATUS'];
			$APPROVEDBY		=	$rsfill->fields['APPROVEDBY'];
			$ISLIQUIDATED	=	$rsfill->fields['ISLIQUIDATED'];
			$APPROVEDBY		=	$REQUESTEDBY = $DATASOURCE->selval($conn_172,PCDBASE,"USERS","NAME","USERID = '{$APPROVEDBY}'");
			
			if($rsfill->fields['REQUESTEDDT'] != 0){
				$REQUESTEDDT	= date('Y-m-d', strtotime($rsfill->fields['REQUESTEDDT']));
				$age			=	number_format((abs(strtotime(date('Y-m-d H:i:s')) - (strtotime($rsfill->fields['REQUESTEDDT']))))/86400, 3);
			}else {$REQUESTEDDT = "";}
			if($rsfill->fields['APPROVEDDT'] != 0){
					$APPROVEDDT	=	date('Y-m-d', strtotime($rsfill->fields['APPROVEDDT']));
			}else{	$APPROVEDDT = 	"";}
			if($rsfill->fields['RELEASEDDT'] != 0){
					$RELEASEDDT	=	date('Y-m-d', strtotime($rsfill->fields['RELEASEDDT']));
			}else {	$RELEASEDDT = 	"";}
			if($STATUS == "SAVED"){
				$savebtn 	= "<img src='/PETTY_CASH/images/submit_button.png' 	class='smallimgbuttons btnsubmit	tooltips' alt='Submit' 	title='Submit' 	data-cano='$CA_NO' data-caamt = '$AMOUNT'>";
				$editbtn 	= "<img src='/PETTY_CASH/images/edit3.png' 			class='smallimgbuttons btnedit		tooltips' alt='Edit' 	title='Edit'	data-cano='$CA_NO'>";
				$deletebtn 	= "<img src='/PETTY_CASH/images/delete_button.png' 	class='smallimgbuttons btndelete	tooltips' alt='Delete' 	title='Cancel'	data-cano='$CA_NO'>";
			}
			else { $savebtn = $editbtn = $deletebtn =""; }
			$table	.=	"<tr align='center' id='td$CA_NO'>
								<td class='ca-dtls-lnk padding tooltips tr-ca-list-dtls' title='Click to view details.' data-cano='$CA_NO'>$CA_NO</td>
								<td class='tr-ca-list-dtls'>".number_format($AMOUNT, 2)."</td>
								<td class='tr-ca-list-dtls'>$STATUS</td>
								<td class='tr-ca-list-dtls'>$REQUESTEDDT</td>
								<td class='tr-ca-list-dtls'>$age</td>
								<td class='tr-ca-list-dtls'>$APPROVEDBY</td>
								<td class='tr-ca-list-dtls'>$APPROVEDDT</td>
								<td class='tr-ca-list-dtls'>$RELEASEDDT</td>
								<td class='tr-ca-list-dtls'>$ISLIQUIDATED</td>
								<td class='tr-ca-list-dtls'>$savebtn $editbtn $deletebtn</td>
							</tr>";
			$rsfill->MoveNext();
		}
		$table	.=	"</tbody>
				</table>";
		echo $table;
		GETCAVALSFORVALIDATION($DATASOURCE,$conn_172);
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
if($action == "SUBMITCA")
{
	$CANO	=	$_GET["CANO"];
	$conn_172->StartTrans();
		$submitCA	=	"UPDATE ".PCDBASE.".CASH_ADVANCE_HDR SET STATUS = 'SUBMITTED',REQUESTEDDT='{$TODAY}'
						 WHERE CA_NO = '{$CANO}'";
		$rssubmitCA =	$DATASOURCE->execQUERY($conn_172,$submitCA,$_SESSION["PC"]["USERNAME"],"CA REQUEST","SUBMITCA");
		if($rssubmitCA)
		{
			echo "<script>
					$('document').ready(function(){
						MessageType.successMsg('Cash Advance request with transaction number $CANO has been successfully submitted.');
						getCAlist();
					});
				  </script>";
		}
	$conn_172->CompleteTrans();
	exit();
}
if($action == "DELETECA")
{
	$CANO	=	$_GET["CANO"];
	$conn_172->StartTrans();
		$deleteCA	=	"UPDATE ".PCDBASE.".CASH_ADVANCE_HDR SET STATUS = 'CANCELLED'
						 WHERE CA_NO = '{$CANO}'";
		$rsdeleteCA = 	$DATASOURCE->execQUERY($conn_172,$deleteCA,$_SESSION["PC"]["USERNAME"],"CA REQUEST","DELETECA");
		if($rsdeleteCA)
		{
			echo "<script>
					$('document').ready(function(){
						MessageType.successMsg('Cash Advance request with transaction number $CANO has been successfully cancelled.');
						getCAlist();
					});
				  </script>";
		}
	$conn_172->CompleteTrans();
	exit();
}
if($action == "EDITCA")
{
	$CANO	=	$_GET["CANO"];
	$AMOUNT	=	$DATASOURCE->selval($conn_172,PCDBASE,"CASH_ADVANCE_HDR","AMOUNT","CA_NO = '{$CANO}'");
	echo "<script>
			$('#hdncano').val('$CANO');
			$('#txtamount').val('$AMOUNT');
		  </script>";
			
	$getCAdtls	=	"SELECT DTL.*, P.* FROM ".PCDBASE.".CASH_ADVANCE_DTL DTL
					 LEFT JOIN ".PCDBASE.".PARTICULARS P ON P.PARTICULARCODE = DTL.PURPOSE
					 WHERE DTL.CA_NO = '{$CANO}' ORDER BY PARTICULARCODE";
//	echo  $d; exit();
	$rsgetCAdtls	=	$conn_172->Execute($getCAdtls);
	if ($rsgetCAdtls == false) 
	{
		$errmsg	=	($conn_172->ErrorMsg()."::".__LINE__); 
		$DATASOURCE->logError($errmsg,$getCAdtls,$_SESSION["PC"]["USERNAME"],"CA REQUEST","EDITCA");
		$DATASOURCE->displayError();
	}
	$y = 1;
	while (!$rsgetCAdtls->EOF) 
	{
		$particularsval	=	$rsgetCAdtls->fields['PARTICULARCODE'];
		$particulars	=	$rsgetCAdtls->fields['PARTICULARDESC']."(".$rsgetCAdtls->fields['PARTICULARCODE'].")";
		$remarks		=	$rsgetCAdtls->fields['REMARKS'];
		if($y == 1)
		{
			echo "<script> 
					$('#selpurpose_$y').val('$particularsval');
					$('#hidremark_$y').val('$remarks');
				 </script>";	
		}
		else
		{
			 echo "<script>
					$('#btnaddpurpose').trigger('click');
					$('#selpurpose_$y').val('$particularsval');
					$('#hidremark_$y').val('$remarks');
					$('#hdnpurposecnt').val('$y');
				  </script>";
		}
	$y++;
	$rsgetCAdtls->MoveNext();
	}
	exit();
}
include("request.html");
?>