$('document').ready(function() {
    oTable = {};
    initiallize();
    bindEvents();
});

const obscureAddress = (address) => {
    return address.substring(0, 6) + '...'+address.substring(address.length - 4, address.length);
}

function bindEvents() {

}

function initiallize() {
    oTable = $('#tblTickets').DataTable({
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "searchHighlight": true,
        "search": {
            "smart": true
        },
        "dom": 'Blfrtip',
        "lengthMenu": [
            [10, 25, 50, -1],
            [10, 25, 50, "All"]
        ],
        "buttons": [
            'copy', 'csv', 'excel', 'pdf', 'print',
        ],
        "ajax": {
            "url": BASEPATH + "/admin/get-swap-txn-logs",
            "data": function(d) {}
        },
         "order": [
            [13, "desc"]
        ],
        "columns": [
            {
                data: 'id',
                name: 'id',
                render: function(data, type, full, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                },
                searchable: true
            },
            
            {
                data: 'txn_date',
                name: 'txn_date',
                searchable: true,
                render: function(data, type, full, meta) {
                    return data;
                }
            },
            
            {
                data: 'address',
                name: 'address',
                searchable: true,
                render: function(data, type, full, meta) {
                    return obscureAddress(data);
                }
            },
            
            {
                data: 'receive_wallet',
                name: 'receive_wallet',
                render: function(data, type, full, meta) {
                    return obscureAddress(data);
                },
                searchable: true
            },

            {
                data: 'from_coin',
                name: 'from_coin',
                render: function(data, type, full, meta) {
                    return data;
                },
                searchable: true
            },
            
            {
                data: 'to_coin',
                name: 'to_coin',
                render: function(data, type, full, meta) {
                    return data;
                },
                searchable: true
            },
            
            {
                data: 'amount',
                name: 'amount',
                render: function(data, type, full, meta) {
                    return data+' '+full.from_coin;
                },
                searchable: true
            },

            {
                data: 'rate',
                name: 'rate',
                render: function(data, type, full, meta) {
                    return data;
                },
                searchable: true
            },
            
            {
                data: 'change',
                name: 'change',
                render: function(data, type, full, meta) {
                    return (data > 0 ? data+' '+full.from_coin : 0);
                },
                searchable: true
            },
            
            {
                data: 'swap_amount',
                name: 'swap_amount',
                render: function(data, type, full, meta) {
                    return data+' '+full.to_coin;
                },
                searchable: true
            },
            
            {
                data: 'txn_hash',
                name: 'txn_hash',
                render: function(data, type, full, meta) {
                    if (full.from_coin == 'BMYT') {
                        return '<a href="https://ecroxscan.com/tx/'+data+'" target="_blank">View Hash</a>';
                    } else if (full.from_coin == 'USDT') {
                        return '<a href="https://bscscan.com/tx/'+data+'" target="_blank">View Hash</a>';
                    }
                },
                searchable: false
            },
            
            {
                data: 'status',
                name: 'status',
                render: function(data, type, full, meta) {
                    if (data == 0) {
                        return '<label class="badge badge-warning">Pending</label>';
                    } else if (data == 1) {
                        return '<label class="badge badge-info">Processing</label>';
                    } else if (data == 2) {
                        return '<label class="badge badge-success">Sucess</label>';
                    } else if (data == 3) {
                        return '<label class="badge badge-danger">Rejected</label>';
                    }
                },
                searchable: false
            },
        
            {
                data: 'receiver_hash',
                name: 'receiver_hash',
                render: function(data, type, full, meta) {
                    if (full.to_coin == 'BMYT') {
                        return data == null ? '' : '<a href="https://ecroxscan.com/tx/'+data+'" target="_blank">View Hash</a>';
                    } else if (full.to_coin == 'USDT') {
                        return data == null ? '' : '<a href="https://bscscan.com/tx/'+data+'" target="_blank">View Hash</a>';
                    }
                },
                searchable: false
            },

            {
                data: 'last_update',
                name: 'last_update',
                render: function(data, type, full, meta) {
                    return data;
                },
                searchable: true
            },
        ]
    });
}