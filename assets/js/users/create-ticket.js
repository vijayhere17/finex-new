jQuery(document).ready(function() {

});

jQuery('.btn-submit').bind('click', function(e) {
    e.preventDefault();
    if (validate()) {
        processupdate();
    }
});

function validate() {
    var type = $("#type").val();
    var title = $("#title").val();
    var desc = $("#desc").val();
    
    if (type == '') {
        erroralert('Please select a type');
        return false;
    }
    
    if (title == '') {
        erroralert('Please enter a title.');
        return false;
    }

    if (desc == '') {
        erroralert('Please enter a message.');
        return false;
    }

    return true;
}

function processupdate() {
    var type = $("#type").val();
    var title = $("#title").val();
    var desc = $("#desc").val();

    var reqObj = {
        _token: token,
        type : type,
        title : title,
        desc : desc
    };

    blockui();

    $.ajax({
        type: 'POST',
        url: BASEPATH + "/submit-create-ticket",
        data: reqObj,
        dataType: 'json',
        success: function(result) {
            if (result.success) {
                successalert('Create ticket and send request successfully!');
                resetformdata();
            } else {
                erroralert(result.error);
            }
            unblockui();
        }
    });
}

function resetformdata(){
    $("#type").val('');
    $("#title").val('');
    $("#desc").val('');
}