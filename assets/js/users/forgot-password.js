jQuery(document).ready(function() {
	// call function whene page load
});

jQuery('.btn-submit').bind('click', function(e) {
    e.preventDefault();
    if (validate()) {
        processsubmit();
    }
});

function validate() {
    var username = $("#username").val();
    
    if (username == '') {
        erroralert('Please enter a username');
        return false;
    }
    
    return true;
}

function processsubmit() {
    var username = $("#username").val();

    var reqObj = {
        _token: token,
        username : username,
    };

    blockui();

    $.ajax({
        type: 'POST',
        url: BASEPATH + "/submit-forgot-password",
        data: reqObj,
        dataType: 'json',
        success: function(result) {
            if (result.success) {
                successalert('Password forgot successfully! New password send your register email id.')
                resetformdata();
            } else {
                erroralert(result.error);
            }
            unblockui();
        }
    });
}

function resetformdata() {
    $("#username").val('');
}