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
		$DATE_Q	=	"AND APPROVEDDT BETWEEN  '{$SDATE}' AND '{$EDATE}'";
	}
	$fill 	=	"SELECT * FROM ".PCDBASE.".CASH_ADVANCE_HDR WHERE REQUESTEDBY = '{$_SESSION['PC']['ID']}' AND STATUS = 'APPROVED' $CA_Q $DATE_Q 
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
							<td class='td-ca-list-title curved30px' colspan='6'>Cash Advance Request List</td>
						</tr>
						<tr align='center' class='trl-ca-list-hdr header'>
							<th>C.A. No.</th>
							<th>Amount</th>
							<th>Status</th>
							<th>Requested By</th>
							<th>Approved Date</th>
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
			$CA_NO		=	$rsfill->fields['CA_NO'];
			$AMOUNT		=	$rsfill->fields['AMOUNT'];
			$REQUESTEDBY=	$rsfill->fields['REQUESTEDBY'];
			$STATUS		=	$rsfill->fields['STATUS'];
			$ISLIQUIDATED=	$rsfill->fields['ISLIQUIDATED'];
			$REQUESTEDBY = $DATASOURCE->selval($conn_172,PCDBASE,"USERS","NAME","USERID = '{$REQUESTEDBY}'");
			if($rsfill->fields['APPROVEDDT'] != 0){
				$APPROVEDDT	= date('Y-m-d', strtotime($rsfill->fields['APPROVEDDT']));
			}else {$APPROVEDDT = "";}
			if($rsfill->fields['APPROVEDDT'] != 0 and $rsfill->fields['STATUS']  == 'APPROVED'){
				$age	=	number_format($diff = (abs(strtotime(date('Y-m-d H:i:s')) - (strtotime($rsfill->fields['APPROVEDDT']))))/86400, 2);
			}
			$approvebtn	 	= "<img src='/PETTY_CASH/images/details_button.png' class='smallimgbuttons btndenomination 	tooltips' 	alt='Denomination Details' 	title='Denomination Details'	data-cano='$CA_NO'	data-caamt='$AMOUNT'>";
			$disapprovebtn 	= "<img src='/PETTY_CASH/images/release_button2.png'class='smallimgbuttons btnissue			tooltips' 	alt='Issue' 				title='Issue'					data-cano='$CA_NO'>";
			$table	.=	"<tr align='center'>
							<td class='ca-dtls-lnk padding tooltips tr-ca-list-dtls' title='Click to view details.' data-cano='$CA_NO'>$CA_NO</td>
							<td class='tr-ca-list-dtls'>".number_format($AMOUNT, 2)."</td>
							<td class='tr-ca-list-dtls'>$STATUS</td>
							<td class='tr-ca-list-dtls'>$REQUESTEDBY</td>
							<td class='tr-ca-list-dtls'>$APPROVEDDT</td>
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
if($action == "SAVEDENOMINATION")
{
	$CANO		=	$_GET["CANO"];
	$conn_172->StartTrans();
	for ($a=1; $a <= 7; $a++)
	{
		$textname	=	'txtpcs'.$a;
	 	$pieces	=	$_POST['txtpcs'.$a];
	 	
	 	if($textname == 'txtpcs1' and $pieces != ''){$denomination = '1000';}
	 	if($textname == 'txtpcs2' and $pieces != ''){$denomination = '500';}
	 	if($textname == 'txtpcs3' and $pieces != ''){$denomination = '200';}
	 	if($textname == 'txtpcs4' and $pieces != ''){$denomination = '100';}
	 	if($textname == 'txtpcs5' and $pieces != ''){$denomination = '50';}
	 	if($textname == 'txtpcs6' and $pieces != ''){$denomination = '20';}
	 	if($textname == 'txtpcs7' and $pieces != ''){$denomination = 'Coins';}
	 	if($pieces != "")
	 	{
		 	$SAVEDENOMINATION		=	"INSERT INTO ".PCDBASE.".CASH_ADVANCE_DENOMINATION (CA_NO, DENOMINATION, PCS, ADDEDDT) 
		 								 VALUES('{$CANO}', '{$denomination}', '{$pieces}', '{$TODAY}')";
		 	$RSSAVEDENOMINATION		=	 $DATASOURCE->execQUERY($conn_172,$SAVEDENOMINATION,$_SESSION["PC"]["USERNAME"],"CA ISSUANCE","SAVEDENOMINATION");
	 	}
	}
		$ISSUECA		=	"UPDATE ".PCDBASE.".CASH_ADVANCE_HDR SET STATUS = 'ISSUED', RELEASEDBY = '{$_SESSION['PC']['NAME']}',  RELEASEDDT = '{$TODAY}' where CA_NO = '{$CANO}'";
		$RSISSUECA		=	$DATASOURCE->execQUERY($conn_172,$ISSUECA,$_SESSION["PC"]["USERNAME"],"CA ISSUANCE","SAVEDENOMINATION");
//		if ($RSISSUECA == false) 
//		{
//			$ftp_conn	=	ftp_connect(FTPD_IP);
//			$x			=	ftp_login($ftp_conn,FTPD_USER,FTPD_PASS);
//			if ($x == false) 
//			{
//				echo "<script>
//						$('document').ready(function(){
//							$('#txterrmsg').text('No connection to printer.');
//							$('#diverrmsg').dialog('open');
//							getCAlist();
//						});
//					  </script>";
//				exit();
//			}
//			else 
//			{
//				//echo 'yes';
//					//file_get_contents("http://192.168.250.57/PETTY_CASH_PCV/Autoprinting.php?&CAno={$CAno}&NAME=$name");
//					file_get_contents("http://".FTPD_IP."/PETTY_CASH_PCV/Autoprinting.php?&CAno={$CAno}");
//					echo "<script>location.reload();</script>";
//			}
//		}
	$conn_172->CompleteTrans();
	echo "<script>
			$('document').ready(function(){
				MessageType.successMsg('Cash Advance Request with C.A. number $CANO has been successfully issued.');
				$('#divdenomination').dialog('close');
				getCAlist();
			});
		  </script>";
	exit();
}
include("issuance.html");
?>