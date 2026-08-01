let coin_rate = PHP2JS.data.coin_rate;
let bmyt_contract_addr = PHP2JS.data.usdt_con_addr;
let bmyt_contract_abi = JSON.parse(PHP2JS.data.usdt_con_abi);

function getcalculationbmyt(){
    var amount = $("input[type=radio]:checked").attr('stakeamount');
    var coin = $("input[type=radio]:checked").attr('stakecoin');
    var monthly = $("input[type=radio]:checked").attr('percantage');
    var bonus = $("input[type=radio]:checked").attr('bonus');
    
    var ptype = $("input[type=radio]:checked").attr('staketype');
   
    var extra = (amount*bonus)/100;
    var total_stake = parseFloat(amount)+parseFloat(extra);
   
    var total_stake_coin = parseFloat(coin);
    
    if(ptype > 0){
        var release_bmyt = parseFloat(total_stake_coin).toFixed(8);
        $("#stype").text('After 25 Months');
    }else{
        var release_bmyt = parseFloat(total_stake_coin*monthly/100).toFixed(8);
        $("#stype").text('Monthly');
    }
    
    $("#txt_amount").text(parseFloat(amount).toFixed(2));
    $("#txt_bonus").text(parseFloat(extra).toFixed(2));
    $("#txt_stake_bmyt").text(total_stake_coin);
    $("#txt_release_bmyt").text(release_bmyt);
    
    var payable = (amount/coin_rate);
    $("#txt_payable_bmyt").text(parseFloat(payable).toFixed(8));
}

jQuery('.btn-submit').bind('click', function(e) {
    e.preventDefault();
    processstake();
});

async function processstake() 
{
    if(!$("input[type='radio']").is(':checked')){
        erroralert('Please select stake package;');
        return false;
    }

    const stake_id = $("input[type=radio]:checked").attr('stakeid');

    const amount = $("input[type=radio]:checked").attr('stakeamount');
    const bonus = $("input[type=radio]:checked").attr('bonus');

    const extra = (amount*bonus)/100;
    const total_stake = parseFloat(amount)+parseFloat(extra);

    // ---------------------------------------------------------------------------
    
    blockui();

    await addecroxnetwork();
    
    var payable = parseFloat(amount/coin_rate).toFixed(8);
        
    var amountwei = web3.utils.toWei(payable.toString(), 'ether');
    
    const payContract = await new web3.eth.Contract(bmyt_contract_abi, bmyt_contract_addr);
    
    await payContract.methods.transfer(PHP2JS.data.to_address, amountwei).send({ 
        from: accounts[0], gas: 2000000
    }).then(function (txHash){
        var reqObj = {
            _token: token,
            stake_id : stake_id,
            bonus : bonus,
            extra : extra,
            total_stake : total_stake,
            hash : txHash.transactionHash
        };

        $.ajax({
            type: 'POST',
            url: BASEPATH + "/process-submit-travel-package",
            data: reqObj,
            dataType: 'json',
            success: function(result) {
                if (result.success) {
                    successalert('Buy request submited successfully!')
                } else {
                    erroralert(result.error);
                }
                unblockui();
            }
        });
    }).catch(function (error){
        unblockui();
        erroralert(JSON.stringify(error));
    });
}

async function addecroxnetwork(){
    try {
        await window.ethereum.request({
            method: 'wallet_addEthereumChain',
            params: [{ 
                chainId: web3.utils.toHex(988207), 
                chainName:'Ecrox Chain',
                rpcUrls:['https://mainnet-rpc.ecroxscan.com'],                   
                blockExplorerUrls:['https://ecroxscan.com:443'],  
                nativeCurrency: { 
                    symbol:'ECROX',   
                    decimals: 18
                } 
            }]       
        });
    } catch (addError) {
        erroralert(addError);
    }
}

// ===============================================================================================================================================================================

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
            "url": BASEPATH + "/get-buy-travel-package-request",
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
                data: 'amount',
                name: 'amount',
                render: function(data, type, full, meta) {
                    return '$'+data;
                },
                searchable: true
            },
            
            {
                data: 'bonus',
                name: 'bonus',
                render: function(data, type, full, meta) {
                    return '$'+data;
                },
                searchable: true
            },

            {
                data: 'total_amount',
                name: 'total_amount',
                render: function(data, type, full, meta) {
                    return '$'+data;
                },
                searchable: true
            },
            
            {
                data: 'stake_coin',
                name: 'stake_coin',
                render: function(data, type, full, meta) {
                    return data+' BMYT';
                },
                searchable: true
            },

            {
                data: 'hash',
                name: 'hash',
                render: function(data, type, full, meta) {
                    return '<a href="'+PHP2JS.data.txn_hash_url+'/'+data+'" target="_blank">View Hash</a>';
                },
                searchable: true
            },

            {
                data: 'status',
                name: 'status',
                render: function(data, type, full, meta) {
                    if (data == 0) {
                        return '<span class="badge bg-warning">Pending</span>';
                    } else if(data == 1) {
                        return '<span class="badge bg-info">Process</span>';
                    } else if(data == 2) {
                        return '<span class="badge bg-success">Success</span>';
                    }  else if(data == 3) {
                        return '<span class="badge bg-danger">Rejected</span>';
                    }
                },
                searchable: true
            },
        ]
    });
}