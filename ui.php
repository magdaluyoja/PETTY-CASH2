<div id="diverrmsg" style="display:none;" class="divdialogs">
	<div class="ui-widget shadowed">
		<div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
			<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
			<strong>Alert:</strong> <a id="txterrmsg"></a></p>
		</div>
	</div>
</div>

<div id="divinfomsg" style="display:none;" class="divdialogs">
	<div class="ui-widget shadowed" >
		<div class="ui-state-highlight ui-corner-all" style="padding: 1em;overflow: auto;">
				<div class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;position:relative;"></div>
				<div style="float:left;position:relative;"><strong>Alert:</strong></div>
				<div id="txtinfomsg" style="float:left;position:relative;margin-left:5px;"></div>
		</div>
	</div>
</div>

<div id="divconfmsg" style="display:none;" class="divdialogs">
	<div class="ui-widget shadowed" >
		<div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;">
			<p><span class="ui-icon ui-icon-help" style="float: left; margin-right: .3em;background-color:#99FF66; border-radius:2px;"></span>
			<strong>Alert:</strong> <a id="txtconfmsg"></a></p>
		</div>
	</div>
</div>

<div id="divsuccmsg" style="display:none;" class="divdialogs">
	<div class="ui-widget shadowed" >
		<div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;">
			<p><span class="ui-icon ui-icon-check" style="float: left; margin-right: .3em;background-color:#CCFF66; border-radius:2px;"></span>
			<strong>Alert:</strong> <a id="txtsuccmsg"></a></p>
		</div>
	</div>
</div>

<div id="divloader" align="center" class="divdialogs">
	<br>
	<!--<img src="/PETTY_CASH/images/animated-loading.gif"style="height:100px;">-->
	<img src="/PETTY_CASH/images/loading2.GIF">
	<br><a id="loadingmsg"></a><br>Please wait...
</div>
<div id="divparticulars"></div>
<?php
	if($_SESSION["PC"]["ID"] != "")
	{
		echo "<img src='/PETTY_CASH/images/theme.gif' class='themeimg tooltips' title='Click to modify theme.'>";
	}
?>
<style>
.ui-dialog, .ui-widget-content
{
	<?php if($_SESSION["PC"]["CB"] == ""){echo "background:#eafaf8 !important;color:#1975A3 !important;";}?> 
}
</style>