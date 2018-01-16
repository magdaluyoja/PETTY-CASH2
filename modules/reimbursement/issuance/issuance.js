$("document").ready(function(){
	$("tr td").on("click",".ca-dtls-lnk",function(){
		var trxno	=	$(this).attr("data-trxno");
		$.ajax({
			url		:	"issuance.php?action=VIEWREIMBURSEMENTDTLS&TRXNO="+trxno,
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
	$("#btnsearch").click(function(){
		var reimnum	=	$("#txtreimnum").val();
		var date	=	$("#txtdate").val();
		getREIMlist(reimnum,date);
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
	$("tr td").on("click",".btnissue",function(){
		var reimno	=	$(this).attr("data-reimno");
		var reimamt	=	$(this).attr("data-reimamt");
		$.ajax({
			url			:	'issuance.php?action=ISSUEREIM&REIMNO='+reimno+'&REIMAMOUNT='+reimamt,
			beforeSend	:	function()
						{
							$('#divloader').dialog("open");
						},
			success		:	function(response)
						{
							$('#divreimbursementdtls').html(response);
							$('#divreimbursementdtls').dialog("open");
							$(".tooltips").tooltip();
							$('#divloader').dialog("close");
						}
	  		});
	});
	$("#divreimbursementdtls").on("keyup",".txtnewamount",function(evt){
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
		}
		else
		{
			$("#tdTOTamount").text(inputAmount.getNumberWithCommas(totamount));
			$("#txtTNamount").val(totamountO);
		}
	});
	$("#divreimbursementdtls").on("click",".chktaxup",function(evt){
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
function getREIMlist(reimnum,date)
{
	$.ajax({
		url			:"issuance.php?action=GETREIMLIST&REIMNUM="+reimnum+"&DATE="+date,
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
//			$("#vat_"+cnt).prop('title', "[ Company Name: "+name+" ][ Company Address: "+add+" ][ TIN No.: "+tin+" ][ VAT Amount: "+vatamt+" ]");
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
function issueREIM()
{
	var frmreimbursementdtls	=	$('#frmreimbursementdtls').serialize();
	var REIMNO					=	$("#tdREIMNO").text();
	$.ajax({
		type		:	'POST',
		data		:	frmreimbursementdtls,
		url			:	'issuance.php?action=GOISSUEREIM&REIMNO='+REIMNO,
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