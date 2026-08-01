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
            "url": BASEPATH + "/admin/get-staked-report",
            "data": function(d) {}
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
                data: 'stake_amount',
                name: 'stake_amount',
                searchable: true,
                render: function(data, type, full, meta) {
                    return data;
                },
                searchable: true
            },

            {
                data: 'topup_type',
                name: 'topup_type',
                searchable: true,
                render: function(data, type, full, meta) {
                    return data;
                },
                searchable: true
            },

            {
                data: 'description',
                name: 'description',
                searchable: true,
                render: function(data, type, full, meta) {
                    return data;
                },
                searchable: true
            },

            {
                data: 'booster',
                name: 'booster',
                searchable: true,
                render: function(data, type, full, meta) {
                    return data > 0 ? '<b style="color: green;">Booster</b>' : '';
                },
                searchable: true
            },
        ]
    });
}

function transfercoin(id){
    var reqObj = {
		_token : $("#token").val(),
		id : id
	};	

	showMask();

	$.ajax({
		type: 'POST',
		url: BASEPATH + "/admin/process-transfer-coin",
		data: reqObj,
		dataType: 'json',
		success: function(result){
			if(result.success){
				toastr.success('Transfer coin successfully!', "Success!");	
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