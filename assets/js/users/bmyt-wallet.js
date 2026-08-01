jQuery(document).ready(function() {
	$(".bmyt_balance").val(PHP2JS.data.bmyt_balance);
    
    const listhtml = ''; $("#listtxn").html('');
    $.each(PHP2JS.data.txn_list, function (i, list) {
        if(list.token.symbol == 'BMYT'){
            listhtml += '<tr><td>'+list.type+'</td><td>'+list.tx_hash+'</td><td>'+list.from.hash+'</td><td>'+list.to.hash+'</td><td>'+parseFloat(list.total.value/1000000000000000000).toFixed(8)+'</td><td>'+list.timestamp+'</td></tr>';
        }
    });
    $("#listtxn").html(listhtml);
});
