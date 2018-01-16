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
			url			:	'liquidation.php?action=VIEWCADTLS&CANO='+cano,
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
	$("tr td").on("click",".btnliquidate",function(){
		var cano	=	$(this).attr("data-cano");
		var amount	=	$(this).attr("data-amt");
		$.ajax({
			url			:	'liquidation.php?action=LIQUIDATE&CANO='+cano+'&CAAMOUNT='+amount,
			beforeSend	:	function()
						{
							$('#divloader').dialog("open");
						},
			success		:	function(response)
						{
							$('#divliquidationdtls').html(response);
							$('#divliquidationdtls').dialog("open");
							$(".tooltips").tooltip();
							$('#divloader').dialog("close");
						}
	  		});
	});
	$("#divliquidationdtls").on("keyup",".txtnewamount",function(evt){
		var newamount	=	$(this).val();
		var count		=	$("#hidcnt").val();
		var totamount	=	0,totamountO	=	0;
		var CAamount	=	+ $("#tdCAamount").text();
		var newReimAmt	=	0,newDisbAmt = 0, difference = 0;
		inputAmount.getnumbersOnly(newamount,this.id);
		totamountO = inputAmount.sumupByLoop(count-1,"txtNamount_","val","none");
		totamount	=	totamountO.toFixed(2);
		if(isNaN(totamount))
		{
			$("#tdTOTamount").text(0);
			$("#txtTNamount").val(0);
			$("#Rtamount").text("0.00");
			$("#hidreturn").val("");
			$("#Rbamount").text("0.00");
			$("#hidreimburse").val("");
		}
		else
		{
			$("#tdTOTamount").text(inputAmount.getNumberWithCommas(totamount));
			$("#txtTNamount").val(totamountO);
			difference = totamountO - CAamount;
			if(difference > 0)
			{
				$("#Rbamount").text(inputAmount.getNumberWithCommas(difference.toFixed(2)));
				$("#hidreimburse").val(difference);
				
				$("#Rtamount").text("0.00");
				$("#hidreturn").val("");
			}
			if(difference < 0)
			{
				$("#Rtamount").text(inputAmount.getNumberWithCommas((difference*-1).toFixed(2)));
				$("#hidreturn").val(difference*-1);
				
				$("#Rbamount").text("0.00");
				$("#hidreimburse").val("");
			}
		}
		
	});
	$("#divliquidationdtls").on("click",".chktaxup",function(evt){
		var counter	=	$(this).attr("data-curcnt");
		var remarks	=	$(this).attr("data-remarks");
		var particular = $(this).val();
		$("#lblpurpose").text(particular);
		$("#lblremark").text(remarks);
		computetaxamt(counter);
		if($('#vat_'+counter).prop('checked'))
		{
			var particular	=	$(this).val();
			$('#rdovatG_'+counter).prop('disabled',false);
			$('#rdovatS_'+counter).prop('disabled',false);
	       	$("#divtax").data('curcnt', counter).dialog('open');
		}	
		else
		{
			$('#rdovatG_'+counter).prop('disabled',true);
			$('#rdovatS_'+counter).prop('disabled',true);
			$('#rdovatG_'+counter).prop('checked',false);
			$('#rdovatS_'+counter).prop('checked',false);
			$('#comp_'+counter).val('');
			$('#vatamt_'+counter).val('');
			$('#txtcomname').val('');
			$('#txtcomadd').val('');
			$('#txtcomtin').val('');
			$("#vat_"+counter).prop('title',' ');
			$("#vat_"+counter).tooltip('destroy');
		}
	});
});
function getCAlist(CAnum,date)
{
	$.ajax({
		url			:"liquidation.php?action=GETCALIST&CANUM="+CAnum+"&DATE="+date,
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
function computetaxamt(c)
{
	var cnt		=	$('#hidcnt').val();
	var a		=	1;
	var taxamt	=	0;
	var taxtot	=	0;
	for(a; a < cnt; a++)
	{
		if($('#vat_'+a).prop('checked'))
		{
			taxamt	= +	$('#txtNamount_'+a).val();
			taxtot	+=	(taxamt * .12);
		}
		
	}
	var curtaxamt	=	$('#txtNamount_'+c).val();
	$('#vatamt_'+c).val(curtaxamt*.12);
	$('#hidvatamt').val(taxtot);	
}
function savecomdtl(cnt)
{
	var name	=	$('#txtcomname').val();
	var	add		=	$('#txtcomadd').val();
	var tin 	=	$('#txtcomtin').val();
	var vatamt	=	$('#vatamt_'+cnt).val();
	var valid	=	true;
	var compdtl	=	'';
	var errmsg	=	"";
	if(name == '')
	{
		errmsg = ' - Company name is empty. <br>';
		valid = false;
	}
	if(add == '')
	{
		errmsg += ' - Company address is empty. <br>';
		valid = false;
	}
	if(tin 	==	'')
	{
		errmsg += ' - Company tin number is empty. <br>';
		valid = false;
	}
	
	if(valid)
	{
		compdtl	=	name + ","	+	add	+	","	+	tin;
		$('#comp_'+cnt).val(compdtl);
		$('#divtax').dialog('close');
		$('#txtcomname').val('');
		$('#txtcomadd').val('');
		$('#txtcomtin').val('');
		
		if($("#vat_"+cnt).is(":checked") == true)
		{
			var vattbl	=	"<table>";
				vattbl	+=		"<tr>";
				vattbl	+=			"<td>Company Name</td>";
				vattbl	+=			"<td>:<b> "+name+"</td>";
				vattbl	+=		"</tr>";
				vattbl	+=		"<tr>";
				vattbl	+=			"<td>Company Address</td>";
				vattbl	+=			"<td>:<b> "+add+"</td>";
				vattbl	+=		"</tr>";
				vattbl	+=		"<tr>";
				vattbl	+=			"<td>TIN No.</td>";
				vattbl	+=			"<td>:<b> "+tin+"</td>";
				vattbl	+=		"</tr>";
				vattbl	+=		"<tr>";
				vattbl	+=			"<td>VAT Amount</td>";
				vattbl	+=			"<td>:<b> "+vatamt+"</td>";
				vattbl	+=		"</tr>";
				vattbl	+=	"</table>";
			$("#vat_"+cnt).addClass('tooltips');
//			$("#vat_"+cnt).prop('title', "Company Name: <b>"+name+"</b><br/>Company Address: <b>"+add+"</b><br/>TIN No.: <b>"+tin+"</b><br/>VAT Amount: <b>"+vatamt+"</b>");
			$("#vat_"+cnt).prop('title', vattbl);
			$("#vat_"+cnt).tooltip({
		    	content: function() {
		        	return $(this).attr('title');
    			}
			});
		}
	}
	else
	{
		MessageType.infoMsg(errmsg);
	}
}
function validatefields()
{
	var cnt		=	$('#hidcnt').val();
	var a = 1, amt = 0;
	var valid	=	true;
	for(a; a <= cnt; a++)
	{
		if($('#vat_'+a).is(':checked'))
		{
			if(!($('#rdovatG_'+a).prop('checked') || $('#rdovatS_'+a).prop('checked')))
			{
				valid	=	false;
				$("#dialogdtldetails"+a).addClass("errpurpose");
			}
			else
			{
				$("#dialogdtldetails"+a).removeClass("errpurpose");
			}
		}
		amt	=	$('#txtNamount_'+a).val();
		if(amt == '')
		{
			valid = false;
			$('#txtNamount_'+a).addClass('errpurpose');
		}
		else
		{
			$('#txtNamount_'+a).removeClass('errpurpose');
		}
	}
	return valid;
}
function liquidateCA()
{
	var frmliquidationdtls	=	$('#frmliquidationdtls').serialize();
	var CANO				=	$("#tdCANO").text();
	$.ajax({
		type		:	'POST',
		data		:	frmliquidationdtls,
		url			:	'liquidation.php?action=LIQUIDATECA&CANO='+CANO,
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