$("document").ready(function(){
	$("#btngenerate").click(function(){
		var seldep	=	$('#seldep').val();
		window.open('CA_deduct_report.php?seldep='+seldep);
	});
});