jQuery(document).ready(function() {
    var userid = $("#hdnLoginID").val();
    var username = $("#hdnUsername").val();
    var firstname = $("#hdnFirstName").val();
    generateTree(userid);
});

$("#btnGoBack").bind("click", function() {
    var userid = $("#hdnLoginID").val();
    generateTree(userid);
});

$("#btnLevelUp").bind("click", function() {
    var parent_id = $("#hdnParentID").val();
    if (parent_id != '' && parent_id != 0) {
        generateTree(parent_id);
    }
});

function generateTree(id) {
    blockui()
    
    $.ajax({
        type: 'GET',
        url: BASEPATH + "/get-binary-tree-view?id=" + id,
        data: {},
        dataType: 'json',
        success: function(result) {
            if (result.success) {
                var data = result.tree;
                var num = 0;
                $.each(data, function(i, tlist) {
                    if (num > 0) {
                        if (num % 2 == 0) {
                            var position = 'R';
                        } else {
                            var position = 'L';
                        }
                    } else {
                        var position = 'N';
                    }
                    var pusername = findparent(i, data);
                    initiationtree(i, tlist, pusername, position);
                    num++;
                });
                var userid = $("#hdnLoginID").val();
                if (id != userid) {
                    console.log(data.p0.parent_id);
                    $("#btnGoBack").show();
                    $("#hdnParentID").val(data.p0.parent_id);
                    $("#btnLevelUp").show();
                } else {
                    $("#btnGoBack").hide();
                    $("#btnLevelUp").hide();
                }
                
                unblockui()
            } else {
                erroralert(result.error_code);
                unblockui()
            }
        },
        statusCode: {
            500: function() {
                erroralert("An error occurred. Please try later.");
                unblockui()
            },
            404: function() {
                erroralert("An error occurred. Please try later.");
                unblockui()
            }
        }
    });
}

function findparent(no, data) {
    if (no == 'p1') {
        return (data.p0 == null ? '' : data.p0.username);
    } else if (no == 'p2') {
        return (data.p0 == null ? '' : data.p0.username);
    } else if (no == 'p3') {
        return (data.p1 == null ? '' : data.p1.username);
    } else if (no == 'p4') {
        return (data.p1 == null ? '' : data.p1.username);
    } else if (no == 'p5') {
        return (data.p2 == null ? '' : data.p2.username);
    } else if (no == 'p6') {
        return (data.p2 == null ? '' : data.p2.username);
    } else if (no == 'p7') {
        return (data.p3 == null ? '' : data.p3.username);
    } else if (no == 'p8') {
        return (data.p3 == null ? '' : data.p3.username);
    } else if (no == 'p9') {
        return (data.p4 == null ? '' : data.p4.username);
    } else if (no == 'p10') {
        return (data.p4 == null ? '' : data.p4.username);
    } else if (no == 'p11') {
        return (data.p5 == null ? '' : data.p5.username);
    } else if (no == 'p12') {
        return (data.p5 == null ? '' : data.p5.username);
    } else if (no == 'p13') {
        return (data.p6 == null ? '' : data.p6.username);
    } else if (no == 'p14') {
        return (data.p6 == null ? '' : data.p6.username);
    } else {
        return '';
    }
}

function initiationtree(no, udata, puser, position) {
    if (no == 'p0') {
        if (udata == null) {
            var html = '<img src="' + BASEPATH + '/assets/tree/blank.png" style="max-width: 60px;"><br><p class="up">+JOIN NOW</p>';
        } else {
            if (udata.kit == null) {
                var html = '<a href="javascript:" onclick="viewuserdata(' + udata.id + ')"><img src="' + BASEPATH + '/assets/tree/icon-unpaid.png" style="max-width: 60px;"></a><br><p class="up" title="' + udata.username + '">' + udata.username + '</p>';
            } else {
                var html = '<a href="javascript:" onclick="viewuserdata(' + udata.id + ')"><img src="' + getplanImage(udata.kit.id, udata.dmc_status) + '" style="max-width: 60px;"></a><br><p class="up" title="' + udata.username + '">' + udata.username + '</p>';
            }
        }
    } else {
        if (udata == null) {
            var html = '<img src="' + BASEPATH + '/assets/tree/blank.png" style="max-width: 60px;"><br><a href="#" onclick="getjoinnow(`' + puser + '`, `' + position + '`)"><p class="up">+JOIN NOW</p></a>';
        } else {
            if (udata.kit == null) {
                var html = '<a href="javascript:" onclick="viewuserdata(' + udata.id + ')"><img src="' + BASEPATH + '/assets/tree/icon-unpaid.png" style="max-width: 60px;"></a><br><a href="#" onclick="getshowtree(' + udata.id + ')"><p class="up" title="' + udata.username + '">' + udata.username + '</p></a>';
            } else {
                var html = '<a href="javascript:"onclick="viewuserdata(' + udata.id + ')"><img src="' + getplanImage(udata.kit.id, udata.dmc_status) + '" style="max-width: 60px;"></a><br><a href="#"  onclick="getshowtree(' + udata.id + ')"><p class="up" title="' + udata.username + '">' + udata.username + '</p></a>';
            }
        }
    }
    $('#' + no).html('');
    $('#' + no).append(html);
}

