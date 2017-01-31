<?php
session_start();
if ($_SESSION["PC"]["USERNAME"] == "" or $_SESSION["PC"]["THEME"] == "") {
	$theme = "cupertino";
}
else 
{
	$theme = $_SESSION["PC"]["THEME"];
}
?>
<script 	src	=	"/PETTY_CASH/jquery/jquery-2.1.3.min.js"></script>
<script 	src	=	"/PETTY_CASH/jquery/<?php echo $theme; ?>/external/jquery/jquery.js"></script>
<script 	src	=	"/PETTY_CASH/jquery/<?php echo $theme; ?>/jquery-ui.min.js"></script>
<link 		href=	"/PETTY_CASH/jquery/<?php echo $theme; ?>/jquery-ui.min.css" rel="stylesheet">

<script 	src=	"/PETTY_CASH/js/table_sorter/jquery.tablesorter.js"></script> 
<link 		href=	"/PETTY_CASH/js/table_sorter/themes/blue/style.css" rel="stylesheet">
<script 	src=	"/PETTY_CASH/js/Paging/paging.js"></script> 

<script 	src=	"/PETTY_CASH/js/table_sorter/html2canvas.js"></script> 
<script 	src=	"/PETTY_CASH/js/Highcharts/js/highcharts.js"></script>
<script 	src=	"/PETTY_CASH/js/Highcharts/js/modules/exporting.js"></script>
<script 	src=	"/PETTY_CASH/js/Highcharts/js/highcharts-3d.js"></script>
<script 	src=	"/PETTY_CASH/js/Highcharts/js/modules/exporting.js"></script>