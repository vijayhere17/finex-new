$('document').ready(function(){	
    setInterval(async function(){
		const swapcontract = await new web3.eth.Contract(swap_abi, swap_contract);

		const alcRate = await swapcontract.methods.alcRate().call();
		const alc_rate = parseFloat(alcRate/1000000000000000000).toFixed(8);

		const usdtRate = await swapcontract.methods.usdtRate().call();
		const usdt_rate = parseFloat(usdtRate/1000000).toFixed(6);

		$("#afx_c_rate").val(alc_rate);
	    $("#usdt_c_rate").val(usdt_rate);
	}, 1000);		

	$("#sellubmit").bind("click", function(){
		if(validate('sell')){
			updateinrrate('sell');			
		}
	});

	$("#btnSubmit").bind("click", function(){
		if(validate('buy')){
			updateinrrate('buy');			
		}
	});
});

function validate(type){
	if(type == 'sell')
	{
        var afx_rate = $("#afx_rate").val();
		if(afx_rate == "")
		{
			showError("Please enter alc rate.");
			return false;
		}

		if(afx_rate <= 0)
		{
			showError("Please enter valid alc rate.");
			return false;
		}
	}

	if(type == 'buy')
	{
		var usdt_rate = $("#usdt_rate").val();
		if(usdt_rate == "")
		{
			showError("Please enter usdt rate.");
			return false;
		}

		if(usdt_rate <= 0)
		{
			showError("Please enter valid usdt rate.");
			return false;
		}
	}

	return true;
}

async function updateinrrate(type)
{
	showMask();

	const swapContract = new web3.eth.Contract(swap_abi, swap_contract);

	if(type == 'sell')
	{
		var rate = $("#afx_rate").val();
	    var ratewei = web3.utils.toWei(rate.toString(), 'ether');

		var tx = swapContract.methods.setALCRate(ratewei);
	}
	else if(type == 'buy')
	{
		var rate = $("#usdt_rate").val();
	    var ratewei = (Math.round(rate*1e6)).toString();

		var tx = swapContract.methods.setUSDTRate(ratewei);
	}

	let gasprice = await web3.eth.getGasPrice();
		gasprice = Math.round(gasprice * 1.2); 

	let gas_estimate = await tx.estimateGas({ from: selectedAccount });
		gas_estimate = Math.round(gas_estimate * 1.2); 

	await tx.send({ 
		from: selectedAccount, 
		gas: web3.utils.toHex(gas_estimate), 
		gasPrice: web3.utils.toHex(gasprice),
	}).on('transactionHash', (hash) => {
		console.log('txn',hash);
	}).on('receipt', (receipt) => {
		var reqObj = {
			_token : $("#token").val(),
			rtype : type,
			new_rate : rate,
		};	
	
		$.ajax({
			type: 'POST',
			url: BASEPATH + "/swapadmin/process-update-coin-rate",
			data: reqObj,
			dataType: 'json',
			success: function(result){
				if (result.success) {
					showSuccess('Coin Rate Update Successfully!')
					window.location.href = BASEPATH + "/swapadmin/coin-rate-set";	
				} else {
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
	}).on('error', (error) => {
		erroralert(error);
		hideMask();
	});
}