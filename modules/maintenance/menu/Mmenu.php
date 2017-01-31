<?php
session_start();
set_time_limit(0);
$action = $_GET["action"];
include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/config/session.php");
include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/config/config.php");
include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/config/class_db.php");
$TODAY	=	date("Y-m-d h:i:s");
if($action == "SEARCHLINK")
{
	$LINK		=	$_GET["LINK"];
	$MAINQUERY	=	$_GET["MAINQUERY"];
	$PAGENO		=	$_GET["PAGENO"];
	if($LINK != undefined)
	{
		$LINK_Q		=	" AND ID LIKE '%$LINK%' OR LINK_NAME LIKE '%$LINK%'";
	}
	if($MAINQUERY != undefined)
	{
		$LINK_Q	=	$_SESSION["MAINQUERY"];
	}
	else 
	{
		$GETLINKS	=	"SELECT * FROM ".PCDBASE.".MODULES WHERE 1 $LINK_Q";
	}
	$_SESSION["MAINQUERY"]	=	$GETLINKS;
	$RSGETLINK 	=	$conn_172->Execute($GETLINKS);
	if($RSGETLINK == false)
	{
		$errmsg	=	($conn_172->ErrorMsg()."::".__LINE__); 
		$DATASOURCE->logError($errmsg,$GETLINK,$_SESSION["PC"]["USERNAME"],"MENU","SEARCHLINK");
		$DATASOURCE->displayError();
	}
	else 
	{
		$TABLE	=	"<table border='0' width='100%' id='tbllinklist' class='tblCAlist tablesorter'>
							<thead> 
								<tr>
									<td class='td-ca-list-title' colspan='9'>Link List</td>
								</tr>
								<tr class='trl-ca-list-hdr header centered'>
									<th>Link ID</th>
									<th>Link Name</th>
									<th>Module Group</th>
									<th>Is Group</th>
									<th>Level</th>
									<th>Link</th>
									<th>Status</th>
									<th>Order</th>
									<td>Action</th>
								</tr>
							</thead>
							<tbody> ";
		if($RSGETLINK->RecordCount() == 0)
		{	
			$TABLE	.=	"<tr align='center'>
							<td class='header no-record tr-ca-list-dtls' colspan='9'>No records found.</td>
						</tr>
						</tbody>
					</table>";
		}
		else 
		{
			while (!$RSGETLINK->EOF) {
				$ID				= $RSGETLINK->fields["ID"]; 
				$LINK_NAME		= $RSGETLINK->fields["LINK_NAME"]; 
				$MODULE_GROUP	= $RSGETLINK->fields["MODULE_GROUP"]; 
				$IS_GROUP		= $RSGETLINK->fields["IS_GROUP"]; 
				$MODULE_LEVEL	= $RSGETLINK->fields["MODULE_LEVEL"]; 
				$LINK			= $RSGETLINK->fields["LINK"]; 
				$STATUS			= $RSGETLINK->fields["STATUS"]; 
				$ORDERING		= $RSGETLINK->fields["ORDERING"]; 
				$editbtn 	= "<img src='/PETTY_CASH/images/editme.png' class='smallimgbuttons btnedit	tooltips' alt='Edit Link' title='Edit Link'	data-id='$ID'>";
				$TABLE	.=		"<tr>
											<td class='tr-ca-list-dtls'>$ID</td>
											<td class='tr-ca-list-dtls'>$LINK_NAME</td>
											<td class='tr-ca-list-dtls'>$MODULE_GROUP</td>
											<td class='tr-ca-list-dtls' align='center'>$IS_GROUP</td>
											<td class='tr-ca-list-dtls' align='center'>$MODULE_LEVEL</td>
											<td class='tr-ca-list-dtls'>$LINK</td>
											<td class='tr-ca-list-dtls centered'>$STATUS</td>
											<td class='tr-ca-list-dtls centered'>$ORDERING</td>
											<td class='tr-ca-list-dtls centered'>$editbtn</td>
										</tr>";
				$RSGETLINK->MoveNext();
			}
		}
		$TABLE .= "</tbody>
	 			</table>";
	 	echo $TABLE;
	}
	exit();
}
if($action == "SETMODGROUP")
{
	$level	=	$_GET["MODLEVEL"] - 1;
	$group	=	$_GET["MODGROUP"];
	echo $DATASOURCE->getDropDown($conn_172,PCDBASE,"MODULES","input_text curved5px","selMODULEGROUP","ID,LINK_NAME","ID","LINK_NAME","MODULE_LEVEL = '{$level}' GROUP BY ID");
	if($group != undefined)
	{
		echo "<script>$('#selMODULEGROUP').val('$group');</script>";
	}
	
	exit();
}
if($action == "SAVELINK")
{
	$txtLINKID		=	$_POST["txtLINKID"];
	$txtLINKNAME	=	$_POST["txtLINKNAME"];
	$rdoISGROUP		=	$_POST["rdoISGROUP"];
	$selMODULELEVEL	=	$_POST["selMODULELEVEL"];
	$selMODULEGROUP	=	$_POST["selMODULEGROUP"];
	$txtLINK		=	$_POST["txtLINK"];
	$txtorder		=	$_POST["txtorder"];
	$selstatus		=	$_POST["selstatus"];
	$mode			=	$_GET["MODE"];
	if($mode == "SAVE")
	{
		$SAVELINK		=	"INSERT INTO ".PCDBASE.".MODULES(`ID`, `LINK_NAME`, `MODULE_GROUP`, `IS_GROUP`, `MODULE_LEVEL`, `LINK`,`ORDERING`,`STATUS`)
						 	 VALUES('{$txtLINKID}','{$txtLINKNAME}','{$selMODULEGROUP}','{$rdoISGROUP}','{$selMODULELEVEL}','{$txtLINK}','{$txtorder}','{$selstatus}')";
		$MSG			=	"saved";
	
	}
	if($mode == "UPDATE")
	{
		$SAVELINK		=	"UPDATE ".PCDBASE.".MODULES SET `LINK_NAME` = '{$txtLINKNAME}', `MODULE_GROUP`='{$selMODULEGROUP}', `IS_GROUP`='{$rdoISGROUP}', 
							`MODULE_LEVEL`='{$selMODULELEVEL}', `LINK`='{$txtLINK}',`ORDERING` = '{$txtorder}',STATUS = '{$selstatus}'
						 	 WHERE ID='{$txtLINKID}'";
		$MSG			=	"updated";
	}
	$RSSAVELINK		=	$DATASOURCE->execQUERY($conn_172,$SAVELINK,$_SESSION["PC"]["USERNAME"],"MENU MAINTENANCE","SAVELINK");
	echo "<script>
				MessageType.successMsg('Link has been successfully $MSG.');
				cancel();
				getMenuList();
		  </script>";
	exit();
}
if($action == "CHECKLINKID")
{
	$LINKID		=	$_GET["LINKID"];
	$LINKCNT	=	$DATASOURCE->selval($conn_172,PCDBASE,"MODULES","COUNT(ID)","ID = '{$LINKID}'");
	if ($LINKCNT > 0)
	{
		echo "<script>
					MessageType.errorMsg('Link ID already exists.');
					$('#txtLINKID').val('');
			  </script>";
	}
	exit();
}
if($action == "EDITLINK")
{
	
	$txtLINKID		=	$_GET["LINKID"];
	$txtLINKNAME	=	$DATASOURCE->selval($conn_172,PCDBASE,"MODULES","LINK_NAME",	"ID = '{$txtLINKID}'");
	$rdoISGROUP		=	$DATASOURCE->selval($conn_172,PCDBASE,"MODULES","IS_GROUP",		"ID = '{$txtLINKID}'");
	$selMODULELEVEL	=	$DATASOURCE->selval($conn_172,PCDBASE,"MODULES","MODULE_LEVEL",	"ID = '{$txtLINKID}'");
	$selMODULEGROUP	=	$DATASOURCE->selval($conn_172,PCDBASE,"MODULES","MODULE_GROUP",	"ID = '{$txtLINKID}'");
	$txtLINK		=	$DATASOURCE->selval($conn_172,PCDBASE,"MODULES","LINK",			"ID = '{$txtLINKID}'");
	$txtorder		=	$DATASOURCE->selval($conn_172,PCDBASE,"MODULES","ORDERING",		"ID = '{$txtLINKID}'");
	$status			=	$DATASOURCE->selval($conn_172,PCDBASE,"MODULES","STATUS",		"ID = '{$txtLINKID}'");
	echo "<script>
				$('#txtLINKID').val('$txtLINKID');
				$('#txtLINKNAME').val('$txtLINKNAME');
				$('#selMODULELEVEL').val('$selMODULELEVEL');
				$('#selMODULELEVEL' ).trigger('change',['$selMODULEGROUP']);
				$('#txtLINK').val('$txtLINK');
				$('.radioset input').removeAttr('checked');
				$('.radioset').buttonset('refresh');
				$('input[name=rdoISGROUP][value=\"$rdoISGROUP\"]').prop('checked', 'checked');
				$('.radioset').buttonset('refresh');
				$('#txtorder').val('$txtorder');
				$('#selstatus').val('$status');
		  </script>";
	exit();
}
include("Mmenu.html");
?>