<?php
	include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/js/jsUI.php");
	include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/config/config.php");
	include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/config/class_db.php");
	include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/fpdf/fpdf.php");

	$seldep		=	$_POST["seldep"];
	$txtfrom	=	$_POST["txtfrom"];
	$txtto		=	$_POST["txtto"];
	$chart		=	$_POST["selchart"];
	$selscope	=	$_POST["selscope"];
	if($selscope == "EMP")
	{
		include("employee_charts.php");
		exit();
	}
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
		$CALIST	=	"SELECT C.`REQUESTEDBY`, SUM(C.`AMOUNT`) AS AMOUNT, U.USERNAME, U.NAME,U.DEPARTMENT FROM  FDCFINANCIALS_PC.CASH_ADVANCE_HDR AS C
					 LEFT JOIN FDCFINANCIALS_PC.USERS AS U ON U.USERID = C.REQUESTEDBY
					 WHERE C.STATUS = 'ISSUED' AND RELEASEDDT BETWEEN '{$txtfrom} 00:00:00' AND '{$txtto} 23:59:59' $seldep_Q
					 GROUP BY C.`REQUESTEDBY`
					 ORDER BY AMOUNT DESC";
		$RSCALIST	=	$conn_172->Execute($CALIST);
		if($RSCALIST == false)
		{
			echo $conn_172->ErrorMsg()."::".__LINE__;exit();
		}
		else 
		{
			$CA_CAamount	=	"";
			$data			=	"";
			$table			=	"<br><br><br><br><br><br>";
			$table			.=	"<table border='0' width='100%' class='tblCAlist tablesorter'>";
			$table				.=	"<thead>";
			$table					.=	"<tr><td class='td-ca-list-title curved30px' colspan='4'>$seldep</td></tr>";
			$table					.=	"<tr align='center' class='trl-ca-list-hdr header'>";
			$table						.=	"<th>Employee ID</th>";
			$table						.=	"<th>Employee Name</th>";
			$table						.=	"<th>CA Amount</th>";
			$table						.=	"<th>Department</th>";
			$table					.=	"</tr>";
			$table			.=	"</thead>";
			$table			.=	"<tbody>";
			if($RSCALIST->RecordCount() == 0)
			{	
				$table	.=	"<tr align='center' class=''>
									<td class='header no-record tr-ca-list-dtls' colspan='4'>No records found.</td>
								</tr>
							 </table>";
			}
			else 
			{
				while (!$RSCALIST->EOF) 
				{
						$X++;
						$REQUESTEDBY	=	$RSCALIST->fields["REQUESTEDBY"];
						$USERNAME		=	$RSCALIST->fields["USERNAME"];
						$NAME			=	ucwords(strtolower($RSCALIST->fields["NAME"]));
						$TOTAMT			=	$RSCALIST->fields["AMOUNT"];
						$DEPARTMENT		=	$RSCALIST->fields["DEPARTMENT"];
	//					$TOTAMT			=	$Global_funcs->Sel_val($conn_172,"FDCFINANCIALS_PC","CASH_ADVANCE_HDR","SUM(AMOUNT)","STATUS = 'ISSUED' AND REQUESTEDBY= '{$REQUESTEDBY}' AND REQUESTEDDT BETWEEN '{$txtfrom} 00:00:00' AND '{$txtto} 23:59:59'");
						
						if($TOTAMT == "")
						{
							$TOTAMT = '0';
						}
						else 
						{
							$CA_CAamount	=	$CA_CAamount.",".intval($TOTAMT);
							$CA_Name		=	$CA_Name.","."$NAME: $TOTAMT";
							
							$table			.=	"<tr class='tr-ca-list-dtls'>";
							$table				.=	"<td style='padding:2px;' align=center>$USERNAME</td>";
							$table				.=	"<td style='padding:2px;'>$NAME</td>";
							$table				.=	"<td style='padding:2px;' align='right'>".number_format($TOTAMT,2)."</td>";
							$table				.=	"<td style='padding:2px;' align='left'>$DEPARTMENT</td>";
							$table			.=	"</tr>";
						}
						$RSCALIST->MoveNext();
				}
				$table		.=	"</tbody>";
				$table		.=	"</table>";
			}
		}
		$arrCA_amount		=	substr($CA_CAamount,1);
		$arrNAME			=	substr($CA_Name,1);
?>
<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title><?php echo $seldep; ?></title>
		<link 		href=	"/PETTY_CASH/styles/styles.css"rel="stylesheet">
	</head>
	<body style="background:#ffffff !important;">
		<input type="hidden" id="txtdataX" name="txtdataX" size="500" value="<?php echo ($arrNAME); ?>">
		<input type="hidden" id="txtdataY" name="txtdataY" size="500" value="<?php echo ($arrCA_amount) ; ?>">
		<script>
			var dataY	=	($("#txtdataY").val()).split(",");
			var dataX	=	($("#txtdataX").val()).split(",");
			
			var arrln 	=	(dataY.length);
			var Adata	=	new Array();
			var label, value;
			var value_to_push = new Array();
			for(var y = 0; y<arrln; y++)
			{
					Adata.push([dataX[y],parseFloat(dataY[y])]);
			}
		</script>
<?php
		if($chart == "PIE" or $chart == "BOTH")
		{
?>
		<script type="text/javascript">
			$(function () {
			    $('#divpie').highcharts({
			        chart: {
			            type: 'pie',
			            options3d: {
			                enabled: true,
			                alpha: 45
			            }
			        },
			        title: {
			            text: "Cash Advances of <?php echo $seldep;?>"
			        },
			        subtitle: {
			            text: "<?php echo " $txtfrom to $txtto" ?>"
			        },
			        plotOptions: {
			            pie: {
			                innerSize: 100,
			                depth: 45
			            }
			        },
			        series: [{
			            name: 'CA Amount',
			            data: Adata
			        }]
			    });
			});
		</script>
<?php
		}
		if($chart == "BAR" or $chart == "BOTH") 
		{
?>
		<script type="text/javascript">
			$(function () {
			    $('#divbar').highcharts({
			        chart: {
			            type: 'column',
			            height:600
			        },
			        title: {
			            text: "Cash Advances of <?php echo $seldep;?>"
			        },
			        subtitle: {
			            text: "<?php echo " $txtfrom to $txtto" ?>"
			        },
			        xAxis: {
			            type: 'category',
			            labels: {
			                rotation: -45,
			                style: {
			                    fontSize: '12px',
			                    fontFamily: 'Verdana, sans-serif'
			                }
			            }
			        },
			        yAxis: {
			            min: 100,
			            title: {
			                text: "Cash Advance Amount"
			            }
			        },
			        legend: {
			            enabled: true
			        },
			        tooltip: {
			            pointFormat: 'Cash Advance: <b>{point.y:.2f}</b>'
			        },
			        series: [{
			            name: 'Cash Advance',
			            data: Adata,
			            dataLabels: {
			                enabled: true,
			                rotation: -90,
			                color: '#FFFFFF',
			                align: 'right',
			                format: '{point.y:.2f}', // one decimal
			                y: 10, // 10 pixels down from the top
			                style: {
			                    fontSize: '10px',
			                    fontFamily: 'Verdana, sans-serif'
			                }
			            }
			        }]
			    });
			});
		</script>
<?php
		}
?>
			<div id="divpie" style="height: auto;"></div>
			<div id="divbar" style="height: auto;"></div>
			<div style="width:80%; float:left;margin-left:10%;margin-right:10%;">
				<?php echo $table; ?>
			</div>
		<script>
			$("table").tablesorter();
		</script>	
		</body>
	</html>
