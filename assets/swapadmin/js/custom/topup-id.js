// JavaScript Document
$('document').ready(function(){	
	bindEvents();
});

function bindEvents(){
	$("#btnSubmit").bind("click", function(){
		submitStakePackage();
	});
}

function submitStakePackage(){
	var assigned_to = $("#assigned_to").val();
	var stake_package = $("#package").val();
	
	if(assigned_to == ""){
		showError("Please enter username to stake.");
		return false;
	}
	
	if(stake_package == ""){
		showError("Please select a package.");
		return false;
	}

	var reqObj = {
		_token : $("#token").val(),
		assigned_to : assigned_to,
		stake_package : stake_package,
	};	
	
	showMask();
	
	$.ajax({
	  type: 'POST',
	  url: BASEPATH + "/admin/process-new-stake",
	  data: reqObj,
	  dataType: 'json',
	  success: function(result){
			if(result.success){
				showSuccess('Stake Sucessfully!');
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