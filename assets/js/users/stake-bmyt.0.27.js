let coin_rate = PHP2JS.data.coin_rate;
let usdt_contract_addr = PHP2JS.data.usdt_con_addr;
let usdt_contract_abi = JSON.parse(PHP2JS.data.usdt_con_abi);

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
}

jQuery('.btn-submit').bind('click', function(e) {
    e.preventDefault();
    processstake();
});

async function processstake() {
    try {
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
        
        // switch bnb network
        await window.ethereum.request({
            method: 'wallet_switchEthereumChain',
            params: [{ chainId: web3.utils.toHex(chainId) }]
        });
        
        var amountwei = web3.utils.toWei(amount.toString(), 'ether');
            
        const payContract = await new web3.eth.Contract(usdt_contract_abi, usdt_contract_addr);

        const tx = payContract.methods.transfer(PHP2JS.data.to_address, amountwei);

        var gasprice = await web3.eth.getGasPrice();
            gasprice = gasprice.toString();
            gasprice = Math.round(gasprice * 1.2);

        var gas_estimate = await tx.estimateGas({ from: accounts[0] });
            gas_estimate = gas_estimate.toString();
            gas_estimate = Math.round(gas_estimate * 1.2);

        const transaction = {
            from: accounts[0],
            to: usdt_contract_addr,
            data: tx.encodeABI(),
            gas: web3.utils.toHex(gas_estimate),
            gasPrice: web3.utils.toHex(gasprice),
        };

        const txHash = await web3.eth.sendTransaction(transaction);

        const receipt = await waitForConfirmation(txHash.transactionHash);

        // ---------------------------------------------------------------------------

        var reqObj = {
            _token: token,
            stake_id : stake_id,
            bonus : bonus,
            extra : extra,
            total_stake : total_stake,
            hash : receipt.transactionHash
        };

        $.ajax({
            type: 'POST',
            url: BASEPATH + "/process-submit-stake",
            data: reqObj,
            dataType: 'json',
            success: function(result) {
                if (result.success) {
                    successalert('Stake request submited successfully!')
                } else {
                    erroralert(result.error);
                }
                unblockui();
            }
        });
    } catch (error) {
        unblockui();
        erroralert(JSON.stringify(error));
    }       
}