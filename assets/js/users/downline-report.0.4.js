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
            "url": BASEPATH + "/get-downline-report",
            "data": function(d) {
                // d.paidsearch = document.querySelector('input[name="paid_search"]:checked').value;
                // d.leg = $("#leg").val();
            }
        },
        "order": [
            [5, "desc"]
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
                data: 'name',
                name: 'name',
                render: function(data, type, full, meta) {
                    return data;
                },
                searchable: true
            },
            
            {
                data: 'activation_date',
                name: 'activation_date',
                render: function(data, type, full, meta) {
                    return data == null ? '' : data;
                },
                searchable: true
            },

            {
                data: 'self_investment',
                name: 'self_investment',
                render: function(data, type, full, meta) {
                    return '$' + data == null ? 0 : Math.round(data,2);
                },
                searchable: true
            },
            
            {
                data: 'isactive',
                name: 'isactive',
                render: function(data, type, full, meta) {
                    if (data) {
                        return '<label class="badge border bg-success">Active</label>';
                    } else {
                        return '<label class="badge border bg-danger">InActive</label>';
                    }
                },
                searchable: false
            },

            {
                data: 'created_at',
                name: 'created_at',
                render: function(data, type, full, meta) {
                    return data;
                },
                searchable: true
            },

            {
                data: 'leg',
                name: 'leg',
                render: function(data, type, full, meta) {
                    return data == 'L' ? 'Left' : 'Right';
                },
                searchable: false
            },

            {
                data: 'referral',
                name: 'referral',
                render: function(data, type, full, meta) {
                    return data;
                },
                searchable: true
            },
        ]
    });
}