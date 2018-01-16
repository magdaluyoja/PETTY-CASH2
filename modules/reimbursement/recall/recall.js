$("document").ready(function(){
	$("tr td").on("click",".ca-dtls-lnk",function(){
		var trxno	=	$(this).attr("data-trxno");
		$.ajax({
			url		:	"recall.php?action=VIEWREIMBURSEMENTDTLS&TRXNO="+trxno,
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
	$("tr td").on("click",".btnrecall",function(){
		var reimno		=	$(this).attr("data-reimno");
		MessageType.confirmmsg(recallreim,"Do you want to recall this Reimbursement request?",reimno);
	});
});
function getREIMlist(REIMnum,date)
{
	$.ajax({
		url			:"recall.php?action=GETREIMLIST&REIMNUM="+REIMnum+"&DATE="+date,
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
function recallreim(reimno)
{
	$.ajax({
		url			:	'recall.php?action=RECALLREIM&REIMNO='+reimno,
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