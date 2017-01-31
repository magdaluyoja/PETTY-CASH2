<?php
	session_start();
	include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/config/config.php");
	include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/config/class_db.php");
	$TODAY		=	date("Ymd");
	$seldep		=	$_POST["seldep"];
	$txtfrom	=	$_POST["txtfrom"];
	$txtto		=	$_POST["txtto"];
	$selscope	=	$_POST["selscope"];
	
	if($selscope == "EMP")
	{
		$txtempid	=	$_POST["txtempid"];
		$txtname	=	$_POST["txtname"];
		$year		=	$_POST["selyear"];
		$U_ID		=	$DATASOURCE->selval($conn_172,PCDBASE,"USERS","USERID","USERNAME = '{$txtempid}'");
		
		$USER_Q		=	" AND CH.REQUESTEDBY = '$U_ID'";
		$REQYR_Q	=	" AND DATE_FORMAT(CH.REQUESTEDDT,'%Y') = '$year'";
	}
	else 
	{
		if($seldep != "")
		{
			$seldep_Q	=	" AND U.DEPARTMENT = '{$seldep}'";
			$dep		=	"";
			$seldep		=	"$seldep Department";
		}
		else 
		{
			$seldep_Q	=	"";
			$seldep		=	"All Departments";
		}
		$date_Q		=	" AND CH.REQUESTEDDT BETWEEN '$txtfrom 00:00:00' AND '$txtto 23:59:59'";
	}
	$GETCA	=	"SELECT CH.`CA_NO`, CH.`REQUESTEDBY`, CH.`AMOUNT`, CH.`STATUS`, CH.`ISLIQUIDATED`, DATE_FORMAT(CH.REQUESTEDDT,'%Y') AS YEAR, CH.`RELEASEDDT`,
				 CD.`CA_NO`, CD.`PURPOSE`, CD.`REMARKS`,
				 U.`USERID`, U.`USERNAME`, U.`NAME`, U.`DEPARTMENT`, U.`DEPT_INITIAL`
				 FROM FDCFINANCIALS_PC.CASH_ADVANCE_HDR AS CH
				 LEFT JOIN FDCFINANCIALS_PC.CASH_ADVANCE_DTL AS CD ON CD.CA_NO = CH.CA_NO
				 LEFT JOIN FDCFINANCIALS_PC.USERS AS U ON U.USERID = CH.REQUESTEDBY
				 WHERE CH.STATUS = 'ISSUED' $USER_Q $REQYR_Q $seldep_Q $date_Q
				 ORDER BY CH.REQUESTEDBY DESC";
	$RSGETCA	=	$conn_172->Execute($GETCA);
	if($RSGETCA == false)
	{
		$errmsg	=	($conn_172->ErrorMsg()."::".__LINE__); 
		$DATASOURCE->logError($errmsg,$GETCA,$_SESSION["PC"]["USERNAME"],"CASH ADVANCE REPORTS: DETAILED","CASH ADVANCE REPORTS: DETAILED");
		$DATASOURCE->displayError();
	}
	else 
	{
		$arrCA	=	array();
		if($RSGETCA->RecordCount() > 0 )
		{
			while (!$RSGETCA->EOF){
				$CA_NO			=	$RSGETCA->fields["CA_NO"];
				$NAME 			=	$RSGETCA->fields["NAME"];
				$DEPARTMENT		=	$RSGETCA->fields["DEPARTMENT"];
				$AMOUNT 		=	$RSGETCA->fields["AMOUNT"];
				$YEAR	 		=	$RSGETCA->fields["YEAR"];
				$PURPOSE	 	=	$RSGETCA->fields["PURPOSE"];
				$REMARKS	 	=	$RSGETCA->fields["REMARKS"];
				
				$arrCA[$YEAR][$DEPARTMENT][$NAME][$CA_NO][$PURPOSE]["REMARKS"]		=	$REMARKS;
				$RSGETCA->MoveNext();
			}
			$CSV	=	"FILSTAR DISTRIBUTORS CORPORATION \r";
			$CSV	.=	"CASH ADVANCE REPORT \r \r";
			foreach ($arrCA  as $YEAR=>$val1)
			{
				$CSV	.=	"YEAR : $YEAR \r";
				foreach ($val1 as $DEPARTMENT=>$val2)
				{
					$CSV	.=	"DEPARTMENT : $DEPARTMENT \r";
					foreach ($val2 as $NAME=>$val3)
					{
						$CSV	.=	"NAME : $NAME \r";
						$subtotal_pername	=	0;
						$CSV	.=	"C.A. NUMBER;AMOUNT;DATE REQUESTED;PURPOSE;REMARKS \r";
						foreach ($val3 as $CA_NO=>$val4)	
						{	
							$amount		=	$DATASOURCE->selval($conn_172,PCDBASE,"CASH_ADVANCE_HDR","AMOUNT","CA_NO = '$CA_NO'");
							$datereq	=	$DATASOURCE->selval($conn_172,PCDBASE,"CASH_ADVANCE_HDR","REQUESTEDDT","CA_NO = '$CA_NO'");
							$datereq	=	date("Y-m-d",strtotime($datereq));
//							$CSV		.=	";;; C.A. No. : $CA_NO; AMOUNT : ; $amount; DATE REQUESTED :; '$datereq \r";
							$subtotal_pername	+=	$amount;
							foreach ($val4 as $PURPOSE=>$val5)
							{
								$REMARKS	=	ucwords(strtolower($val5["REMARKS"]));
								$PURDESC	=	ucwords(strtolower($DATASOURCE->selval($conn_172,PCDBASE,"PARTICULARS","PARTICULARDESC","PARTICULARCODE = '$PURPOSE'")));
								$CSV	.=	"$CA_NO;$amount;$datereq;$PURDESC;$REMARKS \r";
//								$CSV	.=	";;;;;; PURPOSE :$PURDESC; REMARKS : $REMARKS \r";
							}
//							$CSV .= "\r";
						}
						$CSV	.=	"TOTAL AMOUNT :; $subtotal_pername \r";
						$CSV .= "\r";
					}
				}
			}
			header("Content-Disposition: attachment; filename=CAReport$TODAY.csv");
			header("Content-Location: $_SERVER[REQUEST_URI]");
			header("Content-Type: text/plain");
			header("Expires: 0");
			echo $CSV;
		}
		else 
		{
			echo "<script>alert('No records found.');</script>";
		}
	}
?>