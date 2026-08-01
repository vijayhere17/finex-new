// JavaScript Document
$('document').ready(function(){	
	bindEvents();
	
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
            "url": BASEPATH + "/admin/get-offers-list",
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
                data: 'start_date',
                name: 'start_date',
                searchable: true,
                render: function(data, type, full, meta) {
                    return data;
                },
                searchable: true
            },

            {
                data: 'end_date',
                name: 'end_date',
                searchable: true,
                render: function(data, type, full, meta) {
                    return data;
                },
                searchable: true
            },

            {
                data: 'target',
                name: 'target',
                searchable: true,
                render: function(data, type, full, meta) {
                    return data;
                },
                searchable: true
            },

            {
                data: 'no_winners',
                name: 'no_winners',
                searchable: true,
                render: function(data, type, full, meta) {
                    return data;
                },
                searchable: true
            },

            {
                data: 'created_at',
                name: 'created_at',
                searchable: true,
                render: function(data, type, full, meta) {
                    return data;
                },
                searchable: true
            },
            
            {
                data: 'id',
                name: 'id',
                searchable: true,
                render: function(data, type, full, meta) {
                    if(full.status == 0)
                    {
                        return '<a href="Javascript:winnerPopup('+data+')" class="btn btn-green">Add Winner</a>&nbsp;<a href="Javascript:updateWinners('+data+')" class="btn btn-black">Close</a>';
                    }
                    else if(full.status == 1)
                    {
                        return '<a href="Javascript:" class="btn btn-green">View Winner</a>&nbsp;<a href="Javascript:" class="btn btn-success">Closed</a>';
                    }
                },
                searchable: true
            }
        ]
    });
}

function winnerPopup(id)
{
    $("#jackpot_id").val(id);
    $("#winnerModal").modal({ backdrop: 'static', keyboard: false });
}

function updateWinners(id)
{
	var reqObj = {
		_token : $("#token").val(),
		jackpot_id : id,
	};	
	
	showMask();
	
	$.ajax({
    	type: 'POST',
    	url: BASEPATH + "/admin/submit-close-winners",
    	data: reqObj,
    	dataType: 'json',
    	success: function(result)
	    {
			if(result.success)
			{
				showSuccess('Transaction Sucessfully!');
				oTable.draw();
			}
			else
			{
				showError(Errors[result.error_code]);
			}
			hideMask();
		},
		statusCode: {
			500: function() {
		  		showError("An error occurred. Please try later.");
				hideMask();
			}
		}			
	});
}

function addWinners()
{
	var jackpot_id = $("#jackpot_id").val();
	var winners = getWinner();
	
	if(jackpot_id == "")
	{
		showError("Please select jackpot.");
		return false;
	}
	
	if(winners == [])
	{
		showError("Please enter a winners.");
		return false;
	}
	
	var reqObj = {
		_token : $("#token").val(),
		jackpot_id : jackpot_id,
		winners : winners
	};	
	
	showMask();
	
	$.ajax({
    	type: 'POST',
    	url: BASEPATH + "/admin/submit-add-winners",
    	data: reqObj,
    	dataType: 'json',
    	success: function(result)
	    {
			if(result.success)
			{
				showSuccess('Transaction Sucessfully!');
				 $("#winnerModal").modal('hide');
				oTable.draw();
			}
			else
			{
				showError(Errors[result.error_code]);
			}
			hideMask();
		},
		statusCode: {
			500: function() {
		  		showError("An error occurred. Please try later.");
				hideMask();
			}
		}			
	});
}

function bindEvents()
{
	$("#btnSubmit").bind("click", function(){
		updateMember();
	});
}

function updateMember()
{
	var start_date = $("#start_date").val();
	var start_end = $("#start_end").val();
	var target_amount = $("#target_amount").val();
	var price = $("#price").val();
	var winner_no = $("#winner_no").val();
	var winner_per = getWinnerValues();
	
	if(start_date == "")
	{
		showError("Please select start date.");
		return false;
	}
	
	if(start_end == "")
	{
		showError("Please select a end date.");
		return false;
	}
	
	if(target_amount == "")
	{
		showError("Please enter a target business/topup.");
		return false;
	}
	
	if(price == "")
	{
		showError("Please enter a pool price.");
		return false;
	}
	
	if(winner_no == "")
	{
		showError("Please enter a no of winner.");
		return false;
	}
	
	if(winner_per == [])
	{
		showError("Please enter a winner percentage.");
		return false;
	}
	
	var reqObj = {
		_token : $("#token").val(),
		start_date : start_date,
		start_end : start_end,
		target_amount : target_amount,
		price : price,
		winner_no : winner_no,
		winner_per : winner_per
	};	
	
	showMask();
	
	$.ajax({
    	type: 'POST',
    	url: BASEPATH + "/admin/submit-create-new-offers",
    	data: reqObj,
    	dataType: 'json',
    	success: function(result)
	    {
			if(result.success)
			{
				showSuccess('Transaction Sucessfully!');
				oTable.draw();
			}
			else
			{
				showError(Errors[result.error_code]);
			}
			hideMask();
		},
		statusCode: {
			500: function() {
		  		showError("An error occurred. Please try later.");
				hideMask();
			}
		}			
	});
}