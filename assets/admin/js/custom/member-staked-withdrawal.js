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
            "url": BASEPATH + "/admin/get-staked-withdrawal",
            "data": function(d) {
                d.withdraw_type = $("#withdraw_type").val();
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
                data: 'stake_amount',
                name: 'stake_amount',
                searchable: true,
                render: function(data, type, full, meta) {
                    return data;
                },
                searchable: true
            },
            
            {
                data: 'receive_return',
                name: 'receive_return',
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
                    return parseFloat(data * 25 / 100).toFixed(4);
                },
                searchable: true
            },
            
            {
                data: 'stake_amount',
                name: 'stake_amount',
                searchable: true,
                render: function(data, type, full, meta) {
                    let charge = parseFloat(data * 25 / 100).toFixed(4);
                    let roi = parseFloat(full.receive_return).toFixed(4);
                    let month = calculateMonthsBetween(full.request_on, new Date());
                    
                    if(month < 6)
                    {
                        roi = roi;
                    }
                    else
                    {
                        roi = 0;
                    }
                    
                    return parseFloat(data-charge-roi).toFixed(4);
                },
                searchable: true
            },

            {
                data: 'up_status',
                name: 'up_status',
                searchable: true,
                render: function(data, type, full, meta) {
                    if(data == 1)
                    {
                        return '<input name="SUBMIT" type="submit" class="btn btn-success" value="Approve" onclick="actionWithdrawalReq('+full.id+', 2)">&nbsp;&nbsp;<input name="SUBMIT" type="submit" class="btn btn-danger" value="Reject" onclick="actionWithdrawalReq('+full.id+', 3)">'
                    }
                    else if(data == 2)
                    {
                        return '<b style="color: green;">Approved</b>'
                    }
                    else if(data == 3)
                    {
                        return '<b style="color: red;">Rejected</b>'
                    }
                },
                searchable: true
            },
        ]
    });
}

function calculateMonthsBetween(startDate, endDate)
{
    const start = new Date(startDate);
    const end = new Date(endDate);

    // Calculate the total months difference
    const totalMonths = (end.getFullYear() - start.getFullYear()) * 12 + (end.getMonth() - start.getMonth());

    return totalMonths;
}

function actionWithdrawalReq(id, status)
{
    var reqObj = {
		_token : $("#token").val(),
		id : id,
		status : status
	};	

	showMask();

	$.ajax({
		type: 'POST',
		url: BASEPATH + "/admin/process-staked-withdrawal-req",
		data: reqObj,
		dataType: 'json',
		success: function(result){
			if(result.success){
				toastr.success('Request successfully!', "Success!");	
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