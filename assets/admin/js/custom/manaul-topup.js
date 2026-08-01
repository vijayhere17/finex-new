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
	var topup_type = $("#topup_type").val();
	var description = $("#description").val();
	
	if(assigned_to == ""){
		showError("Please enter username to make transaction.");
		return false;
	}
	
	if(amount == ""){
		showError("Please enter a transaction amount.");
		return false;
	}
	
	if(topup_type == ""){
		showError("Please select a topup type.");
		return false;
	}
	
	if(description == ""){
		showError("Please enter a transaction description.");
		return false;
	}
	
	var reqObj = {
		_token : $("#token").val(),
		assigned_to : assigned_to,
		amount : amount,
		topup_type : topup_type,
		description : description
	};	
	
	showMask();
	
	$.ajax({
	  type: 'POST',
	  url: BASEPATH + "/admin/process-manual-topup",
	  data: reqObj,
	  dataType: 'json',
	  success: function(result){
			if(result.success){
				showSuccess('Topup Sucessfully!');
				window.location.href = BASEPATH + "/admin/user-staked-report";	
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