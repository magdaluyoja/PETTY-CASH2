var duppurposeerr;
$("document").ready(function(){
	$("#btnnew").click(function(){
		if($("#disablewhenediting").val() == "0")
		{
			if(checkliquidation())
			{
				$('#divCAreq').toggle("fade");
				$("#trcanumber").hide();
				$("#lblcanumber").text("");
				disablebtns();
			}
		}
	});
	$("#btnaddpurpose").click(function(){
		var purposecntold	= + $("#hdnpurposecnt").val();
		var purposecntnew	=	purposecntold + 1;
		var purposetr		=	"";
		purposetr	=	"<tr id='trpurpose_"+purposecntnew+"'>";
		purposetr	+=		"<td><input type='hidden' id='hidremark_"+purposecntnew+"' name='hidremark_"+purposecntnew+"'></td>";
		purposetr	+=		"<td>:</td>";
		purposetr	+=		"<td>";
		purposetr	+=			"<select name='selpurpose_"+purposecntnew+"' id='selpurpose_"+purposecntnew+"' class='input_text curved5px selpurposes' data-cnt='"+purposecntnew+"'>";
		purposetr	+=			"</select>";
		purposetr	+=		"</td>";
		purposetr	+=		"<td>";
		purposetr	+=			"<img src='/PETTY_CASH/images/viewrem.png' 	id='btnviewrem_"+purposecntnew+"' 	title='View Remarks' 	class='smallimgbuttons viewrem tooltips' 	data-curcnt='"+purposecntnew+"'>&nbsp;";
		purposetr	+=			"<img src='/PETTY_CASH/images/remove.png' 	id='btnviewrem_"+purposecntnew+"' 	title='Remove Purpose' 	class='smallimgbuttons rempurpose tooltips'	data-cnt='"+purposecntnew+"'>";
		purposetr	+=		"</td>";
		purposetr	+=	"</tr>";
		
		$("#tblpurpose tbody").append(purposetr);
		$("#hdnpurposecnt").val(purposecntnew);
		getdropdown();
		$( ".tooltips" ).tooltip();
	});
	$("tr td").on("click",".rempurpose",function(){
		var cnt	=	$(this).attr("data-cnt");
		$("#tblpurpose #trpurpose_"+cnt).remove();
	});
	$("tr td").on("change",".selpurposes",function(){
		var purposeval	=	$(this).val();
		var purposecnt	=	$('#hdnpurposecnt').val();
		var purposecntcur	=	$(this).attr("data-cnt");
		var a = 1;
		duppurposeerr = "";
		for(a; a <= purposecnt; a++)
		{
			if(purposecntcur != a)
			{	
				if($('#selpurpose_'+a).val() == purposeval && purposeval !=  '')
				{
					MessageType.infoMsg("Invalid choice. Duplicate purpose.");
					$(this).val('');
					
					duppurposeerr = "1";
					return;
				}
			}
		}
		$("#hidremark_"+purposecntcur).val("");
		if(duppurposeerr != "1" && purposeval != "")
		{
			viewremarks(purposeval,purposecntcur);
		}
	});
	$("#divremarks").on("click",".remarks", function(){
		var curcnt = $(this).attr("data-curcnt");
		var sList = "";
		$('input[type=checkbox]').each(function () {
			var sThisVal = (this.checked ? "1" : "0");
			if(sThisVal == 1)
			{
				sList += (sList=="" ? this.name : "," + this.name);
			}
		});
		$("#hidremark_"+curcnt).val(sList);
	});
	$("#divremarks").on("change","#txtmisc",function(){
		var curcnt 		= $(this).attr("data-curcnt");
		var remarksmisc	=  $(this).val();
		$("#hidremark_"+curcnt).val(remarksmisc);
	});
	
	$("tr td").on("click",".viewrem",function(){
		var curcnt 		= $(this).attr("data-curcnt");
		var purposeval	= $("#selpurpose_"+curcnt).val();
		if(purposeval != "")
		{
			viewremarks(purposeval,curcnt);
		}
		else
		{
			MessageType.infoMsg("Please select purpose.");
		}
	});
	$("#btnsave").click(function(){
		var purposecnt	=	$("#hdnpurposecnt").val();
		var a 			= 	1;
		var purpose, remarks, amount, P_errfound = "", R_errfound = "", A_errfound = "";
		for(a; a <= purposecnt; a++)
		{
			purpose = $("#selpurpose_"+a).val();
			remarks = $("#hidremark_"+a).val();
			if(purpose == "")
			{
				P_errfound = "- Please select purpose.<br>";
				$("#selpurpose_"+a).addClass("errpurpose");
			}
			else
			{
				$("#selpurpose_"+a).removeClass("errpurpose");
			}
			if(remarks == "")
			{
				R_errfound = " - Please click the remarks icon to select remarks.<br>";
				$("#btnviewrem_"+a).addClass("errpurpose");
			}
			else
			{
				$("#btnviewrem_"+a).removeClass("errpurpose");
			}
		}
		amount = $("#txtamount").val();
		if(amount == "" || amount == 0)
		{
			A_errfound = " - Please input a valid amount.<br>";
			$("#txtamount").addClass("errpurpose");
		}
		else
		{
			$("#txtamount").removeClass("errpurpose");
		}
		if(P_errfound == "" && R_errfound == "" && A_errfound == "")
		{
			MessageType.confirmmsg(saveCAreq,"Do you want to save this Cash Advance request?","")
		}
		else 
		{
			MessageType.infoMsg(P_errfound + R_errfound + A_errfound);
		}
	});
	$("#btnupdate").click(function(){
		var purposecnt	=	$("#hdnpurposecnt").val();
		var a 			= 	1;
		var purpose, remarks, amount, P_errfound = "", R_errfound = "", A_errfound = "";
		for(a; a <= purposecnt; a++)
		{
			purpose = $("#selpurpose_"+a).val();
			remarks = $("#hidremark_"+a).val();
			if(purpose == "")
			{
				P_errfound = " - Please select purpose.<br>";
				$("#selpurpose_"+a).addClass("errpurpose");
			}
			else
			{
				$("#selpurpose_"+a).removeClass("errpurpose");
			}
			if(remarks == "")
			{
				R_errfound = " - Please click the remarks icon to select remarks.";
				$("#btnviewrem_"+a).addClass("errpurpose");
			}
			else
			{
				$("#btnviewrem_"+a).removeClass("errpurpose");
			}
		}
		amount = $("#txtamount").val();
		if(amount == "" || amount == 0)
		{
			A_errfound = " - Please input a valid amount.";
			$("#txtamount").addClass("errpurpose");
		}
		else
		{
			$("#txtamount").removeClass("errpurpose");
		}
		if(P_errfound == "" && R_errfound == "" && A_errfound == "")
		{
			MessageType.confirmmsg(updateCAreq,"Do you want to update this Cash Advance request?","");
		}
		else 
		{
			MessageType.infoMsg(P_errfound + R_errfound + A_errfound);
		}
	});
	$("#txtamount").keyup(function(){
		var caamount	=	$(this).val();
		var id			=	this.id;
		if(checkamount(caamount))
		{
			inputAmount.getnumbersOnly(caamount,id)
		}
	});
	$("#btncancel").click(function(){
		var purposecnt	=	$("#hdnpurposecnt").val();
		for(purposecnt; purposecnt != 1; purposecnt--)
		{
			$("#trpurpose_"+purposecnt).remove();
		}
		$("#txtamount").val("");
		$("#hidremark_1").val("");
		$("#selpurpose_1").val("");
		$("#divCAreq").hide();
		$("#hdnpurposecnt").val("1");
		$("#btnupdate").hide();
		$("#btnsave").show();
		
		$("#txtamount").removeClass("errpurpose");
		$(".selpurposes").removeClass("errpurpose");
		$(".viewrem").removeClass("errpurpose");
		enablebuttons();
	});
	$("#btnsearch").click(function(){
		var CAnum	=	$("#txtCAnum").val();
		var date	=	$("#txtdate").val();
		if($("#disablewhenediting").val() == "0")
		{
			getCAlist(CAnum,date);
		}
	});
	$("#txtCAnum").keyup(function(e){ 
	    var code = e.which;
	    if(code==13)e.preventDefault();
	    if(code==13){
	       getCAlist($(this).val());
	    }
	});
	$("#txtdate").keyup(function(e){ 
	    var code = e.which;
	    if(code==13)e.preventDefault();
	    if(code==13){
	       getCAlist("",$(this).val());
	    }
	});
	$("tr td").on("click",".ca-dtls-lnk",function(){
		var cano	=	$(this).attr("data-cano");
		if($("#disablewhenediting").val() == "0")
		{
			$.ajax({
				url			:	'request.php?action=VIEWCADTLS&CANO='+cano,
				beforeSend	:	function()
							{
								$('#divloader').dialog("open");
							},
				success		:	function(response)
							{
								$('#divparticulars').html(response);
								$('#divloader').dialog("close");
							}
		  		});
		}
	});
	$("tr td").on("click",".btnsubmit",function(){
		var cano		=	$(this).attr("data-cano");
		var caamount	=	$(this).attr("data-caamt");
		if($("#disablewhenediting").val() == "0")
		{
			if(checkamount(caamount))
			{
				if(checkliquidation(caamount))
				{
					MessageType.confirmmsg(submitCA,"Do you want to submit this Cash Advance request?",cano);
				}
			}
		}
	});
	$("tr td").on("click",".btndelete",function(){
		var cano	=	$(this).attr("data-cano");
		if($("#disablewhenediting").val() == "0")
		{
			MessageType.confirmmsg(deleteCA,"Do you want to cancel this Cash Advance request?",cano)
		}
	});
	$("tr td").on("click",".btnedit",function(evt){
		if($("#disablewhenediting").val() == "0")
		{
			var cano	=	$(this).attr("data-cano");
			var department = $("#hdndep").val();
			evt.stopPropagation();
			evt.preventDefault();
			$('#divCAreq').show();
			$('#btnupdate').show();
			$('#btnsave').hide();
			$.ajax({
				url			:	'request.php?action=EDITCA&CANO='+cano,
				beforeSend	:	function()
							{
								$('#divloader').dialog("open");
							},
				success		:	function(response)
							{
								$('#divdebug').html(response);
								$('#divloader').dialog("close");
								disablebtns();
								$("#trcanumber").show();
								$("#lblcanumber").text(cano);
							}
			});
		}
	});
});

