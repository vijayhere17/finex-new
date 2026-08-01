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
            "url": BASEPATH + "/admin/get-balance-report",
            "data": function(d) {
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
                data: 'balance',
                name: 'balance',
                searchable: true,
                render: function(data, type, full, meta) {
                    return parseFloat(data).toFixed(4);
                },
                searchable: true
            },

            {
                data: 'tcredit',
                name: 'tcredit',
                searchable: true,
                render: function(data, type, full, meta) {
                    return parseFloat(data).toFixed(4);;
                },
                searchable: true
            },

            {
                data: 'tdebit',
                name: 'tdebit',
                render: function(data, type, full, meta) {
                    return parseFloat(data).toFixed(4);;
                },
                searchable: false
            },

        ]
    });
}