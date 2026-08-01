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
            "url": BASEPATH + "/admin/get-member-report",
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
                data: 'username',
                name: 'username',
                searchable: true,
                render: function(data, type, full, meta) {
                    return '<a href="javascript:toClip(`'+full.address+'`)"><i class="entypo-popup"></i></a>&nbsp;&nbsp;<a href="javascript:backlogin('+full.id+')">'+data+'</a>';
                }
            },

            {
                data: 'name',
                name: 'firstname',
                searchable: true,
                render: function(data, type, full, meta) {
                    return data;
                }
            },

            {
                data: 'referral',
                name: 'referral',
                searchable: false,
                render: function(data, type, full, meta) {
                    return data;
                }
            },

            {
                data: 'activation',
                name: 'activation',
                searchable: false,
                render: function(data, type, full, meta) {
                    return data;
                }
            },

            {
                data: 'activation_on',
                name: 'activation_on',
                searchable: false,
                render: function(data, type, full, meta) {
                    return data;
                }
            },

            {
                data: 'signup_on',
                name: 'signup_on',
                searchable: false,
                render: function(data, type, full, meta) {
                    return data;
                }
            },
    
            {
                data: 'id',
                name: 'id',
                render: function(data, type, full, meta) {
                    return '<a href="javascript:editprofile('+data+')" class="btn btn-info btn-sm btn-icon icon-left btn-info"><i class="entypo-pencil"></i>Edit</a>';
                },
                searchable: false
            }
        ]
    });
}

function toClip(text) {
    var copy = document.createElement("textarea");
    document.body.appendChild(copy);
    copy.value = text;
    copy.select();
    document.execCommand("copy");
    document.body.removeChild(copy);
    
    showSuccess('Address copy successfylly!')
}

function editprofile(id){
    window.location.href = BASEPATH + "/admin/edit-"+id+"-profile";
}

function backlogin(id){
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
