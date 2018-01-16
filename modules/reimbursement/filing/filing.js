$("document").ready(function(){
	$("#btnnew").click(function(){
		if($("#disablewhenediting").val() == "0")
		{
			$("#divreimbursement").show("fade");
			$("#trreimnumber").hide();
			$("#lblreimnumber").text("");
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
		purposetr	+=			"<input type='hidden' id='hidval_"+purposecntnew+"' name='hidval_"+purposecntnew+"'>";
		purposetr	+=			"<input type='hidden' id='hidname_"+purposecntnew+"' name='hidname_"+purposecntnew+"'>";
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
					MessageType.infoMsg('Invalid choice. Duplicate purpose.');
					$(this).val('');
					$("#hidval_"+purposecntcur).val('');
					$("#hidname_"+purposecntcur).val('');
					
					duppurposeerr = "1";
					return;
				}
			}
		}
		$("#hidval_"+purposecntcur).val("");
		$("#hidname_"+purposecntcur).val("");
		if(duppurposeerr != "1" && purposeval != "")
		{
			viewremarks(purposeval,purposecntcur);
		}
	});
	$("#divremarks").on("keyup",".remarksamt",function(evt){
		var remarksamt	=	$(this).val();
		inputAmount.getnumbersOnly(remarksamt,this.id);
		sumupremarksamt(this.name);
	});
	$("tr td").on("click",".viewrem",function(){
		var curcnt	=	$(this).attr("data-curcnt");
		var remarks	=	$("#hidval_"+curcnt).val();
		var purposeval	=	$("#selpurpose_"+curcnt).val();
		if(purposeval != "")
		{
			viewremarks(purposeval,curcnt,"btnview");
		}
		else
		{
			MessageType.infoMsg('Please select purpose.');
		}
	});
	$("tr td").on("click",".rempurpose",function(){
		var cnt	=	$(this).attr("data-cnt");
		$("#tblpurpose #trpurpose_"+cnt).remove();
		computetotal();
	});
	$("#btnsave").click(function(){
		if(validatereimdtls())
		{
			MessageType.confirmmsg(gosavereimbursement,"Do you want to save this reimbursement details?");
		}
	});
	$("#btncancel").click(function(){
		var purposecntold	= + $("#hdnpurposecnt").val();
		for(purposecntold ; purposecntold != 1; purposecntold--)
		{
			$("#trpurpose_"+purposecntold).remove();
		}
		$("#disablewhenediting").val("0");
		$("#hdnpurposecnt").val(1);
		$("#hidval_1").val("");
		$("#hidname_1").val("");
		$("#selpurpose_1").val("");
		$("#totalAmount").text("0.00");
		$('#divreimbursement').hide();
		$('#btnupdate').hide("fade");
		$('#btnsave').show();
	});
	$("tr td").on("click",".ca-dtls-lnk",function(){
		var trxno	=	$(this).attr("data-trxno");
		$.ajax({
			url		:	"filing.php?action=VIEWREIMBURSEMENTDTLS&TRXNO="+trxno,
			beforeSend	: function()
			{
				$('#divloader').dialog("open");
			},
			success		: function(response)
			{
				$("#divparticulars").html(response);
				$('#divparticulars').dialog("open");
				$('#divloader').dialog("close");
			}
		});
	});
	$("tr td").on("click",".btnsubmit",function(){
		var reimno		=	$(this).attr("data-reimno");
		var reimamount	=	$(this).attr("data-reimamt");
		if($("#disablewhenediting").val() == "0")
		{
			MessageType.confirmmsg(submitREIM,"Do you want to submit this reimbursement request?",reimno);
		}
	});
	$("tr td").on("click",".btndelete",function(){
		var reimno	=	$(this).attr("data-reimno");
		if($("#disablewhenediting").val() == "0")
		{
			MessageType.confirmmsg(deleteREIM,"Do you want to cancel this reimbursment request?",reimno)
		}
	});
	$("tr td").on("click",".btnedit",function(evt){
		var reimno	=	$(this).attr("data-reimno");
		var reimamt	=	$(this).attr("data-reimamt");
		evt.stopPropagation();
		evt.preventDefault();
		$('#divreimbursement').show();
		$('#btnupdate').show();
		$('#btnsave').hide();
		if($("#disablewhenediting").val() == "0")
		{
			$.ajax({
					url			:	'filing.php?action=EDITREIM&REIMNO='+reimno,
					beforeSend	:	function()
								{
									$('#divloader').dialog("open");
								},
					success		:	function(response)
								{
									$('#divdebug').html(response);
									$('#divloader').dialog("close");
									disablebtns();
									$("#trreimnumber").show();
									$("#lblreimnumber").text(reimno);
									$("#totalAmount").text(inputAmount.getNumberWithCommas(reimamt));
								}
				});
		}
	});
	$("#btnsearch").click(function(){
		var reimnum	=	$("#txtreimnum").val();
		var date	=	$("#txtdate").val();
		if($("#disablewhenediting").val() == "0")
		{
			getREIMlist(reimnum,date);
		}
	});
	$("#txtreimnum").keyup(function(e){ 
	    var code = e.which;
	    if(code==13)e.preventDefault();
	    if(code==13){
	       getREIMlist($(this).val());
	    }
	});
	$("#txtdate").keyup(function(e){ 
	    var code = e.which;
	    if(code==13)e.preventDefault();
	    if(code==13){
	       getREIMlist("",$(this).val());
	    }
	});
	$("#btnupdate").click(function(){
		if(validatereimdtls())
		{
			MessageType.confirmmsg(goupdatereimbursement,"Do you want to update this reimbursement details?");
		}
	});
});
function getdropdown()
{
	var opt,txt,cnt = $('#hdnpurposecnt').val();
	$('#selpurpose_1 option').each(function(){
		opt = $(this).val();
		txt = $(this).text();
		$('#selpurpose_'+cnt).append($("<option />").attr("value", opt).text(txt)); 
	});
}
function viewremarks(purposeval,purposecntcur,btnclicked)
{
	$.ajax({
			url			:	'filing.php?action=LOADREMARKS&particular='+purposeval+'&purposecntcur='+purposecntcur+"&BTNCLICKED="+btnclicked,
			beforeSend	:	function()
						{
							$('#divloader').dialog("open");
						},
			success		:	function(response)
						{
							$('#divremarks').html(response);
							$('#divremarks').data('curcnt', purposecntcur).dialog('open');
							$('#divloader').dialog("close");
						}
	  		});
}
function sumupremarksamt()
{
	var cnt		=	$('#hdnremcnt').val();
	var	a		=	1;
	var value	=	0;
	var sum		=	0;
	var total	= +	$('#totalAmount').text().replace(/,/g, '');
	for(a; a < cnt; a++ )
	{
		value	= +	$('#val_'+a).val();
		sum		+=	value;	
	}
	if(isNaN(sum))
	{
		$('#Ptotal').val(0);
	}
	else
	{
		$('#Ptotal').val(inputAmount.getNumberWithCommas(sum.toFixed(2)));
	}
	
}
function saveTotal(purposecurcnt)
{
	var Ptotal			=	+ $("#Ptotal").val().replace(/,/g, '');
	if(Ptotal != 0)
	{
		savetmpremarks();
		computetotal();
	}
	else
	{
		MessageType.infoMsg('Total amount must not be zero(0).');
	}
}
function savetmpremarks()
{
	var curcnt		=	$('#divremarks').data('curcnt');
	var hdnremcnt 	= 	$("#hdnremcnt").val();
	var a			=	1;
	var tmpremname	=	"";
	var tmpremamt	=	"";
	if($("#selpurpose_"+curcnt).val() == "0005")
	{
		if($("#name_1").val() == "" || $("#val_1").val() == "")
		{
			MessageType.infoMsg('Miscellaneous description must not be empty.');
			return;
		}
	}
	for(a; a < hdnremcnt; a++)
	{
		tmpremamt 	+= ","+$("#val_"+a).val();
		tmpremname 	+= ","+$("#name_"+a).val();
	}
	tmpremamt = tmpremamt.substring(1);
	tmpremname= tmpremname.substring(1);
	$("#hidval_"+curcnt).val(tmpremamt);
	$("#hidname_"+curcnt).val(tmpremname);
	$("#divremarks").dialog("close");
}
function computetotal()
{
	var cnt	=	$('#hdnpurposecnt').val();
	var a	=	1;
	var amt	=	0;
	var amts= 	[];
	var b;
	var total = 0;
	for(a; a <= cnt; a++)
	{
		amt		=	$('#hidval_'+a).val();
		if(amt != undefined)
		{
			amts	=	amt.split(',');
			for( b = 0; b < amts.length; b++)
			{
				total += Number(amts[b]);
			}
		}
	}
	total	=	total.toFixed(2);
	
	$("#totalAmount").text(inputAmount.getNumberWithCommas(total));
}
function gosavereimbursement()
{
	var frmreimbursement 	= 	$("#frmreimbursement").serialize();
	var totalAmount			=	$("#totalAmount").text().replace(/,/g, '');
	$.ajax({
		data		: frmreimbursement,
		type		: "POST",
		url			: "filing.php?action=SAVEREIMBURSEMENT&TOTALAMOUNT="+totalAmount,
		beforeSend	: function()
		{
			$('#divloader').dialog("open");
		},
		success		: function(response)
		{
			$("#divdebug").html(response);
			$('#divloader').dialog("close");
			$('#btncancel').trigger("click");
		}
	});
}
function getREIMlist(REIMnum,date)
{
	$.ajax({
		url			:"filing.php?action=GETREIMLIST&REIMNUM="+REIMnum+"&DATE="+date,
		beforeSend	:function(){
			$("#divloader").dialog("open");
		},
		success		:function(response){
			$("#divREIMlist").html(response);
			$("#divloader").dialog("close");
			$(".tooltips").tooltip();
			$("#tblREIMlists").tablesorter(); 
		}
	});
}
function submitREIM(reimno)
{
	$.ajax({
		url			:	'filing.php?action=SUBMITREIM&REIMNO='+reimno,
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
function deleteREIM(reimno)
{
	$.ajax({
		url			:	'filing.php?action=DELETEREIM&REIMNO='+reimno,
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
function disablebtns()
{
	$("#txtreimnum").attr("disabled","disabled");
	$("#txtdate").attr("disabled","disabled");
	$("#disablewhenediting").val("1");
	$(".ca-dtls-lnk").attr("disabled","disabled");
}
function validatereimdtls()
{
	var count		=	$("#hdnpurposecnt").val();
	var a			=	1;
	var valid		=	true;
	var remarksval	= 	selpurpose = R_errmsg = P_errmsg = reimerror = "";
	for(a; a <= count; a++)
	{
		remarksval	=	$("#hidval_"+a).val();
		if(remarksval != undefined)
		{
			remarksval = remarksval.replace(/,/g, '');
			if(remarksval == "")
			{
				$("#btnviewrem_"+a).addClass("errpurpose");
				R_errmsg	=	" - Please click remarks icon to indicate values for the remarks. <br>";
				valid = false;
			}
			else
			{
				$("#btnviewrem_"+a).removeClass("errpurpose");
			}
		}
		selpurpose	=	$("#selpurpose_"+a).val();
		if(selpurpose == "")
		{
			$("#selpurpose_"+a).addClass("errpurpose");
			P_errmsg	=	" - Please select purpose. <br>";
			valid = false;
		}
		else
		{
			$("#selpurpose_"+a).removeClass("errpurpose");
		}
	}
	if(valid == false)
	{
		MessageType.infoMsg(P_errmsg + R_errmsg + reimerror);
	}
	return valid;
}
function goupdatereimbursement()
{
	var frmreimbursement 	= 	$("#frmreimbursement").serialize();
	var totalAmount			=	$("#totalAmount").text().replace(/,/g, '');
	var	lblreimnumber		=	$("#lblreimnumber").text();
	$.ajax({
		data		: frmreimbursement,
		type		: "POST",
		url			: "filing.php?action=UPDATEREIMBURSEMENT&TOTALAMOUNT="+totalAmount+"&LBLREIMNUMBER="+lblreimnumber,
		beforeSend	: function()
		{
			$('#divloader').dialog("open");
		},
		success		: function(response)
		{
			$("#divdebug").html(response);
			$('#divloader').dialog("close");
			$('#btncancel').trigger("click");
		}
	});
}