function submitCA(cano)
{
	$.ajax({
		url			:	'request.php?action=SUBMITCA&CANO='+cano,
		beforeSend	:	function()
					{
						$('#divloader').dialog("open");
					},
		success		:	function(response)
					{
						$('#divdebug').html(response);
						$('#divloader').dialog("close");
					}
  		});
}
function deleteCA(cano)
{
	$.ajax({
		url			:	'request.php?action=DELETECA&CANO='+cano,
		beforeSend	:	function()
					{
						$('#divloader').dialog("open");
					},
		success		:	function(response)
					{
						$('#divdebug').html(response);
						$('#divloader').dialog("close");
					}
  		});
}
function getdropdown()
{
	var opt,txt,cnt = $('#hdnpurposecnt').val();
	$('#selpurpose_1 option').each(function(){
		opt = $(this).val();
		txt = $(this).text();
		$('#selpurpose_'+cnt).append($("<option />").attr("value", opt).text(txt)); 
	});
}
function viewremarks(purposeval,purposecntcur)
{
	$.ajax({
			url			:	'request.php?action=LOADREMARKS&particular='+purposeval+'&purposecntcur='+purposecntcur,
			beforeSend	:	function()
						{
							$('#divloader').dialog("open");
						},
			success		:	function(response)
						{
							$('#divremarks').html(response);
							$('#divremarks').dialog('open');
							$('#divloader').dialog("close");
						}
	  		});
}

