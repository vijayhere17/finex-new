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
            "url": BASEPATH + "/get-stake-request",
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
                data: 'stake_coin',
                name: 'stake_coin',
                render: function(data, type, full, meta) {
                    return data;
                },
                searchable: true
            },

            {
                data: 'hash',
                name: 'hash',
                render: function(data, type, full, meta) {
                    return '<a href="'+PHP2JS.data.txn_hash_url+'/'+data+'" target="_blank">View Hash</a>';
                },
                searchable: true
            },
            
            {
                data: 'id',
                name: 'id',
                render: function(data, type, full, meta) {
                    var count_down = initializeCountdowns(data, full.return_date);
                    return '<div style="display: flex; gap: 5px;">&#128274; <div id="locker_timer_'+data+'" style="color: #ff0000; font-weight: 900;"></div></div>';
                },
                searchable: false
            },

            {
                data: 'status',
                name: 'status',
                render: function(data, type, full, meta) {
                    if (data == 0) {
                        return '<span class="badge bg-warning">Pending</span>';
                    } else if(data == 1) {
                        return '<span class="badge bg-info">Process</span>';
                    } else if(data == 2) {
                        return '<span class="badge bg-success">Success</span>';
                    }  else if(data == 3) {
                        return '<span class="badge bg-danger">Rejected</span>';
                    }
                },
                searchable: true
            }
        ]
    });
}