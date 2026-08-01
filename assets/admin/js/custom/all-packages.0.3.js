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
            "url": BASEPATH + "/admin/get-all-packages-report",
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
                data: 'name',
                name: 'name',
                searchable: true,
                render: function(data, type, full, meta) {
                    return data;
                },
                searchable: true
            },

            {
                data: 'percantage',
                name: 'percantage',
                searchable: true,
                render: function(data, type, full, meta) {
                    return data;
                },
                searchable: true
            },
            
            {
                data: 'direct_ref',
                name: 'direct_ref',
                searchable: true,
                render: function(data, type, full, meta) {
                    return data;
                },
                searchable: true
            },

            {
                data: 'days',
                name: 'months',
                searchable: true,
                render: function(data, type, full, meta) {
                    return data;
                },
                searchable: true
            },
           
            {
                data: 'id',
                name: 'id',
                render: function(data, type, full, meta) {
                    return '<button class="edit-btn btn btn-info" data-id="' +data+ '">Edit</button>';
                },
                searchable: true
            }
        ],
        rowCallback: function(row, data) {
            $('td', row).not(':last-child').each(function() {
                $(this).html('<input type="text" class="edit-input form-control" value="' + $(this).text() + '" data-column="' + $(this).index() + '" data-id="' + data.id + '">');
            });
        }
    });
}

$('#newPackageBtn').on('click', function() {
    $('#addRow').show();
    table.rows().every(function() {
        this.child.hide();
    });
});
    
$('#stakeMastersTable').on('click', '.add-btn', function() {
    var newData = {};
    $('#addRow .new-input').each(function() {
        var columnName = $(this).data('column');
        newData[columnName] = $(this).val();
    });
    
    showMask();

    $.ajax({
        url: BASEPATH + '/admin/add-new-package',
        type: 'POST',
        data: newData,
        success: function(response) {
            showSuccess('Record added successfully');
            table.ajax.reload();
            $('#addRow').hide();
            $('#addRow .new-input').val('');
            
            hideMask();
        },
        error: function() {
            showError('Error adding record');
            hideMask()
        }
    });
});

$('#stakeMastersTable').on('click', '.edit-btn', function() {
    var id = $(this).data('id');
    var row = $(this).closest('tr');
    var updatedData = {};

    row.find('.edit-input').each(function() {
        var columnIndex = $(this).data('column');
        var columnName = table.column(columnIndex).dataSrc();
        updatedData[columnName] = $(this).val();
    });
    
    showMask();
    
    $.ajax({
        url: BASEPATH + '/admin/update-package/'+id,
        type: 'POST',
        data: updatedData,
        success: function(response) {
            showSuccess('Record updated successfully');
            table.ajax.reload();
            
            hideMask();
        },
        error: function() {
            showError('Error updating record');
            hideMask();
        }
    });
});