function saveCAreq(){
	var frmCAreq	=	$("#frmCAreq").serialize();
	$.ajax({
		data		:frmCAreq,
		type		:"POST",
		url			:"request.php?action=SAVEREQUEST",
		beforeSend	:function(){
			$("#divloader").dialog("open");
		},
		success		:function(response){
			$("#divdebug").html(response);
			$("#divloader").dialog("close");
		}
	});
}
function updateCAreq(){
	var frmCAreq	=	$("#frmCAreq").serialize();
	var cano		=	$("#hdncano").val();
	$.ajax({
		data		:frmCAreq,
		type		:"POST",
		url			:"request.php?action=UPDATEREQUEST&CANO="+cano,
		beforeSend	:function(){
			$("#divloader").dialog("open");
		},
		success		:function(response){
			$("#divdebug").html(response);
			$("#divloader").dialog("close");
		}
	});
}
function getCAlist(CAnum,date)
{
	$.ajax({
		url			:"request.php?action=GETCALIST&CANUM="+CAnum+"&DATE="+date,
		beforeSend	:function(){
			$("#divloader").dialog("open");
		},
		success		:function(response){
			$("#divCAlist").html(response);
			$("#divloader").dialog("close");
			$(".tooltips").tooltip();
			$('#btncancel').trigger('click');
			$("#tblCAlists").tablesorter(); 
		}
	});
}
function disablebtns()
{
	$("#txtCAnum").attr("disabled","disabled");
	$("#txtdate").attr("disabled","disabled");
	$("#disablewhenediting").val("1");
	$(".ca-dtls-lnk").attr("disabled","disabled");
}
function enablebuttons()
{
	$("#txtCAnum").removeAttr("disabled");
	$("#txtdate").removeAttr("disabled");
	$("#btnsearch").removeAttr("disabled");
	$("#disablewhenediting").val("0");
}
function checkamount(caamount)
{
	var a				=	1;
	var validamount 	= 	true;	
	var hdndep			=	$('#hdndep').val();
	var userlevel		=	$('#hdnuserlevel').val();
	var hidregamount 	= + $('#hdnuptotodaysamt').val();
	var liquicnt		=	$('#hdnliquidated4todaycnt').val();
	if(hdndep == "WH")
	{
		var txtspecialchild	=	$("#txtspecialchild").val();
		var cnt				=	$("#hdnpurposecnt").val();
		var hdnwhdays		=	$('#hdnwhdays').val();
		var foundfreight	=	false;
		if(hdnwhdays <= 2)
		{
			hidregamount	= + caamount + hidregamount;
		}
		for(a; a <= cnt; a++)
		{
			if($('#selpurpose_'+a).val() == '0015')
			{
				foundfreight = true;
			}
		}
		if(hidregamount > 3000 && (userlevel != "Manager")  && foundfreight == false && txtspecialchild == "N")
		{
			MessageType.infoMsg("Amount exceeds 3, 000.00 for regular cash advance in two days.");
			$('#txtamount').val('');
			validamount 	= false;
			return;
		}
		if(hidregamount > 6000 && (userlevel != "Manager")  && foundfreight == false && txtspecialchild == "Y")
		{
			MessageType.infoMsg("Amount exceeds 6, 000.00 for regular cash advance in two days.");
			$('#txtamount').val('');
			validamount 	= false;
			return;
		}
		if(hidregamount > 5000 && (userlevel == "Manager")  && foundfreight == false)
		{
			MessageType.infoMsg("Amount exceeds 5, 000.00 for regular cash advance in two days.");
			$('#txtamount').val('');
			validamount 	= false;
			return;
		}
		if(hidregamount > 5000 && (userlevel != "Manager") && foundfreight == true)
		{
			MessageType.infoMsg("Amount exceeds 5, 000.00 for regular cash advance with Trucking Delivery of Goods purpose in two days.");
			$('#txtamount').val('');
			validamount 	= false;
			return;
		}
	}
	else
	{
		if(caamount > 1500 && (userlevel != "Manager"))
		{
			MessageType.infoMsg("Amount exceeds 1, 500.00 for regular cash advance per transaction.");
			$('#txtamount').val('');
			validamount 	= false;
			return;
		}
		if(caamount > 3000 && (userlevel == "Manager"))
		{
			MessageType.infoMsg("Amount exceeds 3, 000.00 for regular cash advance per transaction.");
			$('#txtamount').val('');
			validamount 	= false;
			return;
		}
				
		if(liquicnt <= 1)
		{
				caamount		=	Number(caamount);
				hidregamount	=	Number(hidregamount);
				$forlimit		=	hidregamount	+	caamount;
				
			if($forlimit > 4500)
			{
				MessageType.infoMsg("Amount exceeds 4, 500.00 for regular cash advance per day.");
				$("#txtamount").val("");
				validamount 	= false;
			}
		}
	}
	
	return validamount;	
}
function checkliquidation()
{
	var hdndep 				= $("#hdndep").val();
	var hdnunliquidatedamt	= $("#hdnunliquidatedamt").val();
	var whunliquidayscnt	= $('#hdnwhdays').val();
	var valid 				= true;
	if(hdndep == "WH")
	{
		if(whunliquidayscnt > 2)
		{
			if(hdnunliquidatedamt > 0)
			{
				var unliquiCAs		=	$("#hdnunliquiCAs").val();
				var arrUnliquiCAs	=	unliquiCAs.split(","); 
				for(var a = 0; a < arrUnliquiCAs.length; a++)
				{
					$("#td"+arrUnliquiCAs[a]).removeClass("tr-ca-list-dtls");
					$("#td"+arrUnliquiCAs[a]).addClass("errpurpose");
				}
				MessageType.infoMsg("Please liquidate your previous Cash Advance.");
				valid = false;
			}
		}
	}
	else
	{
		if(hdnunliquidatedamt > 0)
		{
			var unliquiCAs		=	$("#hdnunliquiCAs").val();
			var arrUnliquiCAs	=	unliquiCAs.split(","); 
			for(var a = 0; a < arrUnliquiCAs.length; a++)
			{
				$("#td"+arrUnliquiCAs[a]).removeClass("tr-ca-list-dtls");
				$("#td"+arrUnliquiCAs[a]).addClass("errpurpose");
			}
			MessageType.infoMsg("Please liquidate your previous Cash Advance.");
			valid = false;
		}
	}
	return valid;
}