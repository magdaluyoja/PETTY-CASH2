<?php
/********************************************************************************************************************
AUTHOR	: 	
MODULE	:	HOME PAGE
DATE	:	2015-09-09
*********************************************************************************************************************/
session_start();
set_time_limit(0);
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>FDC Petty Cash</title>
		<meta charset='utf-8'>
		<meta name="keywords" content="HTML,CSS,XML,JavaScript">
		<link 		href=	"/PETTY_CASH/images/favicon.ico"rel="icon">
		<link 		href=	"/PETTY_CASH/styles/styles.css"rel="stylesheet">
		<script 	src	=	"/PETTY_CASH/jquery/jquery-2.1.3.min.js"></script>
		<?php include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/js/jsUI.php") ?>
		<script 	src	=	"/PETTY_CASH/login.js"></script>
		<script 	src	=	"/PETTY_CASH/js/js.js"></script>
	</head>
	<body>
	<div id="divheader"></div>
	<div id="divtopnav"><?php if($_SESSION["PC"]["USERNAME"] != ""){include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/menu.php"); }?></div>
	<div id="divcontent" align="center" id="divcontent" align="center" class="<?php echo $_SESSION["PC"]["MB"]; ?>">
		<?php 
			if ($_SESSION["PC"]["USERNAME"] == ""){
				include("login.php");
			}
			else
			{
				include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/config/session.php");
				?>	
				<table height="100%" width="100%">
					<tr align="center">
						<td><h2 class='stroked'>Welcome <?php echo $_SESSION["PC"]["NAME"];?> to FDC's PETTY CASH System</h2></td>
					</tr>
				</table>
				<?php	
			}
		?>
	</div>
	<div id="divfooter">Copyright &copy; 2015 Filstar Distributors Corp. All rights reserved.</div>
	<?php
		include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/ui.php");
	?>
	</body>
</html>