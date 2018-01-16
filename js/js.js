function Amount(){}
Amount.prototype = {
	constructor:Amount,
	
	getnumbersOnly:function(value,id)
	{
		var ValidChars ="0123456789.";
		var IsNumber = "";
		var Char;
		
		for (var i=0; i < value.length; i++)
		{
			Char = value.charAt(i);
			if(ValidChars.indexOf(Char) != -1)
			{
				IsNumber = IsNumber + Char;
			}
		}
		document.getElementById(id).value = IsNumber;
	},
	getNumberWithCommas:function(value) 
	{
    	return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	},
	sumupByLoop:function(count,prename,type,separator)
	{
		var totalAmount = 0,Amount = 0;
		for(var a=1;a <= count; a++)
		{
			if(type!="text")
			{
				if(separator == "")
				{
					Amount	= + ($("#"+prename+a).val()).replace(/,/g, '');
				}
				else
				{
					Amount	= + ($("#"+prename+a).val());
				}
			}
			else
			{
				if(separator == "")
				{
					Amount	= + ($("#"+prename+a).text()).replace(/,/g, '');
				}
				else
				{
					Amount	= + ($("#"+prename+a).text());
				}
			}
			totalAmount = totalAmount + Amount;
		}
		return totalAmount;
	}
}
var inputAmount = new Amount();

function Messages(){}
Messages.prototype = {
	constructor:Messages,
	confirmmsg:function(func,msg,key)
	{
		$("#txtconfmsg").text(msg);
		$("#divconfmsg").dialog({
			title:"Confirmation",
			modal:true,
			autoOpen: true,
			width: "auto",
			buttons: [
				{
					text: "No",
					click: function() {
						$( this ).dialog( "close" );
					}
				},{
					text: "Yes",
					click: function() {
						if(key != "")
						{
							func(key);
						}
						else
						{
							func();
						}
						
						$( this ).dialog( "close" );
					}
				}
			]
		});
	},
	successMsg:function(msg)
	{
		$("#txtsuccmsg").text(msg);
		$("#divsuccmsg").dialog({
			title	: "Successful!",
			modal	: true,
			autoOpen: true,
			width	: "auto",
			buttons	: [
				{
					text: "Ok",
					click: function() {
						$( this ).dialog( "close" );
					}
				}
			]
		});
	},
	infoMsg:function(msg)
	{
		$("#txtinfomsg").html(msg);
		$("#divinfomsg").dialog({
			title:"Message!",
			modal:true,
			autoOpen: open,
			width: "auto",
			buttons: [
				{
					text: "Ok",
					click: function() {
						$( this ).dialog( "close" );
					}
				}
			]
		});
	},
	errorMsg:function(msg)
	{
		$("#txterrmsg").text(msg);
		$("#diverrmsg").dialog({
			title:"Error!",
			modal:true,
			autoOpen: open,
			width: "auto",
			buttons: [
				{
					text: "Ok",
					click: function() {
						$( this ).dialog( "close" );
					}
				}
			]
		});
	}
}
var MessageType = new Messages();

window.setInterval(checkSession,1000);
var timer = 0;
function checkSession()
{
	timer = timer + 1;
	if(timer > 1800)
	{
		$.ajax({
				url	:	"/PETTY_CASH/config/session.php?action=EXPIRESESSION",
				success:function()
				{
					$('#txtsessmsg').text('Your session has expired. Please login again.');
					$('#divsession').dialog('open');
				}
			});
	}
}
$("document").ready(function(){
	$("body").on("load mousemove click keypress scroll",function(){
		timer = 0;
	});
	$(".numbersonly").keyup(function(){
		inputAmount.getnumbersOnly(this.value,this.id);
	});
	$(".themeimg").draggable();
	$(".tooltips").tooltip();
	$(".buttons").button();
	$(".radioset").buttonset();
	$(".buttons").css({"font-family":"'Times New Roman', Times, serif","font-size":"12px"});
	$(".dates").datepicker({ 
			dateFormat: 'yy-mm-dd',
			changeMonth: true,
	        changeYear: true 
	});
	$( "#divloader" ).dialog({
		modal:true,
		title:"Loading...",
		closeOnEscape:false,
		dialogClass:"no-close",
		autoOpen: false,
		width:200,
	});
	$("#divparticulars").dialog({
		bgiframe:true, 
		resizable:false, 
		title: "Particulars",
		height: "auto",
		width: "auto", 
		modal:true, 
		autoOpen: false,	
		draggable: true,
		overlay: { backgroundColor: '#000', opacity: 0.5 },
		buttons: {
					OK : function()
					{	
							$(this).dialog("close");
					}
				 }
	});
	$(".themeimg").click(function(){
		window.location=("/PETTY_CASH/modules/maintenance/theme/");
	});
});
