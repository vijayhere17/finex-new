 jQuery(document).ready(function() {
    const obscureHash = (address) => {
        return address.substring(0, 4) + '...'+address.substring(address.length - 5, address.length);
    }

	$(".bmyt_balance").text(PHP2JS.data.bmyt_balance);
    
    var listhtml = ''; $("#listtxn").html('');
    $.each(PHP2JS.data.txn_list.items, function (i, list) {
        if(list.token.symbol == 'BMYT'){
            listhtml += '<tr><td>'+list.type+'</td><td><a href="https://ecroxscan.com/tx/'+list.tx_hash+'" target="_blank">'+obscureHash(list.tx_hash)+'</a></td><td>'+obscureAddress(list.from.hash)+'</td><td>'+obscureAddress(list.to.hash)+'</td><td>'+parseFloat(list.total.value/1000000000000000000).toFixed(8)+'</td><td>'+formatDate(list.timestamp)+'</td></tr>';
        }
    });
    $("#listtxn").html(listhtml);
});