$('document').ready(function() {
    oTable = {};
    initiallize();
    bindEvents();
});

function bindEvents() {

}

function initiallize() {
    oTable = $('#tblTickets').DataTable({
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "searchHighlight": true,
        "search": {
            "smart": true
        },
        "dom": 'Blfrtip',
        "lengthMenu": [
            [10, 25, 50, -1],
            [10, 25, 50, "All"]
        ],
        "buttons": [
            'copy', 'csv', 'excel', 'pdf', 'print',
        ],
        "ajax": {
            "url": BASEPATH + "/admin/get-all-support-ticket",
            "data": function(d) {}
        },
         "order": [
            [8, "desc"]
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
                data: 'username',
                name: 'username',
                searchable: true,
                render: function(data, type, full, meta) {
                    return '<a style="color: blue;" title="Click to login" href="javascript:backLogin(' + full.member_id + ');"> ' + data + '</a>';
                }
            },

            {
                data: 'name',
                name: 'name',
                render: function(data, type, full, meta) {
                    return data;
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
                data: 'id',
                name: 'id',
                render: function(data, type, full, meta) {
                    if (full.status_master_id != 2) {
                        return '<a href="javascript:" onclick="viewopenmegbox(' + data + ')" class="btn btn-green btn-sm btn-icon icon-left btn-green"><i class="entypo-chat"></i>View Message</a>';
                    } else {
                        return ''
                    }
                },
                searchable: false
            },
            {
                data: 'status_master_id',
                name: 'status_master_id',
                render: function(data, type, full, meta) {
                    if (data == 0) {
                        return '<label class="badge badge-info">Open</label>';
                    } else if (data == 1) {
                        return '<label class="badge badge-warning">Hold</label>';
                    } else if (data == 2) {
                        return '<label class="badge badge-primary">Close</label>';
                    }
                },
                searchable: false
            },

            {
                data: 'updated_at',
                name: 'updated_at',
                render: function(data, type, full, meta) {
                    return data;
                },
                searchable: true
            },
        ]
    });
}

function backLogin(id){
    var reqObj = {
		_token : $("#token").val(),
		id : id
	};	

	showMask();

	$.ajax({
		type: 'POST',
		url: BASEPATH + "/admin/process-back-login",
		data: reqObj,
		dataType: 'json',
		success: function(result){
			if(result.success){
				window.open(BASEPATH+'/dashboard', '_blank');	
			}else{
				showError(Errors[result.error_code]);
			}
			hideMask();
		},
		statusCode: {
			500: function() {
			showError("An error occurred. Please try later.");
				hideMask();
			}
		}			
	});
}

function viewopenmegbox(ticket_id) {
    $("#ticket_id").val(ticket_id);
    viewmessage({ backdrop: 'static', keyboard: false });
    $("#message-chat").modal();
}

function closeopenmegbox() {
    $("#ticket_id").val('0');
    $("#lastmsgid").val('0');
    $("#chatmsg").html('');
    $("#ticket_status").val('');
    $('#message-chat').modal('toggle');
    oTable.draw();
}

function viewmessage() {
    var ticket_id = $("#ticket_id").val();
    var last_id = $("#lastmsgid").val();
    var memid = $("#memberid").val();

    var reqObj = {
        _token: $("#token").val(),
        lastmsgid: last_id,
        ticket_id: ticket_id
    };

    $.ajax({
        type: 'POST',
        url: BASEPATH + "/admin/process-view-ticket-message",
        data: reqObj,
        dataType: 'json',
        success: function(result) {
            if (result.success) {
                var msg_html = '';
                $("#ModalLabel").text('Ticket : ' + result.ticket_no);
                $("#ticket_status").val(result.ticket_status);
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
                toastr.error(result.error, "Error");
            }
        }
    });
}

jQuery('.btn-send-msg').bind('click', function(e) {
    var ticket_id = $("#ticket_id").val();
    var txt_message = $("#txt_message").val();
    var ticket_status = $("#ticket_status").val();

    if (ticket_id <= 0) {
        return false;
    }

    if (ticket_status == '') {
        return false;
    }

    if (txt_message == '') {
        toastr.error('Please type message.', "Error");
        return false;
    }

    if (ticket_status == '') {
        toastr.error('Please select ticket status.', "Error");
        return false;
    }

    var reqObj = {
        _token: $("#token").val(),
        ticket_id: ticket_id,
        txt_message: txt_message,
        ticket_status: ticket_status
    };

    $.ajax({
        type: 'POST',
        url: BASEPATH + "/admin/process-send-ticket-message",
        data: reqObj,
        dataType: 'json',
        success: function(result) {
            if (result.success) {
                if (ticket_status == 2) {
                    oTable.draw();
                    closeopenmegbox();
                } else {
                    $("#txt_message").val('');
                    viewmessage();
                }
            } else {
                erroralert(result.error);
            }
        }
    });
});