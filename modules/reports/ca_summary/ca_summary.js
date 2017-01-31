$("document").ready(function(){
	$("#selscope").change(function(){
		if($(this).val() == "DEP")
	 	{
	 		$(".CDEP").show();
	 		$(".CEMP").hide();
	 	}
	 	else
	 	{
	 		$(".CDEP").hide();
	 		$(".CEMP").show();
	 	}
	});
	$("#btngenerate").click(function(){
		var valid	=	true;
		if($("#selscope").val() == "DEP")
		{
			if(($("#txtfrom").val() == ""  &&  $("#txtto").val() == "") || ($("#txtfrom").val() == ""  &&  $("#txtto").val() != "") || ($("#txtfrom").val() != ""  &&  $("#txtto").val() == "") || ($("#txtfrom").val() > $("#txtto").val()))
			{
				MessageType.infoMsg("Invalid date range.");
				valid = false;
			}
		}
		else
		{
			if($("#txtempid").val() == "" || $("#txtname").val() == "")
			{
				MessageType.infoMsg("Please select employee.");
				valid = false;
			}
		}
		return valid;
	});
	$(".txtemployee").keyup(function(evt){
		var txtempid	=	$('#txtempid').val();
		var txtname		=	$('#txtname').val();
		var evthandler	=	(evt.charCode) ? evt.charCode : evt.keyCode;
		if(txtempid != '' || txtname != '')
		{
			if(evthandler != 40 && evthandler != 13 && evthandler != 27)
			{
				$.ajax({
						url			:	'ca_summary.php?action=Q_SEARCHEMP&EMPID='+txtempid+'&NAME='+txtname,
						success		:	function(response)
									{
										if(response == '')
										{
											MessageType.infoMsg("No records found.");
											$('#divselemp').html('');
										}
										else
										{
											$('#divselemp').html(response);
											var position 	=	$("#txtempid").position();
											var selwidth	=	$("#txtempid").width() + $("#txtname").width()+10;
											$("#divselemp").css({left: position.left, position:'absolute'});
											$('#divselemp').show();
											$('#selemp').css({width:selwidth});
										}
									}
				});
			}
			else if(evthandler == 40 && $('#divselemp').html() != '')
			{
				$('#selemp').focus();
			}
			else
			{
				$('#divselemp').html('');
			}
		}
		else
		{
			$('#divselemp').html('');
		}
	});
	for (i = new Date().getFullYear(); i > 1900; i--)
	{
	    $('.yearpicker').append($('<option />').val(i).html(i));
	}
});
function smartsel(evt)
{
	
	var evthandler	=	(evt.charCode) ? evt.charCode : evt.keyCode;
	
	if(evt == 'click')
	{
		$('#hdnval').val($('#selemp').val());
		var vx = $('#hdnval').val();
		var x = vx.split('|'); 
		$('#txtempid').val(x[0]);
		$('#txtname').val(x[1]);
		$('#divselemp').html('');
	}
	else
	{
		if(evthandler == 13)
		{
			$('#hdnval').val($('#selemp').val());
			var vx = $('#hdnval').val();
			var x = vx.split('|'); 
			$('#txtempid').val(x[0]);
			$('#txtname').val(x[1]);
			$('#divselemp').html('');
		}
	}
}