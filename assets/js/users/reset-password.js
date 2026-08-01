jQuery(document).ready(function() {

});

jQuery('.btn-submit').bind('click', function(e) {
    e.preventDefault();
    if (validate()) {
        processupdate();
    }
});

function validate() {
    var old_password = $("#old_password").val();
    var new_password = $("#new_password").val();
    var con_password = $("#con_password").val();
    
    if (old_password == '') {
        erroralert('Please enter a old password');
        return false;
    }
    
    if (new_password == '') {
        erroralert('Please enter a new password.');
        return false;
    }

    if (con_password == '') {
        erroralert('Please enter a confirm password.');
        return false;
    }

    if (new_password != con_password) {
        erroralert('New password do not matching confirm password.');
        return false;
    }

    return true;
}

function processupdate() {
    var old_password = $("#old_password").val();
    var new_password = $("#new_password").val();
    var con_password = $("#con_password").val();

    var reqObj = {
        _token: token,
        old_password : old_password,
        new_password : new_password,
        con_password : con_password
    };

    blockui();

    $.ajax({
        type: 'POST',
        url: BASEPATH + "/submit-update-password",
        data: reqObj,
        dataType: 'json',
        success: function(result) {
            if (result.success) {
                successalert('Password update successfully!');
                resetformdata();
            } else {
                erroralert(result.error);
            }
            unblockui();
        }
    });
}

function resetformdata(){
    $("#old_password").val('');
    $("#new_password").val('');
    $("#con_password").val('');
}