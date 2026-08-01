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
	var wallet = $("#wallet").val();
	var action = $("#action").val();
	var description = $("#description").val();
	
	if(assigned_to == ""){
		showError("Please enter username to make transaction.");
		return false;
	}
	
	if(amount == ""){
		showError("Please enter a transaction amount.");
		return false;
	}
	
	if(wallet == ""){
		showError("Please select a transaction wallet.");
		return false;
	}
	
	if(action == ""){
		showError("Please select a transaction type.");
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
		wallet : wallet,
		action : action,
		description : description
	};	
	
	showMask();
	
	$.ajax({
	  type: 'POST',
	  url: BASEPATH + "/admin/process-cradit-debit-master",
	  data: reqObj,
	  dataType: 'json',
	  success: function(result){
			if(result.success){
				showSuccess('Transaction Sucessfully!');
				window.location.href = BASEPATH + "/admin/cradit-debit-report";	
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