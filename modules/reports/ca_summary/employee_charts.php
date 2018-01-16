<?php
	$txtempid	=	$_POST["txtempid"];
	$txtname	=	$_POST["txtname"];
	$year		=	$_POST["selyear"];
	$U_ID		=	$DATASOURCE->selval($conn_172,PCDBASE,"USERS","USERID","USERNAME = '{$txtempid}'");
	for ($a = 1; $a <= 12; $a++)
	{
		$month		=	str_pad($a,2,"0",STR_PAD_LEFT);
		$monthname	=	date("F",strtotime("2001-$month-01 00:00:00"));
		$M_amt		=	$DATASOURCE->selval($conn_172,PCDBASE,"CASH_ADVANCE_HDR","SUM(AMOUNT)","DATE_FORMAT(RELEASEDDT,'%Y-%m') = '$year-$month' AND REQUESTEDBY = '{$U_ID}'");
		if($M_amt == "")
		{
			$M_amt = "0";
		}
		$a_M_amount	.=	",$M_amt";
		$a_M_name	.=	",$monthname:$M_amt";
	}
	$a_M_amount = substr($a_M_amount,1);
	$a_M_name 	= substr($a_M_name,1);
?>
<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Per Employee</title>
		<script type="text/javascript" src="jquery-1.11.3.min.js"></script>
		<script type="text/javascript" src="html2canvas.js"></script>
		<style type="text/css">
			${demo.css}
			#divbar {
				height: 400px; 
				min-width: 310px; 
				max-width: 800px;
				margin: 0 auto;
			}
		</style>
		<script src="Highcharts/js/highcharts.js"></script>
		<script src="Highcharts/js/modules/exporting.js"></script>
		<script src="Highcharts/js/highcharts-3d.js"></script>
		<script src="Highcharts/js/modules/exporting.js"></script>
	</head>
	<body>
		<input type="hidden" id="txtval" name="txtval" value="<?php echo $a_M_amount; ?>" >
		<input type="hidden" id="txtlbl" name="txtlbl" value="<?php echo $a_M_name; ?>" >
		<script type="text/javascript">
		<?php
		if($chart == "BAR" or $chart == "BOTH")
		{
		?>
		$(function () {
			var a_val	=	new Array();
			var F_val	=	new Array();
				a_val	=	($("#txtval").val()).split(",");
				for(var a = 0; a<12; a++)
				{
					F_val.push(parseFloat(a_val[a]));
				}
		    $('#divbar').highcharts({
		        chart: {
		            type: 'column',
		            margin: 75,
		            options3d: {
		                enabled: true,
		                alpha: 10,
		                beta: 25,
		                depth: 70
		            }
		        },
		        title: {
		            text: 'Cash Advance of <?php echo $txtname;?>'
		        },
		        subtitle: {
		            text: 'For the Year of <?php echo $year; ?>'
		        },
		        plotOptions: {
		            column: {
		                depth: 25
		            }
		        },
		        xAxis: {
		            categories: Highcharts.getOptions().lang.shortMonths
		        },
		        yAxis: {
		            title: {
		                text:  "Cash Advance Amount"
		            }
		        },
		        series: [{
		            name: 'Cash Advance',
		            data: F_val,
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
		<?php
		}
		if($chart == "PIE" or $chart == "BOTH") 
		{
		?>
		$(function () {
			var dataY	=	($("#txtval").val()).split(",");
			var dataX	=	($("#txtlbl").val()).split(",");
			
			var arrln 	=	(dataY.length);
			var Adata	=	new Array();
			var label, value;
			var value_to_push = new Array();
			for(var y = 0; y<arrln; y++)
			{
					Adata.push([dataX[y],parseFloat(dataY[y])]);
			}
			    $('#divpie').highcharts({
			        chart: {
			            type: 'pie',
			            options3d: {
			                enabled: true,
			                alpha: 45
			            }
			        },
			        title: {
			            text: "Cash Advance of <?php echo $txtname;?>"
			        },
			        subtitle: {
			            text: "For the Year of <?php echo $year; ?>"
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
		<?php
		}
		?>
		</script>
		<div id="divpie" style="height: auto"></div>
		<div id="divbar" style="height: auto"></div>
	</body>
</html>
