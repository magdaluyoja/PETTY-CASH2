$("document").ready(function(){
	$("#btnsearch").click(function(){
		var CAnum	=	$("#txtCAnum").val();
		var date	=	$("#txtdate").val();
		getMenuList($("#txtsearch").val());
	});
	$("#txtsearch").keyup(function(e){ 
	    var code = e.which;
	    if(code==13)e.preventDefault();
	    if(code==13){
	      getMenuList(this.value);
	    }
	});
	$("#btnnew").click(function(){
		$("#divtrxlink").show("fade");
	});
	$("#btncancel").click(function(){
		cancel();
	});
	$("#txtLINKID").change(function(){
		$.ajax({
				url:	"Mmenu.php?action=CHECKLINKID&LINKID="+this.value,
				beforeSend:function()
				{
					$('#divloader').dialog("open");
				},
				success:function(response)
				{
					$("#divLinkDebug").html(response);
					$('#divloader').dialog("close");
				}
			});
	});
	$("#selMODULELEVEL").change(function(event,modeulegroup){
		var level	=	$(this).val();
		$.ajax({
				url:	"Mmenu.php?action=SETMODGROUP&MODLEVEL="+level+"&MODGROUP="+modeulegroup,
				beforeSend:function()
				{
					$('#divloader').dialog("open");
				},
				success:function(response)
				{
					$("#tdmodulegroup").html(response);
					$('#divloader').dialog("close");
				}
			});
	});
	$("#btnsave").click(function(){
		if(validate())
		{
			MessageType.confirmmsg(saveMENU,"Do you want to save this link information?","SAVE");
		}
		else
		{
			MessageType.infoMsg('Fill in the empty fields.');
		}
	});
	$("#btnupdate").click(function(){
		if(validate())
		{
			MessageType.confirmmsg(saveMENU,"Do you want to update this link information?","UPDATE");
		}
		else
		{
			MessageType.infoMsg('Fill in the blank fields.');
		}
	});
	$("tr td").on("click",".btnedit",function(){
		var linkid = $(this).attr("data-id");
		$.ajax({
			type	:	"GET",
			url		:	"Mmenu.php?action=EDITLINK&LINKID="+linkid,
			beforeSend:function(){
				$('#divloader').dialog("open");
			},
			success:function(response){
				$("#divLinkDebug").html(response);
				$('#divloader').dialog("close");
				$("#divtrxlink").show();
				$("#btnupdate").show();
				$("#btnsave").hide();
				$("#txtLINKID").attr('readonly', true);
				var element = document.getElementById("btnnew");
					element.scrollIntoView();
					element.scrollIntoView(false);
					element.scrollIntoView({block: "end"});
					element.scrollIntoView({block: "end", behavior: "smooth"});
			}
		});
	});
});
function getMenuList(linkval,mainquery,pageno)
{
	$.ajax({
		url			:	'Mmenu.php?action=SEARCHLINK&LINK='+linkval+"&MAINQUERY="+mainquery+"&PAGENO="+pageno,
		beforeSend	:	function()
					{
						$('#divloader').dialog("open");
					},
		success		:	function(response)
					{
						$('#divlinks').html(response);
						$('#divloader').dialog("close");
						$("#tbllinklist").tablesorter({sortList: [[0,0]]}); 
						$('#tbllinklist').paging({limit:15});
						$(".tooltips").tooltip();
						$(".buttons").button();
					}
		});
}
function cancel()
{
	$('#frmMmenu *').filter(':input').each(function(){
		if($(this).attr('type') != "radio" && $(this).attr('type') != "button")
		{
	    	$(this).val("");
	    	$(this).removeClass("errpurpose");
		}
		else
		{
			$("#tdisgroup").removeClass("errpurpose");
		}
	});
	$('.radioset input').removeAttr('checked');
	$('.radioset').buttonset('refresh');
	$("#txtLINKID").attr('readonly', false);
	$("#divtrxlink").hide();
	$('#btnupdate').hide();
	$('#btnsave').show();
}
function validate()
{
	var valid		=	true;
	if($("#txtLINKID").val() == "")		{	$("#txtLINKID").addClass("errpurpose"); valid = false;		}else{	$("#txtLINKID").removeClass("errpurpose");}
	if($("#txtLINKNAME").val() == "")	{	$("#txtLINKNAME").addClass("errpurpose"); valid = false;	}else{	$("#txtLINKNAME").removeClass("errpurpose");}
	if($("#selMODULELEVEL").val() == ""){	$("#selMODULELEVEL").addClass("errpurpose"); valid = false;	}else{	$("#selMODULELEVEL").removeClass("errpurpose");}
	if($("#txtorder").val() == "")		{	$("#txtorder").addClass("errpurpose"); valid = false;		}else{	$("#txtorder").removeClass("errpurpose");}
	if($("#selstatus").val() == "")		{	$("#selstatus").addClass("errpurpose"); valid = false;		}else{	$("#selstatus").removeClass("errpurpose");}

	if($("#selMODULELEVEL").val() != "1" && $("#selMODULELEVEL").val() != "")
	{
		if($("#selMODULEGROUP").val() == ""){	$("#selMODULEGROUP").addClass("errpurpose"); valid = false;	}else{	$("#selMODULEGROUP").removeClass("errpurpose");}
	}
	else
	{
		$("#selMODULEGROUP").removeClass("errpurpose");
	}
	if($('input:radio[name=rdoISGROUP]').val() == "N")
	{
		if($("#txtLINK").val() == "")		{	$("#txtLINK").addClass("errpurpose"); valid = false;		}else{	$("#txtLINK").removeClass("errpurpose");}
	}
	if($('input:radio[name=rdoISGROUP]').is(':checked') == false){	
		$("#tdisgroup").addClass("errpurpose"); valid = false;
	}else{	
		$("#tdisgroup").removeClass("errpurpose");
	}
	return valid;
}
function saveMENU(MODE)
{
	var frmMmenu		=	$("#frmMmenu").serialize();
	$.ajax({
			data		:	frmMmenu,
			type		:	"POST",
			url			:	"Mmenu.php?action=SAVELINK&MODE="+MODE,
			beforeSend	:function()
						{
							$('#divloader').dialog("open");
						},
			success		:function(response)
						{
							$("#divLinkDebug").html(response);
							$('#divloader').dialog("close");
						}
			});
}