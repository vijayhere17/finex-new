// JavaScript Document
$('document').ready(function(){	
	bindEvents();
});


function bindEvents(){
	$("#btnSubmit").bind("click", function(){
		if(validate()){
			changePassword();			
		}
	});
}

function validate(){
	var old_password = $("#old_password").val();
	var new_password = $("#new_password").val();
	var repeat_password = $("#repeat_password").val();
	if(old_password == ""){
		showError("Please enter current password.");
		return false;
	}else if(new_password == ""){
		showError("Please enter new password.");
		return false;
	}else if(repeat_password == ""){
		showError("Please confirm new password.");
		return false;
	}else if(new_password != repeat_password){
		showError("Passwords do not match.");
		return false;
	}
	return true;
}

function changePassword(){
	showMask();
	
	var old_password = $("#old_password").val();
	var new_password = $("#new_password").val();
	var repeat_password = $("#repeat_password").val();
	var reqObj = {
			_token : $("#token").val(),
			old_password : old_password,
			new_password : new_password,
			repeat_password : repeat_password
		};	
	$.ajax({
	  type: 'POST',
	  url: BASEPATH + "/swapadmin/process-change-password",
	  data: reqObj,
	  dataType: 'json',
	  success: function(result){
			if(result.success){
				showSuccess('Password Change Sucessfully!')
				window.location.href = BASEPATH + "/swapadmin/change-password";	
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