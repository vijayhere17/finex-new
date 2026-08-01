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
            "url": BASEPATH + "/admin/get-cashback-user-report",
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
                    return '<a href="javascript:backlogin('+full.id+')">'+data+'</a>';
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
                data: 'email',
                name: 'email',
                searchable: true,
                render: function(data, type, full, meta) {
                    return data;
                }
            },

            {
                data: 'mobile',
                name: 'mobile',
                searchable: true,
                render: function(data, type, full, meta) {
                    return data;
                }
            },

            {
                data: 'total_stake',
                name: 'total_stake',
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
                data: 'process_on',
                name: 'process_on',
                render: function(data, type, full, meta) {
                    return data;
                },
                searchable: false
            }
        ]
    });
}
