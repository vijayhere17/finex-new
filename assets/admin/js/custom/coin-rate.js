$('document').ready(function(){	
	$("#btnSubmit").bind("click", function(){
		if(validate()){
			updateinrrate();			
		}
	});
});

function validate(){
	var new_rate = $("#new_rate").val();

	if(new_rate == ""){
		showError("Please enter new rate.");
		return false;
	}

	return true;
}

function updateinrrate(){

	var new_rate = $("#new_rate").val();

	var reqObj = {
    _token : $("#token").val(),
    new_rate : new_rate
  };	

  showMask();

	$.ajax({
	  type: 'POST',
	  url: BASEPATH + "/admin/process-update-coin-rate",
	  data: reqObj,
	  dataType: 'json',
	  success: function(result){
			if(result.success){
                showSuccess('Coin Rate Update Successfully!')
				window.location.href = BASEPATH + "/admin/coin-rate-set";	
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