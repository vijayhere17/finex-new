$('document').ready(function(){	
	oTable = {};
	initiallize();
});

function initiallize() {
    oTable = $('#tblData').DataTable({
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "searchHighlight": true,
        "search": {
            "smart": true
        },
        "dom": 'Blfrtip',
        "lengthMenu": [
            [ 10, 25, 50, 100, 250, 500],
            [ '10 rows', '25 rows', '50 rows', '100 rows', '250 rows', '500 rows']
        ],
        "buttons": [
            'excel', 'pageLength',
        ],
        "ajax": {
            "url": BASEPATH + "/admin/get-stake-report",
            "data": function(d) {
                d.status = PHP2JS.data.status;
            }
        },
        "columns": [
            {
                data: 'id',
                name: 'id',
                render: function(data, type, full, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                },
                searchable: false
            },
            
            {
                data: 'payment',
                name: 'payment',
                searchable: true,
                render: function(data, type, full, meta) {
                    return data;
                },
                searchable: true
            },
            
            {
                data: 'request_on',
                name: 'request_on',
                searchable: true,
                render: function(data, type, full, meta) {
                    return data;
                },
                searchable: true
            },

            {
                data: 'username',
                name: 'username',
                searchable: true,
                render: function(data, type, full, meta) {
                    return data;
                },
                searchable: true
            },

            {
                data: 'name',
                name: 'name',
                searchable: true,
                render: function(data, type, full, meta) {
                    return data;
                },
                searchable: true
            },

            {
                data: 'stake_amount',
                name: 'stake_amount',
                searchable: true,
                render: function(data, type, full, meta) {
                    return data;
                },
                searchable: true
            },

            {
                data: 'txn_hash',
                name: 'txn_hash',
                render: function(data, type, full, meta) {
                    return data;
                },
                searchable: false
            },
            
            {
                data: 'id',
                name: 'id',
                render: function(data, type, full, meta) {
                    if(PHP2JS.data.status == 0){
                        return '<a href="javascript:" class="btn btn-info btn-sm btn-icon icon-left btn-info"><i class="entypo-pencil"></i>Approved</a>&nbsp;&nbsp;<a href="javascript:" class="btn btn-danger btn-sm btn-icon icon-left btn-danger"><i class="entypo-cancel"></i>Rejected</a>';
                    }else{
                        return '';
                    }
                },
                searchable: false
            }
        ]
    });
}

function viewlinks(id){
    $("#linhkshtml").html('');
    
	var reqObj = {
		_token : $("#token").val(),
		id : id
	};	

	showMask();

	$.ajax({
		type: 'POST',
		url: BASEPATH + "/admin/process-view-admin-links",
		data: reqObj,
		dataType: 'json',
		success: function(result){
			if(result.success){
				$("#linhkshtml").html(result.menu_html);
				$("#viewlinksModal").modal();
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

function deleteuser(id){
	if(confirm('Are your sure you want delete admin user?')) 
	{
		var reqObj = {
			_token : $("#token").val(),
			id : id
		};	

		showMask();

		$.ajax({
			type: 'POST',
			url: BASEPATH + "/admin/process-delete-admin-and-links",
			data: reqObj,
			dataType: 'json',
			success: function(result){
				if(result.success){
					showSuccess('Admin user remove successfully!');
					oTable.draw();
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
}