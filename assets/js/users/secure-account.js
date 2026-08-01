jQuery(document).ready(function() {

});

jQuery('.btn-submit').bind('click', function(e) {
    e.preventDefault();
    if (validate()) {
        processupdate();
    }
});

function validate() {
    var code = $("#code").val();
    
    if (code == '') {
        erroralert('Please enter a 2FA code');
        return false;
    }
    
    return true;
}

function processupdate() {
    var secret = $("#secret").val();
    var code = $("#code").val();

    var reqObj = {
        _token: token,
        secret : secret,
        code : code
    };

    blockui();

    $.ajax({
        type: 'POST',
        url: BASEPATH + "/submit-2fa-code",
        data: reqObj,
        dataType: 'json',
        success: function(result) {
            if (result.success) {
                successalert('Google Auth Setup Successfully!');
                resetformdata();
            } else {
                erroralert(result.error);
            }
            unblockui();
        }
    });
}

function resetformdata(){
    $("#code").val('');
    location.reload(); 
}