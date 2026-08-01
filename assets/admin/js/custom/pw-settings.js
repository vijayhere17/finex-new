$('document').ready(function(){	
	$("#btnSubmit").bind("click", function(){
		if(validate()){
			updateinrrate();			
		}
	});
});

function validate()
{
	var pw_weekly_limit = $("#pw_weekly_limit").val();
	var pw_minimum = $("#pw_minimum").val();

	if(pw_weekly_limit == "")
	{
		showError("Please enter weekly limit");
		return false;
	}
	
	if(pw_minimum == "")
	{
		showError("Please enter minimum withdrawal limit");
		return false;
	}

	return true;
}

function updateinrrate(){

	var pw_status = $("#pw_status").val();
    var pw_weekly_limit = $("#pw_weekly_limit").val();
	var pw_minimum = $("#pw_minimum").val();
	
	var reqObj = {
        _token : $("#token").val(),
        pw_status : pw_status,
        pw_weekly_limit : pw_weekly_limit,
        pw_minimum : pw_minimum
    };	

    showMask();

	$.ajax({
    	type: 'POST',
    	url: BASEPATH + "/admin/process-potential-settings",
    	data: reqObj,
    	dataType: 'json',
    	success: function(result){
			if(result.success){
                showSuccess('Potential Settings Update Successfully!')
				window.location.href = BASEPATH + "/admin/potential-withdrawal-settings";	
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