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
            "url": BASEPATH + "/get-topup-report",
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
                data: 'paid_amount',
                name: 'paid_amount',
                render: function(data, type, full, meta) {
                    return '$'+data;
                },
                searchable: true
            },

            {
                data: 'return_days',
                name: 'return_days',
                render: function(data, type, full, meta) {
                    return data+' Days';
                },
                searchable: true
            },

            {
                data: 'is_deleted',
                name: 'is_deleted',
                render: function(data, type, full, meta) {
                    if (data == 0) {
                        return '<span class="badge bg-success">Active</span>';
                    } else if(data == 1) {
                        return '<span class="badge bg-danger">Close</span>';
                    }
                },
                searchable: true
            },
            
            {
                data: 'id',
                name: 'id',
                render: function(data, type, full, meta) {
                    if(full.topup_type == 0)
                    {
                        if(full.up_status == 0)
                        {
                           return '<button type="submit" class="btn btn-danger" style="width: 100%;" onclick="withdrawalModal('+data+', `'+full.paid_amount+'`, `'+full.receive_return+'`, `'+full.created_at+'`)">Withdrawal</button>';
                        }
                        else if(full.up_status == 1)
                        {
                           return '<button type="submit" class="btn btn-warning" style="width: 100%;">Pending</button>';
                        }
                        else if(full.up_status == 2)
                        {
                           return '<button type="submit" class="btn btn-success" style="width: 100%;">Success</button>';
                        }
                    }
                    else
                    {
                        return '';
                    }
                },
                searchable: false
            },
        ]
    });
}

let withdrawal_id = 0, charge = 25;

function withdrawalModal(id, amount, roi, topupdate)
{
    let month = calculateMonthsBetween(topupdate, new Date());
    
    console.log('month',month);
    
    withdrawal_id = id;
    
    if(month < 6)
    {
        var roi_deduct = roi;
    }
    else
    {
        var roi_deduct = 0;
    }
    
    let withcharge = amount * charge / 100;
    let net_withdrawal = amount-withcharge-roi_deduct;
    
    $("#topup_amount").val(amount);
    $("#total_roi").val(roi);
    $("#withdrawal_charge").val(withcharge);
    $("#capital_withdrawal").val(net_withdrawal);
    
    $("#withdrawModal").modal('show');
}

function closeWithdrawalModal()
{
    withdrawal_id = 0;
    
    $("#topup_amount").val(0);
    $("#total_roi").val(0);
    $("#withdrawal_charge").val(0);
    $("#capital_withdrawal").val(0);
    
    $("#withdrawModal").modal('hide');
}

function processWithdrawal() 
{
    if(withdrawal_id <= 0)
    {
        return false;
    }

    var reqObj = {
        _token: token,
        withdrawal_id : withdrawal_id,
    };

    blockui();

    $.ajax({
        type: 'POST',
        url: BASEPATH + "/process-submit-capital-withdrawal", 
        data: reqObj,
        dataType: 'json',
        success: function(result) {
           if (result.success) {
                successalert('Capital withdrawal request submited successfully!');
                oTable.draw(); closeWithdrawalModal();
            } else {
                erroralert(result.error);
            }
            unblockui();
        }
    });
}