<div id="divsession" style="display:none;">
	<div class="ui-widget shadowed">
		<div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;">
			<p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
			<strong>Alert:</strong> <a id="txtsessmsg"></a></p>
		</div>
	</div>
</div>
<script>
$("document").ready(function(){
	$( "#divsession" ).dialog({
		title:"Session Expired Message!",
		modal:true,
		dialogClass:"no-close",
		closeOnEscape:false,
		autoOpen: false,
		width: 500,
		buttons: [
			{
				text: "Ok",
				click: function() {
					$( this ).dialog( "close" );
					window.location="/PETTY_CASH";
				}
			}
		]
	});
});
</script>
<?php
	session_start();
	if($_GET["action"] == "EXPIRESESSION")
	{
		session_destroy();
		exit();
	}
	if($_SESSION["PC"]["USERNAME"] == "")
	{
		echo "<script>
					$('document').ready(function(){
						$('#txtsessmsg').text('Your session has expired. Please login again.');
						$('#divsession').dialog('open');	
					});
			  </script>";
		$action	=	"";
		exit();
	}
?>

