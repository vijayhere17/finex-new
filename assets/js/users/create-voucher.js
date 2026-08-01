jQuery(document).ready(function() {

});

async function changereturndate(){
    var str = $("#tourtype option:selected").text();
    var match = str.match(/(\d+) days/);
    var daysToAdd = parseInt(match[1], 10);
    
    var currentDate = new Date();
    
    var newDate = new Date(currentDate);
        newDate.setDate(newDate.getDate() + daysToAdd);
    
    var formattedDate = newDate.getFullYear() + '-' + ('0' + (newDate.getMonth() + 1)).slice(-2) + '-' + ('0' + newDate.getDate()).slice(-2);
    
    $("#to_date").val(formattedDate);
    
    document.getElementById("to_date").disabled = true;
}

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