var CANO	=	"";
$("document").ready(function(){
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
	$("#btnsearch").click(function(){
		var CAnum	=	$("#txtCAnum").val();
		var date	=	$("#txtdate").val();
		getCAlist(CAnum,date);
	});
	
	$("tr td").on("click",".ca-dtls-lnk",function(){
		var cano	=	$(this).attr("data-cano");
		$.ajax({
			url			:	'reimbursement.php?action=VIEWCADTLS&CANO='+cano,
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
	});
	$("tr td").on("click",".btnreimburse",function(){
		var cano	=	$(this).attr("data-cano");
		var amount	=	$(this).attr("data-reimamt");
		$("#REIM").text(inputAmount.getNumberWithCommas(amount));
		$("#REMamt").text(inputAmount.getNumberWithCommas(amount));
		$("#divreimbursement").dialog("open");
		CANO	=	cano;
	});
	$("#btnaddpurpose").click(function(){
		if($("#REMamt").text() == "0.00")
		{
			MessageType.infoMsg('Remaining amount is already zero(0).');
		}
		else
		{
			var purposecntold	= + $("#hdnpurposecnt").val();
			var purposecntnew	=	purposecntold + 1;
			var purposetr		=	"";
			purposetr	=	"<tr id='trpurpose_"+purposecntnew+"'>";
			purposetr	+=		"<td class='label_text'><input type='hidden' id='hidremark_"+purposecntnew+"' name='hidremark_"+purposecntnew+"'></td>";
			purposetr	+=		"<td class='label_text'>";
			purposetr	+=			":<select name='selpurpose_"+purposecntnew+"' id='selpurpose_"+purposecntnew+"' class='input_text curved5px selpurposes' data-cnt='"+purposecntnew+"'>";
			purposetr	+=			"</select>";
			purposetr	+=			"<input type='hidden' id='hidval_"+purposecntnew+"' name='hidval_"+purposecntnew+"'>";
			purposetr	+=			"<input type='hidden' id='hidname_"+purposecntnew+"' name='hidname_"+purposecntnew+"'>";
			purposetr	+=		"</td>";
			purposetr	+=		"<td>";
			purposetr	+=			"<img src='/PETTY_CASH/images/viewrem.png' 	id='btnviewrem_"+purposecntnew+"' 	title='View Remarks' 	class='smallimgbuttons viewrem tooltips' 	data-curcnt='"+purposecntnew+"'>";
			purposetr	+=			"<img src='/PETTY_CASH/images/remove.png' 	id='btnviewrem_"+purposecntnew+"' 	title='Remove Purpose' 	class='smallimgbuttons rempurpose tooltips'	data-cnt='"+purposecntnew+"'>";
			purposetr	+=		"</td>";
			purposetr	+=	"</tr>";
			
			$("#tblpurpose tbody").append(purposetr);
			$("#hdnpurposecnt").val(purposecntnew);
			getdropdown();
			$( ".tooltips" ).tooltip();
		}
		
	});
	$("tr td").on("click",".rempurpose",function(){
		var cnt	=	$(this).attr("data-cnt");
		$("#tblpurpose #trpurpose_"+cnt).remove();
		computetotal();
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
});
function getCAlist(CAnum,date)
{
	$.ajax({
		url			:"reimbursement.php?action=GETCALIST&CANUM="+CAnum+"&DATE="+date,
		beforeSend	:function(){
			$("#divloader").dialog("open");
		},
		success		:function(response){
			$("#divCAlist").html(response);
			$("#divloader").dialog("close");
			$(".tooltips").tooltip();
			$("#tblCAlists").tablesorter(); 
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
function viewremarks(purposeval,purposecntcur,btnclicked)
{
	$.ajax({
			url			:	'reimbursement.php?action=LOADREMARKS&particular='+purposeval+'&purposecntcur='+purposecntcur+"&BTNCLICKED="+btnclicked,
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
function closeparticulars()
{
	var purposecntold	= + $("#hdnpurposecnt").val();
	for(purposecntold ; purposecntold != 1; purposecntold--)
	{
		$("#trpurpose_"+purposecntold).remove();
	}
	$("#hdnpurposecnt").val(1);
	$("#hidval_1").val("");
	$("#hidname_1").val("");
	$("#selpurpose_1").val("");
	$("#REIM").text("");
	$("#totalAmount").text("0.00");
	$("#REMamt").text("");
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
	var reim	= +	$("#REIM").text().replace(/,/g, '');
	var rem		=	0;
		rem		=	reim - total;	
		total	=	total.toFixed(2);
		rem		=	rem.toFixed(2);
	
	$("#totalAmount").text(inputAmount.getNumberWithCommas(total));
	$("#REMamt").text(inputAmount.getNumberWithCommas(rem));
}
function sumupremarksamt(nme)
{
	var cnt		=	$('#hdnremcnt').val();
	var	a		=	1;
	var value	=	0;
	var sum		=	0;
	var reim	= +	$('#REIM').text().replace(/,/g, '');
	var total	= +	$('#totalAmount').text().replace(/,/g, '');
	for(a; a < cnt; a++ )
	{
		value	= +	$('#val_'+a).val();
		sum		+=	value;	
	}
	
	if(nme != '')
	{		
		var Ptotal			=	+ $("#Ptotal").val().replace(/,/g, '');
		var hdnpurposecnt	=	$("#hdnpurposecnt").val();
		var purposecurcnt	= 	$("#divremarks").data('curcnt');	
		var a	=	1;
		var amt	=	0;
		var amts= 	[];
		var b;
		var total = 0;
		var overalltotal	=	0;
		for(var a = 1; a <= hdnpurposecnt; a++)
		{
			if(purposecurcnt != a)
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
		}
		overalltotal = sum + total;
		if((overalltotal) <= reim)
		{
			$('#Ptotal').val(sum);;
		}
		else
		{
			MessageType.infoMsg('Total must be less than or equal to the remaining amount.');
			$('#'+nme).val('');
			sum = 0;
			a 	= 1;
			for(a; a < cnt; a++ )
			{
				value	= +	$('#val_'+a).val();
				sum		+=	value;	
			}
			$('#Ptotal').val(sum);
		}
	}
	else
	{
		$('#Ptotal').val(sum);
	}
}
function savereimbursement()
{
	var count		=	$("#hdnpurposecnt").val();
	var a			=	1;
	var valid		=	true;
	var REMamt		=	$("#REMamt").text();
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
	if(REMamt != "0.00")
	{
		reimerror = " - Reimbursement amount is not equal to Total amount. <br>";
		$("#totalAmount").addClass("errpurpose");
		$("#REIM").addClass("errpurpose");
		valid = false;
	}
	else
	{
		$("#totalAmount").removeClass("errpurpose");
		$("#REIM").removeClass("errpurpose");
	}
	if(valid)
	{
		MessageType.confirmmsg(gosavereimbursement,"Do you want to save this reimbursement details?");
	}
	else
	{
		MessageType.infoMsg(P_errmsg + R_errmsg + reimerror);
	}
}
function gosavereimbursement()
{
	var frmreimbursement = $("#frmreimbursement").serialize();
	var REIM	=	$("#REIM").text().replace(/,/g, '');
	$.ajax({
		data		: frmreimbursement,
		type		: "POST",
		url			: "reimbursement.php?action=SAVEREIMBURSEMENT&CANO="+CANO+"&REIM="+REIM,
		beforeSend	: function()
		{
			$('#divloader').dialog("open");
		},
		success		: function(response)
		{
			$("#divdebug").html(response);
			$('#divloader').dialog("close");
			$('#divreimbursement').dialog("close");
		}
	});
}