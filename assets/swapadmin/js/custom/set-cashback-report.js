// JavaScript Document
$('document').ready(function(){	
	bindEvents();
});

function bindEvents(){
	$("#btnSubmit").bind("click", function(){
		processCashback();
	});
}

function processCashback(){
	var assigned_to = $("#assigned_to").val();
	
	if(assigned_to == ""){
		showError("Please enter username to set cashback.");
		return false;
	}
	
	var reqObj = {
		_token : $("#token").val(),
		assigned_to : assigned_to,
	};	
	
	showMask();
	
	$.ajax({
	  type: 'POST',
	  url: BASEPATH + "/admin/process-set-cashback",
	  data: reqObj,
	  dataType: 'json',
	  success: function(result){
			if(result.success){
				showSuccess('Transaction Sucessfully!');
				oTable.draw();	
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