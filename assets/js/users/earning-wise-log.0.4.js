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
            "url": BASEPATH + "/get-earning-wise-log",
            "data": function(d) {
                d.logtype = PHP2JS.data.logtype;
            }
        },
        "order": [
            [4, "desc"]
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
                data: 'description',
                name: 'description',
                searchable: true
            },
            {
                data: 'amount',
                name: 'amount',
                render: function(data, type, full, meta) {
                    return '$' + data;
                },
                searchable: true
            },
            {
                data: 'txn_type',
                name: 'txn_type',
                render: function(data, type, full, meta) {
                    if (data == 1) {
                        return '<span class="badge bg-success">Paid</span>';
                    } else if (data == 2) {
                        return '<span class="badge bg-danger">Debit</span>';
                    } else if (data == 3) {
                        return '<span class="badge bg-warning">Flush</span>';
                    }
                    return '-';
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
            }
        ]
    });
}
