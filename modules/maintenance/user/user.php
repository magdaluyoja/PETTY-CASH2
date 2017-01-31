<?php
session_start();
set_time_limit(0);
$action = $_GET["action"];
include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/config/session.php");
include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/config/config.php");
include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/config/class_db.php");
$TODAY	=	date("Y-m-d h:i:s");
if($action == "SEARCHUSER")
{
	$USER		=	$_GET["USER"];
	$MAINQUERY	=	$_GET["MAINQUERY"];
	$PAGENO		=	$_GET["PAGENO"];
	if($USER != undefined)
	{
		$USER_Q	=	" AND USERNAME LIKE '%$USER%' OR NAME LIKE '%$USER%' OR DEPARTMENT LIKE '%$USER%' OR STATUS LIKE '%$USER%'";
	}
	if($MAINQUERY != undefined)
	{
		$GETUSER	=	$_SESSION["MAINQUERY"];
	}
	else 
	{
		$GETUSER	=	"SELECT * FROM ".PCDBASE.".USERS WHERE 1 $USER_Q";
	}
	$_SESSION["MAINQUERY"]	=	$GETUSER;
	$RSGETUSER 	=	$conn_172->Execute($GETUSER);
	if($RSGETUSER == false)
	{
		$errmsg	=	($conn_172->ErrorMsg()."::".__LINE__); 
		$DATASOURCE->logError($errmsg,$GETUSER,$_SESSION["PC"]["USERNAME"],"USER","SEARCHUSER");
		$DATASOURCE->displayError();
	}
	$TABLE	=	"<table border='0' width='100%' id='tbluserlist' class='tblCAlist tablesorter'>
					<thead> 
						<tr>
							<td class='td-ca-list-title' colspan='9'>User List</td>
						</tr>
						<tr class='trl-ca-list-hdr header centered'>
							<th>Username</th>
							<th>Name</th>
							<th>User Level</th>
							<th>Email Add</th>
							<th>Position</th>
							<th>Department</th>
							<th>Status</th>
							<th>Custodian</th>
							<td>Actions</th>
						</tr>
					</thead>
					<tbody> ";
	if($RSGETUSER->RecordCount() == 0)
	{	
		$TABLE	.=	"<tr align='center' class=''>
						<td class='header no-record tr-ca-list-dtls' colspan='9'>No records found.</td>
					</tr>
					</tbody>
				</table>";
	}
	else 
	{
		while (!$RSGETUSER->EOF) {
			$USERID			= $RSGETUSER->fields["USERID"]; 
			$USERNAME		= $RSGETUSER->fields["USERNAME"]; 
			$NAME			= ucwords(strtolower($RSGETUSER->fields["NAME"])); 
			$USERLEVEL		= $RSGETUSER->fields["USERLEVEL"]; 
			$POSITION		= ucwords(strtolower($RSGETUSER->fields["POSITION"])); 
			$DEPARTMENT		= $RSGETUSER->fields["DEPARTMENT"]; 
			$STATUS			= $RSGETUSER->fields["STATUS"]; 
			$EMAILADD		= $RSGETUSER->fields["EMAILADD"]; 
			$ISCUSTODIAN	= $RSGETUSER->fields["ISCUSTODIAN"]; 
			$editbtn 	= "<img src='/PETTY_CASH/images/editme.png' 	class='smallimgbuttons btnedit		tooltips' alt='Edit User' 			title='Edit User'			data-id='$USERID'>";
			$menubtn 	= "<img src='/PETTY_CASH/images/menu.png' 		class='smallimgbuttons btnmenu		tooltips' alt='Edit Module Access' 	title='Edit Module Access'	data-username='$USERNAME'>";
			$TABLE	.=		"<tr>
										<td class='tr-ca-list-dtls centered'>$USERNAME</td>
										<td class='tr-ca-list-dtls'>$NAME</td>
										<td class='tr-ca-list-dtls'>$USERLEVEL</td>
										<td class='tr-ca-list-dtls'>$EMAILADD</td>
										<td class='tr-ca-list-dtls'>$POSITION</td>
										<td class='tr-ca-list-dtls'>$DEPARTMENT</td>
										<td class='tr-ca-list-dtls centered'>$STATUS</td>
										<td class='tr-ca-list-dtls centered'>$ISCUSTODIAN</td>
										<td class='tr-ca-list-dtls centered'>$editbtn $menubtn</td>
									</tr>";
			$RSGETUSER->MoveNext();
		}
		$TABLE .= "</tbody>
	 			</table>";
	}
	echo $TABLE;
	exit();
}
if($action == "CHECKUSERNAME")
{
	$USERNAME 	= 	$_GET["USERNAME"];
	$CHKUNAME	=	$DATASOURCE->selval($conn_172,PCDBASE,"USERS","COUNT(USERNAME)","USERNAME = '{$USERNAME}'");
	if($CHKUNAME > 0)
	{
		echo "<script>
				MessageType.infoMsg('Username is already in use.');
				$('#txtusername').val('');
		  </script>";
	}
	exit;
}
if($action == "SAVEUSER")
{
	$UPDATEMODE			= $_GET["UPDATEMODE"];
	$name				= ucwords($_POST['txtname']);
	$username 			= $_POST['txtusername'];
	$password 			= $_POST['txtpassword'];
	$position			= $_POST['txtposition'];
	$department			= $_POST['seldep'];
	$status				= $_POST['selstatus'];
	$level				= $_POST['sellevel'];
	$addlvl				= $_POST['optcustodian'];
	$txteadd			= $_POST['txteadd'];
	$depinitial			= $DATASOURCE->selval($conn_172,PCDBASE,"DEPARTMENTS","DEP_INITIAL","DEP_DESCRIPTION = '{$department}'");
	$conn_172->StartTrans();
	if($UPDATEMODE == "updatemode")
	{
		$ENDMSG	=	"updated.";
		$save	=	"UPDATE ".PCDBASE.".USERS SET PASSWORD='$password', NAME='$name', USERLEVEL='$level', POSITION='$position', DEPARTMENT='$department', 
					 DEPT_INITIAL='$depinitial', STATUS='$status', EMAILADD='$txteadd',EDITEDBY='{$_SESSION["PC"]['USERNAME']}', EDITEDDT='$TODAY', ISCUSTODIAN='$addlvl'
					 WHERE USERNAME = '{$username}'";
	}
	else 
	{
		$ENDMSG	=	"saved.";
		$save	=	"INSERT INTO ".PCDBASE.".USERS (USERNAME, PASSWORD, NAME, USERLEVEL, POSITION, DEPARTMENT, DEPT_INITIAL, STATUS, EMAILADD,CREATEDBY, CREATEDDT, ISCUSTODIAN) 
					 VALUES ('{$username}','{$password}', '{$name}', '{$level}', '{$position}', '{$department}', '{$depinitial}', '{$status}','{$txteadd}', '{$_SESSION["PC"]['USERNAME']}', '{$TODAY}', '{$addlvl}')";
	}
//	echo $save; exit();
	$rssel	=	$DATASOURCE->execQUERY($conn_172,$save,$_SESSION["PC"]["USERNAME"],"USER AMINTENANCE","SAVEUSER");
	$conn_172->CompleteTrans();
	echo "<script>
			MessageType.successMsg('User iformation has been successfully $ENDMSG.');
			getUserList('',1,'');
		  </script>";
	exit();
}	
if($action == "EDITUSER")
{
	$userid	=	$_GET["USERID"];
	$sel	 	=	"SELECT * from ".PCDBASE.".USERS  WHERE USERID = '{$userid}'";
	$rssel		=	$conn_172->Execute($sel);
	if ($rssel == false) 
	{
		$errmsg	=	($conn_172->ErrorMsg()."::".__LINE__); 
		$DATASOURCE->logError($errmsg,$selapp,$_SESSION["PC"]["USERNAME"],"USER","EDITUSER");
		$DATASOURCE->displayError();
	}
	else 
	{
		$USERID			=$rssel->fields["USERID"]; 
		$USERNAME		=$rssel->fields["USERNAME"]; 
		$PASSWORD		=$rssel->fields["PASSWORD"]; 
		$NAME			=$rssel->fields["NAME"]; 
		$USERLEVEL		=$rssel->fields["USERLEVEL"]; 
		$POSITION		=$rssel->fields["POSITION"]; 
		$DEPARTMENT		=$rssel->fields["DEPARTMENT"]; 
		$DEPT_INITIAL	=$rssel->fields["DEPT_INITIAL"]; 
		$STATUS			=$rssel->fields["STATUS"]; 
		$EMAILADD		=$rssel->fields["EMAILADD"]; 
		$ISCUSTODIAN	=$rssel->fields["ISCUSTODIAN"]; 
		echo "<script> 
				$('.radioset input').removeAttr('checked');
				$('.radioset').buttonset('refresh');
				$('#txtname').val('$NAME');
				$('#txtusername').val('$USERNAME');
				$('#txtpassword').val('$PASSWORD');
				$('#txtcpassword').val('');
				$('#txtposition').val('$POSITION');
				$('#seldep').val('$DEPARTMENT');
				$('#selstatus').val('$STATUS');
				$('#sellevel').val('$USERLEVEL');
				$('#txteadd').val('$EMAILADD');
				$('#rdo$ISCUSTODIAN').prop('checked',true);
				$('.radioset').buttonset('refresh');
				$('#divtrxuser').show();
				$('#btnupdate').show();
				$('#btnsave').hide();
				$('#txtusername').attr('disabled','disabled');
			  </script>";
	}
	exit();
}
if($action == "GETMODULES")
{
	$username	=	$_GET["USERNAME"];
	$name		=	$DATASOURCE->selval($conn_172,PCDBASE,"USERS","NAME","USERNAME = '{$username}'");
	
	$getModules		=	"SELECT * FROM ".PCDBASE.".MODULES WHERE MODULE_GROUP = ''";
	$RSgetModules	=	$conn_172->Execute($getModules);
	if($RSgetModules == false)
	{
		$errmsg	=	($conn_172->ErrorMsg()."::".__LINE__); 
		$DATASOURCE->logError($errmsg,$getModules,$_SESSION["PC"]["USERNAME"],"USER","GETMODULES");
		$DATASOURCE->displayError();
	}
	else 
	{
		$aMainMenu	=	array();
		echo "<table width='100%' border='0'>
				<tr>
					<td class='td-page-title ui-widget-header' id='tdEMuser'>$name--$username</td>
				</tr>
				<tr>
					<td>";
						echo 	 "<form id='frmmodules'>";
						echo 	 "<div id='menutab' class='shadowed'>";
						echo 		"<ul>";	
						while (!$RSgetModules->EOF) 
						{
							$LINK_NAME	=	$RSgetModules->fields["LINK_NAME"];
							$ID			=	$RSgetModules->fields["ID"];
							$IS_GROUP	=	$RSgetModules->fields["IS_GROUP"];
							echo 		"<li> <a href='#div$ID'>$LINK_NAME</a></li>";	
							$aMainMenu[$ID]["LINK_NAME"]	=	$LINK_NAME;
							$aMainMenu[$ID]["IS_GROUP"]		=	$IS_GROUP;
							$RSgetModules->MoveNext();
						}
						echo 		"</ul>";
						foreach ($aMainMenu as $id=>$val)
						{
							$LINKNAME	=	$val["LINK_NAME"];
							$IS_GROUP	=	$val["IS_GROUP"];
							echo 	 "<div id='div$id'>";	
							echo 		"<ul>";
							echo 			"<li>
												<label for='$id' onclick='toggleD(\"$id\");'>
													<input type='checkbox' id='$id' name='links[]' value='$id'>";
							echo 						"$LINKNAME";
														
							echo 				"</label>";
												if ($IS_GROUP == "Y")
												{
													echo getLinks($id,$LINKNAME,$DATASOURCE,$conn_172);
												}
							echo 			"</li>";
							echo 		"</ul>";
							echo 	 "</div>";	
						}
						echo 	 "</div>";	
						echo 	 "</form>";	
						
						$GETUSERACCESS		=	"SELECT * FROM ".PCDBASE.".USER_ACCESS WHERE USERNAME = '{$username}'";
						$RSGETUSERACCESS	=	$conn_172->Execute($GETUSERACCESS);
						if($RSGETUSERACCESS == false)
						{
							$errmsg	=	($conn_172->ErrorMsg()."::".__LINE__); 
							$DATASOURCE->logError($errmsg,$GETUSERACCESS,$_SESSION["PC"]["USERNAME"],"USER","GETMODULES");
							$DATASOURCE->displayError();
						}
						else 
						{
							while (!$RSGETUSERACCESS->EOF) 
							{
								$LINK_ID	=	$RSGETUSERACCESS->fields["LINK_ID"];
								echo "<script>
											$('#$LINK_ID').prop('checked',true);
									  </script>";
								$RSGETUSERACCESS->MoveNext();
							}
						}
		echo 		"</td>
				</td>
			 </table>";
		
	}
	exit();
}
function getLinks($id,$LINKNAME,$DATASOURCE,$conn_172)
{
	$aID		=	explode(" ",$id);
	$aLength	=	count($aID);
	$newid	=	$aID[$aLength-1];
	$getlink	=	"SELECT * FROM ".PCDBASE.".MODULES WHERE MODULE_GROUP = '{$newid}'";
	$RSgetlink	=	$conn_172->Execute($getlink);
	if($RSgetlink == false)
	{
		$errmsg	=	($conn_172->ErrorMsg()."::".__LINE__); 
		$DATASOURCE->logError($errmsg,$getlink,$_SESSION["PC"]["USERNAME"],"USER","GETMODULES");
		$DATASOURCE->displayError();
	}
	else 
	{
		$linklist	=	"<ul>";
		while (!$RSgetlink->EOF) 
		{
			$ID				=	$RSgetlink->fields["ID"];
			$LINK_NAME		=	$RSgetlink->fields["LINK_NAME"];
			$MODULE_GROUP	=	$RSgetlink->fields["MODULE_GROUP"];
			$IS_GROUP		=	$RSgetlink->fields["IS_GROUP"];
			if ($IS_GROUP == "Y")
			{
				$toggleD	=	"toggleD(\"$ID\");";
			}
			else
			{
				$toggleD	=	"";
			}
			$linklist		.=	"<li>
									<label for='$ID' onclick='$toggleD toggleG(\"$id\");'>
										<input type='checkbox' id='$ID' name='links[]' value='$ID' class='$id'>";
			$linklist		.=				"$LINK_NAME";
			$linklist		.=		"</label>";
											if ($IS_GROUP == "Y")
											{
			$linklist		.= 					getLinks($id." ".$ID,$LINK_NAME,$DATASOURCE,$conn_172);
											}
			$linklist		.=	"</li>";
			$RSgetlink->MoveNext();
		}
		$linklist	.=	"</ul>";
		return $linklist;
	}
}
if($action == "SAVEMODULES")
{
	$txtEusername	=	explode("--",$_GET["USERNAME"]);
	$txtEusername		=	$txtEusername[1];
	$conn_172->StartTrans();
		$delUserAccess		=	"DELETE FROM ".PCDBASE.".USER_ACCESS WHERE USERNAME = '{$txtEusername}'";
		$RSdelUserAccess	=	$DATASOURCE->execQUERY($conn_172,$delUserAccess,$_SESSION["PC"]["USERNAME"],"USER","SAVEMODULES");
		if(!empty($_POST['links'])) 
		{
	    	foreach($_POST['links'] as $link)
		    {
		       	$DATENOW	=	date("Y-m-d h:i:s");
		       	$INSERTUSERACCESS	=	"INSERT INTO ".PCDBASE.".USER_ACCESS(`USERNAME`, `LINK_ID`, `MODIFIED_BY`, `MODIFIED_DATE`)
		       							 VALUES('{$txtEusername}','{$link}','{$_SESSION["PC"]["USERNAME"]}','{$DATENOW}')"; 
		       	$RSINSERTUSERACCESS	=	$DATASOURCE->execQUERY($conn_172,$INSERTUSERACCESS,$_SESSION["PC"]["USERNAME"],"USER","SAVEMODULES");
	   		}
	   		echo "<script>
					MessageType.successMsg('User access has been successfully modified.');
					$('#divEmodules').dialog('close');
			  </script>";
		}
	$conn_172->CompleteTrans();
	exit();
}
include("user.html");
?>