<div id="divinfomsgtheme" style="display:none;" class="divdialogs">
	<div class="ui-widget shadowed" >
		<div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;">
			<p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
			<strong>Alert:</strong> <a id="txtinfomsgtheme"></a></p>
		</div>
	</div>
</div>
<script>
$("#seltheme").val("<?php echo $theme; ?>");
$("#selmainback").val("<?php echo $_SESSION["PC"]["MB"]; ?>");
$("#selcontentback").val("<?php echo $_SESSION["PC"]["CB"]; ?>");
$(".selectmenus").selectmenu();
$(".buttons").button();

$( "#divinfomsgtheme" ).dialog({
	title:"Message",
	modal:true,
	autoOpen: false,
	width: 500,
	buttons: [
		{
			text: "Ok",
			click: function() {
				$( this ).dialog( "close" );
				location.reload();
			}
		}
	]
});
</script>