jQuery(document).ready(function() {
    oTable = {};
    initiateDataTable();
});

function initiateDataTable() {
    oTable = $('#tblTicketHistory').DataTable({
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "searchHighlight": true,
        "search": {
            "caseInsensitive": true
        },
        "ajax": {
            "url": BASEPATH + "/get-ticket-history",
            "data": function(d) {
                d.status = $("#status").val();
            }
        },
        "order": [
            [5, "desc"]
        ],
        "columns": [
            {
                data: 'id',
                name: 'id',
                render: function(data, type, full, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                },
                searchable: true
            },

            {
                data: 'ticket_no',
                name: 'ticket_no',
                render: function(data, type, full, meta) {
                    return data;
                },
                searchable: true
            },

            {
                data: 'subject',
                name: 'subject',
                render: function(data, type, full, meta) {
                    return data;
                },
                searchable: true
            },

            {
                data: 'title',
                name: 'title',
                render: function(data, type, full, meta) {
                    return data;
                },
                searchable: true
            },

            {
                data: 'status_master_id',
                name: 'status_master_id',
                render: function(data, type, full, meta) {
                    if (data == 0) {
                        return '<span class="badge bg-info">Open</span>';
                    } else if (data == 1) {
                        return '<span class="badge bg-warning">Hold</span>';
                    } else if (data == 2) {
                        return '<span class="badge bg-primary">Close</span>';
                    }
                },
                searchable: false
            },

            {
                data: 'created_at',
                name: 'created_at',
                render: function(data, type, full, meta) {
                    return data;
                },
                searchable: true
            },

            {
                data: 'id',
                name: 'id',
                render: function(data, type, full, meta) {
                    return ' <button type="submit" class="btn btn-primary mr-2" onclick="viewopenmegbox(' + data + ')">View Message</button>';
                },
                searchable: false
            },
        ]
    });
}

function viewopenmegbox(ticket_id) {
    $("#ticket_id").val(ticket_id);
    
    viewmessage();
    
    $("#message-chat").modal('show');
}

function closeopenmegbox() {
    $("#ticket_id").val('0');
    $("#lastmsgid").val('0');
    $("#chatmsg").html('');
    $('#message-chat').modal('toggle');
}

function viewmessage() {
    var ticket_id = $("#ticket_id").val();
    var last_id = $("#lastmsgid").val();
    var memid = $("#memberid").val();

    var reqObj = {
        _token: token,
        lastmsgid: last_id,
        ticket_id: ticket_id
    };

    $.ajax({
        type: 'POST',
        url: BASEPATH + "/process-view-ticket-message",
        data: reqObj,
        dataType: 'json',
        success: function(result) {
            if (result.success) {
                var msg_html = '';
                $("#ModalLabel").text('Ticket : ' + result.ticket_no);
                var allmsg = result.message;
                if (allmsg != null && allmsg.length > 0) {
                    $.each(allmsg, function(i, item) {
                        $("#lastmsgid").val(item.id);
                        if (item.to_id == memid) {
                            msg_html += `<div class="incoming"><div class="bubble"><p>` + item.message + `</p></div></div>`;
                        }
                        if (item.from_id == memid) {
                            msg_html += `<div class="outgoing"><div class="bubble"><p>` + item.message + `</p></div></div>`;
                        }
                    });
                    $('#chatmsg').append(msg_html);
                }
            } else {
                erroralert(result.error);
            }
        }
    });
}

jQuery('.btn-send-msg').bind('click', function(e) {
    var ticket_id = $("#ticket_id").val();
    var txt_message = $("#txt_message").val();

    if (ticket_id <= 0) {
        return false;
    }

    if (txt_message == '') {
        erroralert('Please type message.');
        return false;
    }

    var reqObj = {
        _token: token,
        ticket_id: ticket_id,
        txt_message: txt_message
    };

    $.ajax({
        type: 'POST',
        url: BASEPATH + "/process-send-ticket-message",
        data: reqObj,
        dataType: 'json',
        success: function(result) {
            if (result.success) {
                $("#txt_message").val('');
                viewmessage();
            } else {
                erroralert(result.error);
            }
        }
    });
});