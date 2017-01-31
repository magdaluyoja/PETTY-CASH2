<?php
	include("config.php");
	class db_funcs
	{
		function selval($conn, $dbname, $tblname, $field, $condition)
		{
			$selval		=	"SELECT $field FROM $dbname.$tblname WHERE $condition";
			$RSselval	=	$conn->Execute($selval);
			if($RSselval == false)
			{
				$errmsg	=	($conn->ErrorMsg()."::".__LINE__);
				$this->logError($errmsg,$selval,"");
				$this->displayError();
				exit();
			}
			else 
			{
				$data	=	$RSselval->fields["$field"];
				return $data;
			}
		}
		function getDropDown($conn, $dbname, $tblname, $class, $id, $fields, $value, $text, $condition,$data)
		{
			$seldata	=	"SELECT $fields FROM $dbname.$tblname WHERE $condition";
			$RSseldata	=	$conn->Execute($seldata);
			if($RSseldata == false)
			{
				$errmsg	=	($conn->ErrorMsg()."::".__LINE__); 
				return $this->logError($errmsg,$seldata,$_SESSION['PC']['USERNAME'],'CA REQUEST',"");
				$this->displayError();
				exit();
			}
			else 
			{
				$dropDown	=	"<select id='$id' name='$id' class='$class' $data>
									<option value=''><-- Please Select -->";
				while (!$RSseldata->EOF) 
				{
					$Dvalue	=	$RSseldata->fields["$value"];
					$Dtext	=	$RSseldata->fields["$text"];
					$dropDown	.=	"<option value='$Dvalue'>$Dtext</option>";
					$RSseldata->MoveNext();	
				}
				$dropDown	.=	"</select>";
				return $dropDown;
			}
		}
		
		function execQUERY($conn, $query,$user,$module,$action)
		{
			$RSEXEC_query	=	$conn->Execute($query);
			if ($RSEXEC_query == false) 
			{
				$errmsg	=	$conn->ErrorMsg()."::".__LINE__; 
				$this->logError($errmsg,$query,$user,$module,$action);
				$this->displayError();
				exit();
			}
			else 
			{
				$this->logSuccess($query,$user);
			}
			return $RSEXEC_query;
		}
		function logError($ERRMSG,$QUERY,$user,$module,$action)
		{
			$msg		=	"<========ERROR========>".chr(10);
			$msg		.=	"DATE-TIME:". date("Y-m-d h:i:s").chr(10);
			$msg		.=	"USER:".$user.chr(10);
			$msg		.=	"MODULE:".$module.chr(10);
			$msg		.=	"ACTION:".$action.chr(10);
			$msg		.=	"ERROR MSG:".$ERRMSG.chr(10);
			$msg		.=	"QUERY:".$QUERY.chr(10);
			$msg		.=	"<=========END=========>".chr(10).chr(10);
			
			$filename	=	$_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/logs/ERRORS/error_".date('Ymd').".log";
			
			$fp 	=	 fopen($filename, "a+");
			fwrite($fp, $msg);
			fclose($fp);
		}
		function logSuccess($QUERY,$user)
		{		
			$msg		=	"<========SUCCESSFUL========>".chr(10);
			$msg		.=	"DATE-TIME:". date("Y-m-d h:i:s").chr(10);
			$msg		.=	"USER:".$user.chr(10);
			$msg		.=	"QUERY:".$QUERY.chr(10);
			$msg		.=	"<============END===========>".chr(10).chr(10);
			
			$filename	=	$_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/logs/SUCCESS/success_".date('Ymd').".log";
			
			$fp 	=	 fopen($filename, "a+");
			fwrite($fp, $msg);
			fclose($fp);			
		}
		function alert($data)
		{
			return "<script>alert('$data');</script>";
		}
		function  newTRXno($dbconn,$type)
		{
			if($type == "R")
			{
				$date1		=	$this->selval($dbconn,PCDBASE,"REIMBURSEMENTREQ_HDR","MAX(SAVEDDT)","1");
			 	$lastTRXno 	= 	$this->selval($dbconn,PCDBASE,"REIMBURSEMENTREQ_HDR","REIM_NO","SAVEDDT = '{$date1}'");
			 	 $dgt		=	substr($lastTRXno, 13);	
			}
			else 
			{
				$date1		=	$this->selval($dbconn,PCDBASE,"CASH_ADVANCE_HDR","MAX(SAVEDDT)","1");
			 	$lastTRXno 	= 	$this->selval($dbconn,PCDBASE,"CASH_ADVANCE_HDR","CA_NO","SAVEDDT = '{$date1}'");
			 	 $dgt		=	substr($lastTRXno, 12);	
			}
			 $dep		=	$_SESSION['PC']['DEP'];
			 $newdgt 	= 	$dgt + 1;
			 $lnt 		= 	strlen($newdgt);
			 $date1		=	date("Y-m-d",strtotime($date1));
			 $date2		=	date('Y-m-d');
			 if( $date1==$date2 )
			 {
				if($lnt == 1)
				{
					$newTRXno	=	"$type$dep"."-".date('Ymd').'-'."00".$newdgt;
				}
				if($lnt	==	2)
				{
					$newTRXno	=	"$type$dep"."-".date('Ymd').'-'."0".$newdgt;
				}
				if($lnt	==	3)
				{
					$newTRXno	=	"$type$dep"."-".date('Ymd').'-'.$newdgt;
				}
			 }
			 else 
			 {
			 	$newTRXno	=	"$type$dep"."-".date('Ymd').'-'."001";
			 }
			return $newTRXno;
		}
		function getCAdtlsparticulars($CANO,$conn_172){
			$getdtls	=	"SELECT DTL.*, P.* FROM ".PCDBASE.".CASH_ADVANCE_DTL DTL
					 		 LEFT JOIN ".PCDBASE.".PARTICULARS P ON P.PARTICULARCODE = DTL.PURPOSE 
					 		 WHERE CA_NO = '{$CANO}'";
		//	echo $getdtls; exit();
			$rsgetdtls	=	$conn_172->Execute($getdtls);
			if ($rsgetdtls == false) 
			{
				$errmsg	=	($conn_172->ErrorMsg()."::".__LINE__); 
				$this->logError($errmsg,$getdtls,$_SESSION["PC"]["USERNAME"],"CA","VIEWCADTLS");
				echo "<script>
						$('document').ready(function(){
							MessageType.errorMsg('An error has occured. Unable to process data. Please contact web admin to fix this problem.');
						});
					  </script>";
				exit();
			}
			$table	=	"<table border='0' width='100%' class='dialogtable curved5px shadowed'>
							<tr>
								<td align='center' colspan='2' class='dialogdtltitle'>$CANO</td>
							</tr>
							<tr class='trl-ca-list-hdr'>
								<td class='dialogdtlheader centered'>Purpose</td>
								<td class='dialogdtlheader centered'>Remarks</td>
							</tr>";
			while (!$rsgetdtls->EOF) {
				$part	=	ucwords(strtolower($rsgetdtls->fields['PARTICULARDESC']));
				$rem	=	$rsgetdtls->fields['REMARKS'];
				$remark =	explode(",",$rem);
				$rems	=	"";
				foreach ($remark as $r)
				{
					$x++;
					$remedsc	=	$this->selval($conn_172,PCDBASE,"REMARKS","REMARKS","REMARKS_ID = '{$r}'");
					if($remedsc != "")
					{
						$rems	.= " , ".ucwords(strtolower($remedsc));
					}
					else 
					{
						$rems	.= " , ".$r;
					}
				}
			$table	.=	"<tr>
							<td class='dialogdtldetails padding'>$part</td>
							<td class='dialogdtldetails padding'>".substr($rems,2)."</td>
						</tr>";
			$rsgetdtls->MoveNext();
			}
			$table		.=	"</table>";
			
			return $table;
		}
		function displayError()
		{
			echo "<script>
					$('document').ready(function(){
						MessageType.errorMsg('An error has occured. Unable to process data. Please contact web admin to fix this problem.');
					});
			  	  </script>";
			exit();
		}
		function displaySuccMsg($CANO,$msg)
		{
			echo "<script>
					$('document').ready(function(){
						$('#txtsuccmsg').text('Cash Advance request with transaction number $CANO has been successfully $msg.');
						$('#divsuccmsg').dialog('open');
					});
				  </script>";
		}
		function getREIMdtlsparticulars($TRXNO,$conn_172)
		{
			$TRXNO		=	$_GET["TRXNO"];
			$TRXAMOUNT	=	$this->selval($conn_172,PCDBASE,"REIMBURSEMENTREQ_HDR","AMOUNT","REIM_NO = '{$TRXNO}'");
			
			$GETDTLS	=	"SELECT P.PARTICULARDESC, R.REMARKS, DTL.AMOUNT, DTL.PURPOSE
							 FROM ".PCDBASE.".REIMBURSEMENTREQ_DTL DTL 
							 LEFT JOIN ".PCDBASE.".PARTICULARS P ON P.PARTICULARCODE = DTL.PURPOSE
							 LEFT JOIN ".PCDBASE.".REMARKS R ON R.REMARKS_ID = DTL.REMARKS
							 WHERE DTL.REIM_NO = '{$TRXNO}'";
			$RSGETDTLS	=	$conn_172->Execute($GETDTLS);
			if ($RSGETDTLS == false) 
			{
				$errmsg	=	($conn_172->ErrorMsg()."::".__LINE__); 
				$DATASOURCE->logError($errmsg,$GETDTLS,$_SESSION["PC"]["USERNAME"],"REIMBURSEMENT REQUEST","VIEWREIMBURSEMENTDTLS");
				$DATASOURCE->displayError();
			}
			else 
			{
				$table	=	"<table border='0' class='dialogtable curved5px shadowed' width='100%'>
								<tr>
									<td class='dialogdtltitle' colspan='3'>C.A. No: <a  id='tdCANO' class='td-ca-list-title'>$TRXNO</a> &nbsp; Amount: <a id='tdCAamount'>$TRXAMOUNT</a></td>
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
				$part		=	ucwords(strtolower($RSGETDTLS->fields['PARTICULARDESC']));
				$rem		=	ucwords(strtolower($RSGETDTLS->fields['REMARKS']));
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
				return $table;
			}
		}
	}
	
	
	$DATASOURCE	=	new db_funcs();
?>