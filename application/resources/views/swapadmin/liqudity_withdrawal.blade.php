@extends('swapadmin.master')
@section('title', '')
@section('extra')
@endsection
@section('content')
<ol class="breadcrumb bc-3">
	<li>
		<a href="{{URL::to('/')}}/swapadmin/home"><i class="entypo-home"></i>Home</a>
	</li>
	<li>
		<a href="javascript://">Admin</a>
	</li>
	<li class="active">
		<strong>{{ $page_titel }}</strong>
	</li>
</ol>
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-primary" data-collapsed="0">
			<div class="panel-heading">
				<div class="panel-title">{{ $page_titel }}</div>
				<div class="panel-options"></div>
			</div>
			<div class="panel-body">
				<div name="form1" class="form-horizontal form-groups-bordered validate" id="form1" role="form">
					<div id="errorDiv" class="form-group errorDiv" style="display:none;">
						<span id="errorMsg">
							Error goes here...
						</span>
					</div>
					
					<div class="form-group">
						<label for="field-1" class="col-sm-4 control-label">EDU Liqudity</label>
						<div class="col-sm-4">
							<input name="alc_liq" type="number" class="form-control" id="alc_liq" readonly>
						</div>
					</div>
					
					<div class="form-group">
						<label for="field-1" class="col-sm-4 control-label">USDT Liqudity</label>
						<div class="col-sm-4">
							<input name="usdt_liq" type="number" class="form-control" id="usdt_liq" readonly>
						</div>
					</div>
					
					<div class="form-group">
						<label for="field-1" class="col-sm-4 control-label">Select Withdraw Liqudity</label>
						<div class="col-sm-4">
						     <select id="withdraw_liq" class="form-control">
						         <option value="" decimal="0">Select Withdraw</option>
    						     <option value="0x73e0D982Bdf7552884E2BEdB5B3C3f182e2AcD90" decimal="18">EDU Withdraw</option>
    						 </select>
						</div>
					</div>

					<div class="form-group">
						<label for="field-1" class="col-sm-4 control-label">Withdraw Liqudity</label>
						<div class="col-sm-4">
							<input name="withdraw_amount" type="number" class="form-control" id="withdraw_amount">
						</div>
						<div class="col-sm-4">
						    <input name="SUBMIT" type="submit" class="btn btn-warning" onclick="withdrawalLiq();" value="Withdraw">
						</div>
					</div>
			    </div>
			</div>
		</div>
	</div>
</div>
<br />
@endsection
@section('jscontent')
	<script>
        $('document').ready(function(){	
    	  	setInterval(async function(){
    			const contractcoin = await new web3.eth.Contract(coin_abi, coin_contract);
                const coin_balance = await contractcoin.methods.balanceOf(swap_contract).call();
                const coin_bal = parseFloat(coin_balance/1000000000000000000).toFixed(8);
    
                $("#alc_liq").val(coin_bal);
    
                const contractusdt = await new web3.eth.Contract(coin_abi, usdt_contract);
                const usdt_balance = await contractusdt.methods.balanceOf(swap_contract).call();
                const usdt_bal = parseFloat(usdt_balance/1000000).toFixed(6);
    
                $("#usdt_liq").val(usdt_bal);
        	}, 1000);		
    	});
    	
    	async function withdrawalLiq()
        {
            var withdraw_liq = $("#withdraw_liq").val();
            var withdraw_amount = $("#withdraw_amount").val();
            var decimal = $('#withdraw_liq').find('option:selected').attr('decimal');
            
            if(withdraw_liq == "")
    		{
    			showError("Please select withdrawal liqudity");
    			return false;
    		}
    		
    		if(withdraw_amount == "")
    		{
    			showError("Please enter withdrawal liqudity amount");
    			return false;
    		}
    
    		if(withdraw_amount <= 0)
    		{
    			showError("Please enter valid withdrawal liqudity amount");
    			return false;
    		}
    		
        	showMask();
        
        	const swapContract = new web3.eth.Contract(swap_abi, swap_contract);
        
        	if(decimal == '18')
        	{
        		var amountwei = web3.utils.toWei(withdraw_amount.toString(), 'ether');
        	}
        	else if(decimal == '6')
        	{
        	    var amountwei = (Math.round(withdraw_amount*1e6)).toString();
        	}
        	
        	var tx = swapContract.methods.claimTokens(withdraw_liq, amountwei);
        
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
        	    showSuccess('Withdrawal successfully!')
        		window.location.href = BASEPATH + "/swapadmin/liqudity-withdrawal";	
        	}).on('error', (error) => {
        		erroralert(error);
        		hideMask();
        	});
        }
    </script>
    
	<script src="{{ URL::asset('assets/swapadmin/js/custom/coin-rate.0.4.js') }}"></script>
@endsection