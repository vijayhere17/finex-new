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
	showMask();
	
	var member_id = $("#member_id").val();
	var uesrname = $("#uesrname").val();
	var firstname = $("#firstname").val();
	var lastname = $("#lastname").val();
	var status = $("#status").val();
	var w_status = $("#w_status").val();
	var wt_status = $("#wt_status").val();
	
	var reqObj = {
		_token : $("#token").val(),
		member_id : member_id,
		uesrname : uesrname,
		firstname : firstname,
		lastname : lastname,
		status : status,
		w_status : w_status,
		wt_status : wt_status
	};	
	
	$.ajax({
	  type: 'POST',
	  url: BASEPATH + "/admin/process-update-member",
	  data: reqObj,
	  dataType: 'json',
	  success: function(result){
			if(result.success){
				showSuccess('Member Update Sucessfully!');
				window.location.href = BASEPATH + "/admin/member-report";	
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