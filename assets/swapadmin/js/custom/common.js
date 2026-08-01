// JavaScript Document

var Errors = {
    "EMAIL_OR_PASSWORD_INCORRECT": "Email or password incorrect.",
    "INVALID_REQUEST_DATA": "Invalid request data sent.",
    "UNEXPECTED_ERROR_OCCURED": "An error occurred. Please try later.",
    "SESSION_INVALID": "Session expired. Please log in again.",
    "INCORRECT_CURRENT_PASSWORD": "Cuurent password incorrect.",
    "PASSWORD_DO_NOT_MATCH": "Passwords do not match.",
    "INVITE_CODE_ALREADY_EXISTS": "Invite code already exists",
    "INVALID_MEMBER": "Member Not Found"
};

var BASEPATH = $("#basePath").val();
var TOKEN = $("#token").val();

function showError(error) {
    toastr.error(error, "Opps!");
}

function showSuccess(msg) {
    toastr.success(msg, "Success!");
}

function showToasterError(error) {
    toastr.error(error, "Opps!");
}

$(document).ajaxError(function() {
    showToasterError("An error occurred. Please try later.");
});

function showMask() {
    $("body").mLoading({
        text: "Please wait...",
        icon: BASEPATH + "/assets/images/circle.gif"
    });
}

function hideMask() {
    $("body").mLoading('hide');
}

function formatDate(dateVal) {
    var newDate = new Date(dateVal);

    var sMonth = padValue(newDate.getMonth() + 1);
    var sDay = padValue(newDate.getDate());
    var sYear = newDate.getFullYear();
    var sHour = newDate.getHours();
    var sMinute = padValue(newDate.getMinutes());
    var sAMPM = "AM";

    var iHourCheck = parseInt(sHour);

    if (iHourCheck > 12) {
        sAMPM = "PM";
        sHour = iHourCheck - 12;
    } else if (iHourCheck === 0) {
        sHour = "12";
    }

    sHour = padValue(sHour);

    return sDay + "/" + sMonth + "/" + sYear + " " + sHour + ":" + sMinute + " " + sAMPM;
}

function padValue(value) {
    return (value < 10) ? "0" + value : value;
}
