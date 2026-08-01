jQuery(document).ready(function() {
    oTable = {};
    initiateDataTable();
});

function initiateDataTable() {
    oTable = $('#tableList').DataTable({
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "searchHighlight": true,
        "search": {
            "caseInsensitive": true
        },
        "ajax": {
            "url": BASEPATH + "/get-topup-history",
            "data": function(d) {
            }
        },
        "order": [
            [1, "desc"]
        ],
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
                data: 'member.username',
                name: 'member.username',
                render: function(data, type, full, meta) {
                    return obscureAddress(data);
                },
                searchable: true
            },
            
            {
                data: 'created_at',
                name: 'created_at',
                render: function(data, type, full, meta) {
                    return formatDate(data);
                },
                searchable: true
            },
            
            {
                data: 'amount',
                name: 'amount',
                render: function(data, type, full, meta) {
                    return '$'+data;
                },
                searchable: true
            },

           
            {
                data: 'id',
                name: 'id',
                render: function(data, type, full, meta) {
                    return '<span class="badge bg-success">Success</span>';
                },
                searchable: false
            }
        ]
    });
}
