// JavaScript Document
$('document').ready(function(){	
	bindEvents();
});

function bindEvents(){
	$("#btnSubmit").bind("click", function(){
		submitPackageMaster();
	});
}

function submitPackageMaster(){
	var name = $("#name").val();
	var amount = $("#amount").val();
	var coin = $("#coin").val();
	var percantage = $("#percantage").val();
	var months = $("#months").val();
	var direct_ref = $("#direct_ref").val();
	var locking = $("#locking").val();
	var dmc = $("#dmc").val();
	var dmc_commission = $("#dmc_commission").val();
	var left_dmc = $("#left_dmc").val();
	var right_dmc = $("#right_dmc").val();
	
	if(name == ""){
		showError("Please enter package name.");
		return false;
	}
	
	if(amount == ""){
		showError("Please enter package amount.");
		return false;
	}
	
	if(coin == ""){
		showError("Please enter coin value.");
		return false;
	}
	
	if(percantage == ""){
		showError("Please enter package percantage.");
		return false;
	}
	
	if(months == ""){
		showError("Please enter package months.");
		return false;
	}
	
	if(direct_ref == ""){
		showError("Please eneter direct referral.");
		return false;
	}
	
	if(locking == ""){
		showError("Please enter locking.");
		return false;
	}
	
	if(dmc == ""){
		showError("Please enter dmc.");
		return false;
	}
	
	if(dmc_commission == ""){
		showError("Please eneter dmc commission.");
		return false;
	}
	
	if(left_dmc == ""){
		showError("Please enter left dmc.");
		return false;
	}
	
	if(right_dmc == ""){
		showError("Please eneter right dmc.");
		return false;
	}

	var reqObj = {
		_token : $("#token").val(),
		name : name,
		amount : amount,
		coin : coin,
		percantage : percantage,
		months : months,
		direct_ref : direct_ref,
		locking : locking,
		dmc : dmc,
		dmc_commission : dmc_commission,
		left_dmc : left_dmc,
		right_dmc : right_dmc,
	};	
	
	showMask();
	
	$.ajax({
	  type: 'POST',
	  url: BASEPATH + "/admin/process-new-travel-package",
	  data: reqObj,
	  dataType: 'json',
	  success: function(result){
			if(result.success){
				showSuccess('Package Add Sucessfully!');
				window.location.href = BASEPATH + "/admin/new-package";	
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