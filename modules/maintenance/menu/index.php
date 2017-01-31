<?php
/********************************************************************************************************************
AUTHOR	: 	JAY-R AGUELO MAGADALUYO
MODULE	:	CASH ADVANCE MAINTENANCE: MENU
*********************************************************************************************************************/
session_start();
set_time_limit(0);
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>FDC Petty Cash Maintenance: Menu</title>
		<meta charset='utf-8'>
		<link 		href=	"/PETTY_CASH/images/favicon.ico"rel="icon">
		<link 		href=	"/PETTY_CASH/styles/styles.css"rel="stylesheet">
		<script 	src	=	"/PETTY_CASH/jquery/jquery-2.1.3.min.js"></script>
		<?php include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/js/jsUI.php") ?>
		<script 	src	=	"/PETTY_CASH/js/js.js"></script>
		<script 	src	=	"Mmenu.js"></script>
	</head>
	<body>
		<div id="divheader"></div>
		<div id="divtopnav"><?php include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/menu.php");?></div>
		<div id="divcontent" align="center" class="<?php echo $_SESSION["PC"]["MB"]; ?>">
			<table width="100%" border="0"  class="td-main-content <?php echo $_SESSION["PC"]["CB"]; ?>">
				<tr>
					<td class="td-page-title ui-widget-header">MENU MAINTENANCE</td>
				</tr>
				<tr>
					<td align="center">
						<table width="95%" border="0">
							<tr>
								<td align="center">
									<?php 
										include("Mmenu.php");
									?>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		<br><br><br>
		</div>
		<div id="divfooter"">Copyright &copy; 2015 Filstar Distributors Corp. All rights reserved.</div>
		<?php
			include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/modules/maintenance/menu/MmenuUI.php");
			include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/ui.php");
		?>
	</body>
</html>