<?php
include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/config/config.php");
$conn	=	newADOConnection("mysqlt");
$RSconn	=	$conn->Connect("localhost","root","");
//$RSconn	=	$conn->Connect("192.168.250.172","root","");
if($RSconn == false)
{
	echo "<script>
				$( document ).ready(function() {
					MessageType.errorMsg('Unable to connect to database. Please contact web admin to fix this problem.');
				});
		  </script>";
}

function getLink($id,$LINKNAME,$DATASOURCE,$conn)
{
	$getlink	=	"SELECT * FROM ".PCDBASE.".MODULES WHERE MODULE_GROUP = '{$id}' AND STATUS ='Active' ORDER BY ORDERING";
	$RSgetlink	=	$conn->Execute($getlink);
	if($RSgetlink == false)
	{
		echo $errmsg	=	($conn->ErrorMsg()."::".__LINE__); 
		exit();
	}
	else 
	{
		while (!$RSgetlink->EOF) 
		{
			$ID				=	$RSgetlink->fields["ID"];
			$LINK_NAME		=	$RSgetlink->fields["LINK_NAME"];
			$MODULE_GROUP	=	$RSgetlink->fields["MODULE_GROUP"];
			$IS_GROUP		=	$RSgetlink->fields["IS_GROUP"];
			$LINK			=	$RSgetlink->fields["LINK"];
			$linkCnt		=	selval($conn, PCDBASE, "USER_ACCESS", "COUNT(LINK_ID)", "USERNAME = '{$_SESSION["PC"]["USERNAME"]}' AND LINK_ID = '{$ID}'");
			if ($linkCnt > 0)
			{
				$DISPLAY	=	"style='display:block;'";
			}
			else 
			{
				$DISPLAY	=	"style='display:none;'";
			}
			if($IS_GROUP == "N")
			{
				$linkhere	=	"$LINK";
				$class		=	"";
				$div_ul		=	"";
				$div_ul_end	=	"";
			}
			else 
			{
				$linkhere	=	"#$LINK_NAME";
				$class		=	"class='parent'";
				$div_ul		=	"<div $DISPLAY><ul>";
				$div_ul_end	=	"</ul></div>";
			}
			$linklist	.= "<li $DISPLAY> <a href='$linkhere' $class> <span>$LINK_NAME</span> </a>";	
								if ($IS_GROUP == "Y")
								{
									$linklist	.=	$div_ul;
									$linklist	.=	getLink($ID,$LINK_NAME,$DATASOURCE,$conn);
									$linklist	.=	$div_ul_end;
								}
								
			$linklist	.= 	 "</li>";
			$RSgetlink->MoveNext();
		}
		return $linklist;
	}
}
function selval($conn, $dbname, $tblname, $field, $condition)
{
	$selval		=	"SELECT $field FROM $dbname.$tblname WHERE $condition";
	$RSselval	=	$conn->Execute($selval);
	if($RSselval == false)
	{
		$errmsg	=	($conn->ErrorMsg()."::".__LINE__);
		db_funcs::logError($errmsg,$selval,"");
		exit();
	}
	else 
	{
		$data	=	$RSselval->fields["$field"];
		return $data;
	}
}
?>
<!--<link type="text/css" href="/PETTY_CASH/menus/<?php echo $_SESSION["PC"]["THEME"]; ?>/menu.css" rel="stylesheet" />-->
<link type="text/css" href="/PETTY_CASH/menus/cupertino/menu.css" rel="stylesheet" />
<div id="menu">
	<ul class="menu">	
		<?php
		$getModules		=	"SELECT * FROM ".PCDBASE.".MODULES WHERE MODULE_GROUP = ''  AND STATUS ='Active' ORDER BY ORDERING";
		$RSgetModules	=	$conn->Execute($getModules);
		if($RSgetModules == false)
		{
			echo $errmsg	=	($conn->ErrorMsg()."::".__LINE__); 
			exit();
		}
			$aMainMenu	=	array();
			while (!$RSgetModules->EOF) 
			{
				$LINK_NAME	=	$RSgetModules->fields["LINK_NAME"];
				$ID			=	$RSgetModules->fields["ID"];
				$IS_GROUP	=	$RSgetModules->fields["IS_GROUP"];
				$LINK		=	$RSgetModules->fields["LINK"];
				
				$linkCnt	=	selval($conn, PCDBASE, "USER_ACCESS", "COUNT(LINK_ID)", "USERNAME = '{$_SESSION["PC"]["USERNAME"]}' AND LINK_ID = '{$ID}'");
				if ($linkCnt > 0)
				{
					$DISPLAY	=	"style='display:block;'";
				}
				else 
				{
					$DISPLAY	=	"style='display:none;'";
				}
				if($IS_GROUP == "N")
				{
					$linkhere	=	"$LINK";
					$class		=	"";
					$div_ul		=	"";
					$div_ul_end	=	"";
				}
				else 
				{
					$linkhere	=	"#$LINK_NAME";
					$class		=	"class='parent'";
					$div_ul		=	"<div $DISPLAY><ul>";
					$div_ul_end	=	"</ul></div>";
				}
				
				echo 	 "<li $DISPLAY> <a href='$linkhere' $class> <span>$LINK_NAME</span> </a>";	
								if ($IS_GROUP == "Y")
								{
									echo $div_ul;
									echo getLink($ID,$LINK_NAME,$DATASOURCE,$conn);
									echo $div_ul_end;
								}
				echo 	 "</li>";
				$RSgetModules->MoveNext();
			}
			
		?>
	</ul>
	<ul class="menu2 menu" >
			<li style="color:#66ffff;"><?php echo "Hi " . $_SESSION["PC"]["NAME"] . "!"; ?></li>
			<li class="usersep parent" ><a href="/PETTY_CASH/logout.php" style="background:none;color:#ffffff;" id="lnklogout"><span>Logout</span></a></li>
	</ul>
</div>
