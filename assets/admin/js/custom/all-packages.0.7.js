$(document).ready(function(){	
    oTable = {};
    initiallize();
    
    // Set CSRF token for all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
            [10, 25, 50, 100, 250, 500],
            ['10 rows', '25 rows', '50 rows', '100 rows', '250 rows', '500 rows']
        ],
        "buttons": [
            'excel', 'pageLength',
        ],
        "ajax": {
            "url": BASEPATH + "/admin/get-all-packages-report",
            "data": function(d) {}
        },
        "columns": [
            {
                data: 'id',
                name: 'id',
                orderable: false,
                render: function(data, type, full, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                },
                searchable: false
            },
            {
                data: 'name',
                name: 'name',
                searchable: true,
                orderable: false,
                render: function(data, type, full, meta) {
                    return data;
                }
            },
            {
                data: 'percantage',
                name: 'percantage',
                searchable: true,
                orderable: false,
                render: function(data, type, full, meta) {
                    return data;
                }
            },
            {
                data: 'direct_ref',
                name: 'direct_ref',
                searchable: true,
                orderable: false,
                render: function(data, type, full, meta) {
                    return data;
                }
            },
            {
                data: 'days',
                name: 'months',
                searchable: true,
                orderable: false,
                render: function(data, type, full, meta) {
                    return data;
                }
            },
            {
                data: 'id',
                name: 'id',
                orderable: false,
                render: function(data, type, full, meta) {
                    return '<button class="edit-btn btn btn-info" data-id="' + data + '">Edit</button>';
                },
                searchable: false
            }
        ],
        rowCallback: function(row, data) {
            // Avoid converting to inputs immediately; handle edit separately
        }
    });

    // Handle edit click to make row editable
    $('#tblData').on('click', '.edit-btn', function() {
        var id = $(this).data('id');
        var row = $(this).closest('tr');
        var updatedData = {};

        // Convert row to editable inputs on edit click
        row.find('td:not(:last-child)').each(function(index) {
            var currentValue = $(this).text();
            var columnName = oTable.column(index).dataSrc();
            $(this).html('<input type="text" class="edit-input form-control" value="' + currentValue + '" data-column="' + columnName + '">');
        });

        // Submit edit
        var submitEdit = function() {
            row.find('.edit-input').each(function() {
                var columnName = $(this).data('column');
                updatedData[columnName] = $(this).val();
            });

            showMask();
            $.ajax({
                url: BASEPATH + '/admin/update-package/' + id,
                type: 'POST',
                data: updatedData,
                success: function(response) {
                    showSuccess('Record updated successfully');
                    oTable.ajax.reload();
                    hideMask();
                },
                error: function() {
                    showError('Error updating record');
                    hideMask();
                }
            });
        };

        // Add a save button to the row
        row.find('td:last-child').html('<button class="save-btn btn btn-success" data-id="' + id + '">Save</button>');
        $('#tblData').off('click', '.save-btn').on('click', '.save-btn', function() {
            submitEdit();
        });
    });

    $('#newPackageBtn').on('click', function() {
        $('#addRow').show();
        oTable.rows().every(function() {
            this.child.hide();
        });
    });

    $('#tblData').on('click', '.add-btn', function() {
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
                oTable.ajax.reload();
                $('#addRow').hide();
                $('#addRow .new-input').val('');
                hideMask();
            },
            error: function() {
                showError('Error adding record');
                hideMask();
            }
        });
    });
}