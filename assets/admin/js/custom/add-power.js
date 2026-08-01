// JavaScript Document
$('document').ready(function(){	
	bindEvents();
});


function bindEvents(){
	$("#btnSubmit").bind("click", function(){
		updateMember();
	});
}

function updateMember(){
	var assigned_to = $("#assigned_to").val();
	var amount = $("#amount").val();
	
	if(assigned_to == ""){
		showError("Please enter username to make transaction.");
		return false;
	}
	
	if(amount == ""){
		showError("Please enter a power amount.");
		return false;
	}
	
	var reqObj = {
		_token : $("#token").val(),
		assigned_to : assigned_to,
		amount : amount,
	};	
	
	showMask();
	
	$.ajax({
	  type: 'POST',
	  url: BASEPATH + "/admin/process-add-power",
	  data: reqObj,
	  dataType: 'json',
	  success: function(result){
			if(result.success){
				showSuccess('Transaction Sucessfully!');
				$("#assigned_to").val(''); $("#amount").val('0'); $("#membername").text('');
			}else{
				showError(Errors[result.error_code]);
			}
			hideMask();
		},
		statusCode: {
			500: function() {
		  		showError("An error occurred. Please try later.");
				hideMask();
			}
		}			
	});
}