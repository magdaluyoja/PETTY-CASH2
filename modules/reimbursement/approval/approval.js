$("document").ready(function(){
	$("tr td").on("click",".ca-dtls-lnk",function(){
		var trxno	=	$(this).attr("data-trxno");
		$.ajax({
			url		:	"approval.php?action=VIEWREIMBURSEMENTDTLS&TRXNO="+trxno,
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
	$("tr td").on("click",".btnapprove",function(){
		var reimno		=	$(this).attr("data-reimno");
		MessageType.confirmmsg(approveREIM,"Do you want to approve this Reimbursement request?", reimno);
	});
	$("tr td").on("click",".btndisapprove",function(){
		var reimno		=	$(this).attr("data-reimno");
		MessageType.confirmmsg(disapproveREIM,"Do you want to disapprove this Reimbursement request?", reimno);
	});
});
function getREIMlist(REIMnum,date)
{
	$.ajax({
		url			:"approval.php?action=GETREIMLIST&REIMNUM="+REIMnum+"&DATE="+date,
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
function approveREIM(REIMnum)
{
	$.ajax({
		url			:	'approval.php?action=APPROVEREIM&REIMNO='+REIMnum,
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
function disapproveREIM(REIMnum)
{
	$.ajax({
		url			:	'approval.php?action=DISAPPROVEREIM&REIMNO='+REIMnum,
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