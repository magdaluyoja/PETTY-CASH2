<?php
session_start();
set_time_limit(0);
$action = $_GET["action"];
include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/config/session.php");
include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/config/config.php");
include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/config/class_db.php");
$TODAY	=	date("Y-m-d h:i:s");
if($action == "GETHOLILIST")
{
	$selholi	=	"SELECT * FROM ".PCDBASE.".HOLIDAYS";
	$rsselholi	=	$conn_172->Execute($selholi);
	if ($rsselholi	== false) 
	{
		$errmsg	=	($conn_172->ErrorMsg()."::".__LINE__); 
		$DATASOURCE->logError($errmsg,$fill,$_SESSION["PC"]["USERNAME"],"CA APPROVAL","GETCALIST");
		$DATASOURCE->displayError();
	}
	else 
	{
		$tableapps	=	"<table id='tblholilists' class='tblCAlist tablesorter' border='0' width='100%'>
						 <thead>
							<tr>
								<td class='td-ca-list-title' colspan='5'>Holiday List</td>
							</tr>
							<tr class='trl-ca-list-hdr header centered'>
								<th class='padding'>Holiday Date(Numeric)</th>
								<th class='padding'>Holiday Date</th>
								<th class='padding'>Holiday Name</th>
								<th class='padding'>Holiday Status</th>
								<td class='padding'>Actions</td>
							</tr>
						</thead>
						<tbody>";
		 while (!$rsselholi->EOF)
		 {
			$cnt	=	0;
			$id		=	$rsselholi->fields['H_ID'];	
			$date	=	$rsselholi->fields['H_DATE'];
			$name	=	$rsselholi->fields['H_NAME'];
			$status	=	$rsselholi->fields['STATUS'];
			
			$editbtn 	= "<img src='/PETTY_CASH/images/editme.png' 		class='smallimgbuttons btnedit			tooltips' alt='Edit' 		title='Edit' 	data-id='$id'>";
			$actbtn 	= "<img src='/PETTY_CASH/images/activate.png' 		class='smallimgbuttons btnactivate		tooltips' alt='Activate' 	title='Submit'	data-id='$id'>";
			$deactbtn 	= "<img src='/PETTY_CASH/images/deactivateme.png' 	class='smallimgbuttons btndeactivate	tooltips' alt='Deactivate' 	title='Cancel'	data-id='$id'>";
				
			$tableapps .= "	<tr>
								<td class='tr-ca-list-dtls centered'>".date("m-d",strtotime($date))."</td>
								<td class='tr-ca-list-dtls centered'>$date</td>
								<td class='tr-ca-list-dtls'>$name</td>
								<td class='tr-ca-list-dtls centered'>$status</td>
								<td class='tr-ca-list-dtls centered'>$editbtn $actbtn $deactbtn</td>
							</tr>";
			$rsselholi->MoveNext(); 
		}
		$tableapps .= "</tbody>
	 			</table>";
	 	echo $tableapps;
	}
	exit();
}
if($action == "SAVE")
{
	$id				=	$_POST['hidid'];
	$holidate		=	$_POST['txtdate'];
	$holiname		=	addcslashes(ucwords($_POST['txtname']), "'");
	$selsave		=	"INSERT ".PCDBASE.".HOLIDAYS (H_DATE, H_NAME, STATUS) VALUES('{$holidate}', '{$holiname}', 'ACTIVE')";
	$rsselsave		=	$DATASOURCE->execQUERY($conn_172,$selsave,$_SESSION["PC"]["USERNAME"],"HOLIDAY AMINTENANCE","SAVE");
	echo "<script>
			MessageType.successMsg('Holiday has been successfully saved.');
			getHliList();
			$('.input_text').val('');
		  </script>";

exit();
}
if($action == "UPDATE")
{
	$id				=	$_POST['hidid'];
	$holidate		=	$_POST['txtdate'];
	$holiname		=	addcslashes(ucwords($_POST['txtname']), "'");
	$selsave		=	"UPDATE ".PCDBASE.".HOLIDAYS SET H_DATE = '{$holidate}', H_NAME = '{$holiname}' 
						 WHERE H_ID = '{$id}'";
	$rsselsave		=	$DATASOURCE->execQUERY($conn_172,$selsave,$_SESSION["PC"]["USERNAME"],"HOLIDAY AMINTENANCE","UPDATE");
	echo "<script>
			MessageType.successMsg('Holiday has been successfully updated.');
			getHliList();
			$('.input_text').val('');
		  </script>";

exit();
}
if($action == "ACTHOLIDAY")
{
	
	$HOLIID	=	$_GET['HOLIID'];
	$selsave		=	"UPDATE ".PCDBASE.".HOLIDAYS SET STATUS = 'ACTIVE'
						 WHERE H_ID = '{$HOLIID}'";
	$rsselsave		=	$DATASOURCE->execQUERY($conn_172,$selsave,$_SESSION["PC"]["USERNAME"],"HOLIDAY AMINTENANCE","ACTHOLIDAY");
	echo "<script>
			MessageType.successMsg('Holiday has been successfully activated.');
			getHliList();
			$('.input_text').val('');
		  </script>";

exit();
}
if($action == "DEACTHOLIDAY")
{
	
	$HOLIID	=	$_GET['HOLIID'];
	$selsave		=	"UPDATE ".PCDBASE.".HOLIDAYS SET STATUS = 'INACTIVE'
						 WHERE H_ID = '{$HOLIID}'";
	$rsselsave		=	$DATASOURCE->execQUERY($conn_172,$selsave,$_SESSION["PC"]["USERNAME"],"HOLIDAY AMINTENANCE","DEACTHOLIDAY");
	echo "<script>
			MessageType.successMsg('Holiday has been successfully deactivated.');
			getHliList();
			$('.input_text').val('');
		  </script>";

exit();
}
if($action == "GETHOLIDTLS")
{
	$HOLIID	=	$_GET['HOLIID'];
	$holiname	=	$DATASOURCE->selval($conn_172,PCDBASE,"HOLIDAYS","H_NAME"," H_ID = '{$HOLIID}'");
	$holidate	=	$DATASOURCE->selval($conn_172,PCDBASE,"HOLIDAYS","H_DATE"," H_ID = '{$HOLIID}'");
	echo "<script>
			$('#txtname').val('$holiname');
			$('#hidid').val('$HOLIID');
			$('#txtdate').val('$holidate');
		  </script>";
	exit();
}
include("holidays.html");
?>