$("document").ready(function(){
	$("#btnsearch").click(function(){
		var CAnum	=	$("#txtCAnum").val();
		var date	=	$("#txtdate").val();
		getCAlist(CAnum,date);
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
	$("tr td").on("click",".btnapprove",function(){
		var cano		=	$(this).attr("data-cano");
		MessageType.confirmmsg(approveCA,"Do you want to approve this Cash Advance request?",cano);
	});
	$("tr td").on("click",".btndisapprove",function(){
		var cano		=	$(this).attr("data-cano");
		MessageType.confirmmsg(disapproveCA,"Do you want to disapprove this Cash Advance request?",cano);
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
function approveCA(CAnum)
{
	$.ajax({
		url			:	'approval.php?action=APPROVECA&CANO='+CAnum,
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
function disapproveCA(CAnum)
{
	$.ajax({
		url			:	'approval.php?action=DISAPPROVECA&CANO='+CAnum,
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