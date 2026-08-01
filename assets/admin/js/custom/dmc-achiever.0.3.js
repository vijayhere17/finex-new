$('document').ready(function(){	
	oTable = {};
	initiallize();
	
	$("#btnSubmit").bind("click", function(){
		if(validate()){
			addachievement();			
		}
	});
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
            "url": BASEPATH + "/admin/get-dmc-achievers",
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
                data: 'achieve_on',
                name: 'achieve_on',
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
                data: 'dmc_level',
                name: 'dmc_level',
                searchable: true,
                render: function(data, type, full, meta) {
                    return data;
                },
                searchable: true
            },

            {
                data: 'daily_amount',
                name: 'daily_amount',
                searchable: true,
                render: function(data, type, full, meta) {
                    return data;
                },
                searchable: true
            },

            {
                data: 'remaining_days',
                name: 'remaining_days',
                searchable: true,
                render: function(data, type, full, meta) {
                    return data+' Days';
                },
                searchable: true
            }
        ]
    });
}

function validate(){
	var assigned_to = $("#assigned_to").val();
	var achieve_level = $("#achieve_level").val();
	
	if(assigned_to == ""){
		showError("Please enter username to achive level.");
		return false;
	}
	
	if(achieve_level == ""){
		showError("Please select level.");
		return false;
	}
	
	return true;
}

function addachievement(){
	var assigned_to = $("#assigned_to").val();
	var achieve_level = $("#achieve_level").val();
	
	var reqObj = {
		_token : $("#token").val(),
		assigned_to : assigned_to,
		achieve_level : achieve_level
	};
	
	showMask();
	
	$.ajax({
	  type: 'POST',
	  url: BASEPATH + "/admin/process-set-dmc-level",
	  data: reqObj,
	  dataType: 'json',
	  success: function(result){
			if(result.success){
				showSuccess('Renk Achieve Sucessfully!');
				$("#achieverModal").modal('hide');
				$("#assigned_to").val('');
				$("#achieve_level").val('');
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