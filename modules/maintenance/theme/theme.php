<?php
	session_start();
	$action = $_GET["action"];
	include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/config/session.php");
	include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/config/config.php");
	include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/config/class_db.php");
	if($action == "SETTHEME")
	{
		$THEMENAME 		= 	$_GET["THEMENAME"];
		$SELMAINBACK	=	$_GET["MAINBACK"];
    	$SELCONTENTBACK	=	$_GET["CONTENTBACK"];
		if ($_SESSION["PC"]["THEME"] == "") {
			$savetheme	=	"INSERT INTO ".PCDBASE.".THEME(USERNAME,THEME,MAIN_BACKGROUND,CONTENT_BACKGROUND)
							 VALUES('{$_SESSION["PC"]["USERNAME"]}','{$THEMENAME}','{$SELMAINBACK}','{$SELCONTENTBACK}')";
		}
		else 
		{
			$savetheme = 	"UPDATE ".PCDBASE.".THEME SET THEME = '{$THEMENAME}',MAIN_BACKGROUND='{$SELMAINBACK}',CONTENT_BACKGROUND='{$SELCONTENTBACK}'
							 WHERE USERNAME = '{$_SESSION["PC"]["USERNAME"]}'";
		}
//		echo $savetheme; exit();
		$RSTHEME = $DATASOURCE->execQUERY($conn_172,$savetheme,$_SESSION["PC"]["USERNAME"],"THEME MAINTENANCE",SETTHEME);
		if($RSTHEME)
		{
			echo "<script>
					$( document ).ready(function() {
						$('#txtinfomsgtheme').text('Theme $THEMENAME has been successfully set.');
						$('#divinfomsgtheme').dialog('open');
						
					});
			  </script>";
			$_SESSION["PC"]["THEME"] 	= $THEMENAME;
			$_SESSION["PC"]["MB"] 		= $SELMAINBACK;
			$_SESSION["PC"]["CB"] 		= $SELCONTENTBACK;
		}
		exit();
	}
	include("theme.html");
?>