function viewuserdata(id) {
    blockui()
    
    $("#userdetails").html('');

    var reqObj = {
        _token: token,
        id: id
    };

    $.ajax({
        type: 'POST',
        url: BASEPATH + "/get-view-tree-user",
        data: reqObj,
        dataType: 'json',
        success: function(result) {
            if (result.success) {
                var response = result.data;
                $("#userdetails").html(`<tr class="table-active"><td>Sponsor</td><td>:</td><td>` + (response.referral == null ? '' : response.referral.username) + `</td></tr>
                <tr><td>Username</td><td>:</td><td>` + response.username + `</td></tr>
                <tr class="table-success"><td>Name</td><td>:</td><td>` + response.firstname + ` ` + response.lastname + `</td></tr>
                <tr><td>Package</td><td>:</td><td>` + (response.kit == null ? 'N/A' : response.kit.package) + `</td></tr>
                <tr class="table-warning"><td>Stake On</td><td>:</td><td>` + (response.activation_date == null ? '--/--/----' : formatDate(response.activation_date)) + `</td></tr>
                <tr><td>Left Team</td><td>:</td><td>` + (response.left_count) + `</td></tr>
                <tr class="table-danger"><td>Left DMC</td><td>:</td><td>` + (result.left_dmc) + `</td></tr>
                <tr><td>Right Team</td><td>:</td><td>` + (response.right_count) + `</td></tr>
                <tr class="table-primary"><td>Right DMC</td><td>:</td><td>` + (result.right_dmc) + `</td></tr>
                <tr><td>Left Stake</td><td>:</td><td>$` + (response.binary_point == null ? '' : response.binary_point.left_points) + `</td></tr>
                <tr class="table-info"><td>Right Stake</td><td>:</td><td>$` + (response.binary_point == null ? '' : response.binary_point.right_points) + `</td></tr>
                <tr><td>Joining Date</td><td>:</td><td>` + formatDate(response.created_at) + `</td></tr>`);
                $(".modal-title").text('#' + response.username);
                $("#viewUserModal").modal('show');
                unblockui()
            } else {
                erroralert(result.error);
                unblockui()
            }
        },
        statusCode: {
            500: function() {
                erroralert("An error occurred. Please try later.");
                unblockui()
            },
            404: function() {
                erroralert("An error occurred. Please try later.");
                unblockui()
            }
        }
    });
}

function getplanImage(plan_id, dmc_status) {
    var url = BASEPATH + '/assets/tree/';
    if (dmc_status == 0) {
        url += 'icon-paid.png';
    } else {
        url += 'icon-dmc.png';
    }    
    return url;
}

function getshowtree(id) {
    generateTree(id);
}

function getjoinnow(str1, str) {
    var url = BASEPATH + '/join/' + str1 + '/' + str;
    window.open(url, '_blank');
}

jQuery('#username').on('change', function(e) {
    var member_id = $("#hdnLoginID").val();
    var username = $("#username").val();

    if (username == '') {
        return false;
    }

    var reqObj = {
        _token: token,
        member_id: member_id,
        username: username
    };

    blockui();

    $.ajax({
        type: 'POST',
        url: BASEPATH + "/process-check-tree-user",
        data: reqObj,
        dataType: 'json',
        success: function(result) {
            if (result.success) {
                if (result.member.firstname != null) { var firstname = result.member.firstname; } else { var firstname = 'Name Is Not Available'; }
                $("#user_name").text(firstname);
                $("#searchID").val(result.member.id);
                generateTree(result.member.id);
            } else {
                erroralert(result.error);
            }
            unblockui();
        }
    });
});