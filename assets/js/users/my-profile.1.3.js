const is_active = PHP2JS.data.user.kit_id, is_auth = PHP2JS.data.user.is_authenticator;

let user = PHP2JS.data.user;

jQuery(document).ready(function() {
	$("#username").val(user.username);
    $("#firstname").val(user.firstname);
    $("#lastname").val(user.lastname);
    $("#email").val(user.email);
    
    if(user.wallet_addr != undefined)
    {
       // document.getElementById('wallet_addr').readOnly = true;
    }
    
    // document.getElementById('email').readOnly = true;
    
    document.getElementById('username').readOnly = true;
});

jQuery('.btn-otp-submit').bind('click', function(e) {
    e.preventDefault();
    if (validate(false)) {
        processupdate(false);
    }
});

jQuery('.btn-submit').bind('click', function(e) {
    e.preventDefault();
    if (validate(true)) {
        processupdate(true);
    }
});

function validate(otpstatus) {
    var firstname = $("#firstname").val();
    var lastname = $("#lastname").val();
    var email = $("#email").val();
    var otp = $("#otp").val();
    
    if (firstname == '') {
        erroralert('Please enter first name');
        return false;
    }
    
    if (lastname == '') {
        erroralert('Please enter last name');
        return false;
    }
    
    if (email == '') {
        erroralert('Please enter email');
        return false;
    }
    
    if(is_active > 0){
        if(is_auth > 0){
            if(otpstatus){
                if (otp == '') {
                    erroralert('Please enter a google 2fa code.');
                    return false;
                }
            }
        }
    }

    return true;
}

function processupdate(status) {
    var firstname = $("#firstname").val();
    var lastname = $("#lastname").val();
    var email = $("#email").val();
    var otp = $("#otp").val();

    var reqObj = {
        _token: token,
        firstname : firstname,
        lastname : lastname,
        email : email, 
        otp : otp,
        status : status
    };

    blockui();

    $.ajax({
        type: 'POST',
        url: BASEPATH + "/submit-update-profile", 
        data: reqObj,
        dataType: 'json',
        success: function(result) {
           if (result.success) {
                if(status){
                    successalert('Profile Update submited successfully!');
                    $("#otp").val('');
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