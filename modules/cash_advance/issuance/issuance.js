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
			url			:	'issuance.php?action=VIEWCADTLS&CANO='+cano,
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
	$("tr td").on("click",".btndenomination",function(){
		var cano	=	$(this).attr("data-cano");
		var caamt	=	$(this).attr("data-caamt");
		$('#divdenomination').dialog('open');
		$("#tdCANO").text(cano);
		$("#tdCAamount").text(caamt);
		$("#hdncaamount").val(caamt);
	});
	$(".denomination").keyup(function(){
		var multiplier 	= + $(this).attr("data-multiplier");
		var curcount	= (this.name).substring(6,7);
		var amount		= multiplier * $(this).val();
		var totalamount	=	0;
		inputAmount.getnumbersOnly(this.value,this.id);
		amount		= amount.toFixed(2);
		if(isNaN(amount))
		{
			$("#am"+curcount).text("0.00");
		}
		else
		{
			amount		= inputAmount.getNumberWithCommas(amount);
			$("#am"+curcount).text(amount);
		}
		totalamount	=	(inputAmount.sumupByLoop(7,"am","text","")).toFixed(2);
		if(isNaN(totalamount))
		{
			$("#txttotal").val(0);
		}
		else
		{
			$("#txttotal").val(inputAmount.getNumberWithCommas(totalamount));
		}
	});
});
function getCAlist(CAnum,date)
{
	$.ajax({
		url			:"issuance.php?action=GETCALIST&CANUM="+CAnum+"&DATE="+date,
		beforeSend	:function(){
			$("#divloader").dialog("open");
		},
		success		:function(response){
			$("#divCAlist").html(response);
			$("#divloader").dialog("close");
			$(".tooltips").tooltip();
			$(".btnissue").hide();
			$("#tblCAlists").tablesorter(); 
		}
	});
}
function clearForm()
{
    $(":input", frmdenomination).each(function()
    {
	    var type = this.type;
	    var tag = this.tagName.toLowerCase();
    	if (type == 'text')
    	{
    		this.value = "";
    	}
    });
	$('#am1').text('0.00');
	$('#am2').text('0.00');
	$('#am3').text('0.00');
	$('#am4').text('0.00');
	$('#am5').text('0.00');
	$('#am6').text('0.00');
	$('#am7').text('0.00');
	$('#txttotal').val('0.00');
}
function saveDenomination()
{
	var frmdenomination = $("#frmdenomination").serialize();
	var CANO			= $("#tdCANO").text();
	$.ajax({
		type		:	'POST',
		data		:	frmdenomination,
		url			:	'issuance.php?action=SAVEDENOMINATION&CANO='+CANO,
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