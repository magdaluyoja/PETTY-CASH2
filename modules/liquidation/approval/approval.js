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
			url			:	'approval.php?action=VIEWCADTLS&CANO='+cano,
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
	$("tr td").on("click",".btndetails",function(){
		var cano	=	$(this).attr("data-cano");
		var amount	=	$(this).attr("data-amt");
		$.ajax({
			url			:	'approval.php?action=VIEWLIQUIDATIONDTLS&CANO='+cano+'&CAAMOUNT='+amount,
			beforeSend	:	function()
						{
							$('#divloader').dialog("open");
						},
			success		:	function(response)
						{
							$('#divliquidationdtls').html(response);
							$('#divliquidationdtls').dialog("open");
							$('#divloader').dialog("close");
						}
	  		});
	});
	$("tr td").on("click",".btnapprove",function(){
		var cano	=	$(this).attr("data-cano");
		var amount	=	$(this).attr("data-amt");
		MessageType.confirmmsg(approveliqui,"Do you want to approve this Cash Advance Liquidation request?",cano);
	});
	$("tr td").on("click",".btndisapprove",function(){
		var cano	=	$(this).attr("data-cano");
		var amount	=	$(this).attr("data-amt");
		MessageType.confirmmsg(disapproveliqui,"Do you want to disapprove this Cash Advance Liquidation request?",cano);
	});
});
function getCAlist(CAnum,date)
{
	$.ajax({
		url			:"approval.php?action=GETCALIST&CANUM="+CAnum+"&DATE="+date,
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
function approveliqui(cano){
	$.ajax({
			url			:	'approval.php?action=APPROVELIQUIDATION&CANO='+cano,
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
function disapproveliqui(cano){
	$.ajax({
			url			:	'approval.php?action=DISAPPROVELIQUIDATION&CANO='+cano,
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