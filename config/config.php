<?php
	include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/adodb5/adodb.inc.php");
//	$conn_171	=	newADOConnection("mysqlt");
//	$RSconn_171	=	$conn_171->Connect("192.168.250.171","root","");
//	if($RSconn_171 == false)
//	{
//		echo "<script>
//					$( document ).ready(function() {
//						MessageType.errorMsg('Unable to connect to database at server no. 171. Please contact web admin to fix this problem.');
//					});
//			  </script>";
//	}
	$conn_10	=	newADOConnection("mysqlt");
//	$RSconn_10	=	$conn_10->Connect("192.168.250.10","root","");
	// $RSconn_10	=	$conn_10->Connect("192.168.250.10","root","");
	// if($RSconn_10 == false)
	// {
	// 	echo "<script>
	// 				$( document ).ready(function() {
	// 					MessageType.errorMsg('Unable to connect to database at server no. 10. Please contact web admin to fix this problem.');
	// 				});
	// 		  </script>";
	// }
	
	$conn_172	=	newADOConnection("mysqlt");
//	$RSconn_172	=	$conn_172->Connect("192.168.250.172","root","");
//	$RSconn_172	=	$conn_172->Connect("192.168.250.17","root","");
	$RSconn_172	=	$conn_172->Connect("localhost","root","");
	if($RSconn_172 == false)
	{
		echo "<script>
					$( document ).ready(function() {
						MessageType.errorMsg('Unable to connect to database at server no. 172. Please contact web admin to fix this problem.');
					});
			  </script>";
	}
	define("PCDBASE","FDCFINANCIALS_PC");
	
?>