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
            "url": BASEPATH + "/admin/get-withdrawal-report",
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
                data: 'amount',
                name: 'amount',
                searchable: true,
                render: function(data, type, full, meta) {
                    return data;
                },
                searchable: true
            },

            {
                data: 'coin_rate',
                name: 'coin_rate',
                searchable: true,
                render: function(data, type, full, meta) {
                    return data;
                },
                searchable: true
            },

            {
                data: 'payable',
                name: 'payable',
                searchable: true,
                render: function(data, type, full, meta) {
                    return data;
                },
                searchable: true
            },

            {
                data: 'wallet',
                name: 'wallet',
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
                    return data == null ? '' : '<a href="https://polygonscan.com/tx/'+data+'" target="_blank"></i>View Hash</a>';
                },
                searchable: false
            },
            
            {
                data: 'id',
                name: 'id',
                render: function(data, type, full, meta) {
                    if(PHP2JS.data.status == 0){
                        return '<a href="javascript:approvedtxn('+data+')" class="btn btn-success btn-sm btn-icon icon-left btn-info"><i class="entypo-pencil"></i>Manual Approved</a>&nbsp;&nbsp;<a href="javascript:actionwithdrawalreq('+data+', 2, `approve`)" class="btn btn-info btn-sm btn-icon icon-left btn-info"><i class="entypo-pencil"></i>Approved</a>&nbsp;&nbsp;<a href="javascript:actionwithdrawalreq('+data+', 3, `reject`)" class="btn btn-danger btn-sm btn-icon icon-left btn-danger"><i class="entypo-cancel"></i>Rejected</a>';
                    }else if(PHP2JS.data.status == 1){
                        return '<a href="javascript:approvedtxn('+data+')" class="btn btn-success btn-sm btn-icon icon-left btn-info"><i class="entypo-pencil"></i>Update Txn. Hase</a>&nbsp;&nbsp;<a href="javascript:actionwithdrawalreq('+data+', 4, `pending`)" class="btn btn-info btn-sm btn-icon icon-left btn-info"><i class="entypo-pencil"></i>Make It Pending</a>';
                    }else if(PHP2JS.data.status == 2){
                        return '<a href="javascript:approvedtxn('+data+')" class="btn btn-success btn-sm btn-icon icon-left btn-info"><i class="entypo-pencil"></i>Update Txn. Hase</a>';
                    }else{
                        return '';
                    }
                },
                searchable: false
            }
        ]
    });
}

function actionwithdrawalreq(withdrawid, status, context)
{
	if(confirm('Are your sure you want '+context+' request?')) 
	{
		var reqObj = {
			_token : $("#token").val(),
			withdrawid : withdrawid,
			status : status
		};	

		showMask();

		$.ajax({
			type: 'POST',
			url: BASEPATH + "/admin/process-withdrawal-request",
			data: reqObj,
			dataType: 'json',
			success: function(result){
				if(result.success){
					showSuccess('Withdrawal request '+context+' successfully!');
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

// ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

function approvedtxn(id){
    $("#hdnwithid").val(id);
    $('#myModal').modal({backdrop: 'static', keyboard: false})  
}

function closeapprovedtxnModel(){
    $("#hdnwithid").val(0);
    $("#hash").val('');
    $('#myModal').modal('hide')  
}

function getSubmitTxnHash(){
    var id = $("#hdnwithid").val();
    var hash = $("#hash").val();
    
    if(id == '0'){
        showToasterError('Invalid approved request.');
        return false;
    }
    
    if(hash == ''){
        showToasterError('Please enter a transaction hash');
        return false;
    }
    
    actionRequest(id, 2, hash);
}

function actionRequest(id, status, hash) {

    showMask();

    var reqObj = {
        _token: $("#token").val(),
        id: id,
        status: status,
        hash: hash
    };

    $.ajax({
        type: 'POST',
        url: BASEPATH + "/admin/process-manual-withdrawal-request",
        data: reqObj,
        dataType: 'json',
        success: function(result) {
            if (result.success) {
                if (status == 2) {
                    toastr.success("Withdrawal Request Has Been Approved Successful!", "Success");
                } else if (status == 3) {
                    toastr.success("Withdrawal Request Has Been Rejected Successful!", "Success");
                }
                closeapprovedtxnModel();
                oTable.draw();
            } else {
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