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
            "url": BASEPATH + "/admin/get-bonanza-achievers",
            "data": function(d) {
                d.bonanza_id = $("#bonanza_id").val();
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
                data: 'bonanza',
                name: 'bonanza',
                searchable: true,
                render: function(data, type, full, meta) {
                    return data;
                },
                searchable: true
            }
        ]
    });
}
