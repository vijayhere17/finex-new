// JavaScript Document
$('document').ready(function() {
    bindEvents();
});


function bindEvents() {
    $("#btnLogin").bind("click", function() {
        login();
    });

    $(document).on("keypress", function(e) {
        if (e.keyCode == 13) {
            e.preventDefault();
            login();
        }
    });
}

function login() {
    var username = $("#username").val();
    var password = $("#password").val();

    var reqObj = {
        _token: $("#token").val(),
        username: username,
        password: password
    };

    $.ajax({
        type: 'POST',
        url: BASEPATH + "/swapadmin/process-admin-login",
        data: reqObj,
        dataType: 'json',
        success: function(result) {
            if (result.success) {
                window.location.href = BASEPATH + "/swapadmin/home";
            } else {
                showError(Errors[result.error_code]);
            }
        },
        statusCode: {
            500: function() {
                showError("An error occurred. Please try later.");
            }
        }
    });
}