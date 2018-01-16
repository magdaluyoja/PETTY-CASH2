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
			url			:	'filing.php?action=VIEWCADTLS&CANO='+cano,
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
			url			:	'filing.php?action=LIQUIDATE&CANO='+cano+'&CAAMOUNT='+amount,
			beforeSend	:	function()
						{
							$('#divloader').dialog("open");
						},
			success		:	function(response)
						{
							$('#divliquidate').html(response);
							$('#divliquidate').dialog("open");
							$('#divloader').dialog("close");
						}
	  		});
	});
	$("#divliquidate").on("keyup",".txtliquiamt",function(evt){
		var liquiamt	=	$(this).val();
		var count		=	$("#hdncount").val();
		var totalamount	=	0;
		inputAmount.getnumbersOnly(this.value,this.id);
		totalamount 	= 	(inputAmount.sumupByLoop(count-1,"txtliquiamt","val")).toFixed(2)
		if(isNaN(totalamount))
		{
			$("#totalliquiamount").text(0.00);
		}
		else
		{
			$("#totalliquiamount").text(inputAmount.getNumberWithCommas(totalamount));
		}
	});
});
function getCAlist(CAnum,date)
{
	$.ajax({
		url			:"filing.php?action=GETCALIST&CANUM="+CAnum+"&DATE="+date,
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
function clear()
{
	var cnt 	= $('#hdncount').val();

	for(var a = 1; a < cnt; a++)
	{
		 $('#txtliquiamt'+a).val('');
	}
	$('#totalliquiamount').text('0.00');
}
function saveliquidation()
{
	var cnt 		=   $('#hdncount').val();
	var valid 		=	true;
	var msg			=	"";	
	var	diff		=	0;
	for(var a = 1; a < cnt; a++)
	{
		amount = $('#txtliquiamt'+a).val();
		if(amount == '' || amount == 0)
		{
			valid = false;
			$('#txtliquiamt'+a).addClass('errpurpose');
		}
		else
		{
			$('#txtliquiamt'+a).removeClass('errpurpose');
		}
	}
	CAamount	= + $("#tdCAamount").text();
	tamount		= + ($("#totalliquiamount").text()).replace(/,/g, '');
	if(CAamount > tamount)
	{
		diff	=	(CAamount-tamount);
		msg = "C.A. amount is greater than the generated total amount.<br> You'll be returning an amount of <b>"+ diff + " pesos</b> if approved.";
		$('#hdnretamount').val(diff);
	}
	if(CAamount < tamount)
	{
		diff	=	(tamount-CAamount);
		msg = "C.A. amount is less than the generated total amount.<br> Please get your reimbursement with an amount of <b>"+ diff + " pesos</b> if approved.";
		$('#hdnreimamount').val(diff);
	}
	if(valid)
	{
		MessageType.confirmmsg(goSaveLiquidation,"Do you want to save this liquidation?");
		if(msg != '')
		{
			MessageType.infoMsg(msg);
		}
	}
	else
	{
		MessageType.infoMsg('Amount must not be empty or zero in value.');
	}
}
function goSaveLiquidation()
{
	var frmliquidation	=	$('#frmliquidation').serialize();
	var cnt 			=   $('#hdncount').val();
	var CAamount		=	$("#tdCAamount").text();
	var CANO			=	$("#tdCANO").text();
	
	$.ajax({
		type		: 	'POST',
		data		: 	frmliquidation,
		url			:	'filing.php?action=SAVELIQUIDATION&CANO='+CANO+'&CNT='+cnt +'&CAAMOUNT='+CAamount,
		beforeSend	:	function()
					{
						$('#divloader').dialog("open");
					},
		success		:	function(response)
					{
						$('#divdebug').html(response);
						$('#divloader').dialog("close");
						$('#divliquidate').dialog("close");
					}
	});
}