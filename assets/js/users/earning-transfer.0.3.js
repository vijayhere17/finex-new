jQuery(document).ready(function() {
     $(".balance").text('$'+PHP2JS.data.balance);
});

jQuery('.btn-otp-submit').bind('click', function(e) {
    e.preventDefault();
    if (validate(false)) {
        processTransfer(false);
    }
});

jQuery('.btn-submit').bind('click', function(e) {
    e.preventDefault();
    if (validate(true)) {
        processTransfer(true);
    }
});

function validate(otpstatus) {
    var balance = $(".balance").text();
    var amount = $("#amount").val();
    var otp = $("#otp").val();
    
    if (parseFloat(balance) <= 0) {
        erroralert('Account balance is $0');
        return false;
    }
    
    if (parseFloat(amount) == '') {
        erroralert('Please enter a amount.');
        return false;
    }

    if (parseFloat(amount) <= 0) {
        erroralert('Please enter a valid amount.');
        return false;
    }

    if (parseFloat(amount) > parseFloat(balance)) {
        erroralert('Insufficient account balance.');
        return false;
    }
    
    if(otpstatus){
        if (otp == '') {
            erroralert('Please enter a one - time password.');
            return false;
        }
    }

    return true;
}

function processTransfer(status) {
    var amount = $("#amount").val();
    var otp = $("#otp").val();
    
    var reqObj = {
        _token: token,
        amount : amount,
        otp : otp,
        status : status
    };

    blockui();

    $.ajax({
        type: 'POST',
        url: BASEPATH + "/process-earning-transfer",
        data: reqObj,
        dataType: 'json',
        success: function(result) {
            if (result.success) {
                if(status){
                    successalert('Earning transfer successfully!');
                    resetformdata(result.balance);
                    location.reload();
                }else{
                    successalert('OTP send your register email id!');
                    $(".btn-otp-submit").hide();
                    $(".btn-submit").show();
                }
            } else {
                erroralert(result.error);
            }
            unblockui();
        }
    });
}

function resetformdata(balance){
    $("#amount").val('');
    $("#otp").val('');
    $(".balance").text(balance);
    $(".btn-otp-submit").show();
    $(".btn-submit").hide();
}