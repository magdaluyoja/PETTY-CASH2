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
			url			:	'recall.php?action=VIEWCADTLS&CANO='+cano,
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
	$("tr td").on("click",".btnrecall",function(){
		var cano		=	$(this).attr("data-cano");
		MessageType.confirmmsg(recallCA,"Do you want to recall this Cash Advance request?",cano);
	});
	$("#btnsearch").click(function(){
		var CAnum	=	$("#txtCAnum").val();
		var date	=	$("#txtdate").val();
		getCAlist(CAnum,date);
	});
});
function getCAlist(CAnum,date)
{
	$.ajax({
		url			:"recall.php?action=GETCALIST&CANUM="+CAnum+"&DATE="+date,
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
function recallCA(CAnum)
{
	$.ajax({
		url			:	'recall.php?action=RECALLCA&CANO='+CAnum,
		beforeSend	:	function()
					{
						$('#divloader').dialog("open");
					},
		success		:	function(response)
					{
						$('#divdebug').html(response);
						$('#divloader').dialog("close");
						$("#tblCAlists").tablesorter( {sortList: [[0,0]]} ); 
					}
  		});
}