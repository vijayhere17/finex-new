@extends('swapadmin.master')
@section('title', 'Home')
@section('extra')
<style>
	.bitcoin {
		font-style: normal;
		font-weight: normal;
		speak: none;
		display: inline-block;
		text-decoration: inherit;
		width: 1em;
		text-align: center;
		opacity: .8;
		font-variant: normal;
		text-transform: none;
		line-height: 1em !important;
		margin-left: .2em;
	}
	
	.tile-stats h3 {
        font-size: 15px;
        margin-top: 5px;
    }
</style>
@endsection
@section('content')

<div class="row">
	<div class="col-md-12">
		<h2>Smart Contract Summary</h2>
	</div>
</div>

<div class="row">
	<div class="col-sm-3">
		<div class="tile-stats tile-blue">
			<div class="icon"><i class="entypo-users"></i></div>
			<div class="num alc_rate" style="cursor:pointer;">0.00</div>
			<h3>EDU Rate</h3>
		</div>
	</div>
	<div class="col-sm-3">
		<div class="tile-stats tile-blue">
			<div class="icon"><i class="entypo-users"></i></div>
			<div class="num usdt_rate" style="cursor:pointer;">0.00</div>
			<h3>USDT Rate</h3>
		</div>
	</div>

	<div class="col-sm-6">
		<div class="tile-stats tile-blue">
			<div class="icon"><i class="entypo-users"></i></div>
			<div class="num swap_address" style="cursor:pointer;"></div>
			<h3>Swap Contract</h3>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<h2>Liquidity Summary</h2>
	</div>
</div>

<div class="row">
    <div class="col-sm-6">
		<div class="tile-stats tile-blue">
			<div class="icon"><i class="entypo-users"></i></div>
			<div class="num alc_balance" style="cursor:pointer;">0.00000000</div>
			<h3>EDU Liquidity</h3>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="tile-stats tile-blue">
			<div class="icon"><i class="entypo-users"></i></div>
			<div class="num usdt_balance" style="cursor:pointer;">0.00000000</div>
			<h3>USDT Liquidity</h3>
		</div>
	</div>
</div> 

@endsection
@section('jscontent')
<script>
    const swap_abi = [{"inputs":[{"internalType":"contract IERC20","name":"_tokenA","type":"address"},{"internalType":"contract IERC20","name":"_tokenB","type":"address"},{"internalType":"uint256","name":"_usdtRate","type":"uint256"},{"internalType":"uint256","name":"_alcRate","type":"uint256"},{"internalType":"address","name":"_systemAddress","type":"address"}],"stateMutability":"nonpayable","type":"constructor"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"owner","type":"address"},{"indexed":false,"internalType":"uint256","name":"amountA","type":"uint256"},{"indexed":false,"internalType":"uint256","name":"amountB","type":"uint256"}],"name":"LiquidityAdded","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"previousOwner","type":"address"},{"indexed":true,"internalType":"address","name":"newOwner","type":"address"}],"name":"OwnershipTransferred","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"swapper","type":"address"},{"indexed":true,"internalType":"address","name":"tokenFrom","type":"address"},{"indexed":true,"internalType":"address","name":"tokenTo","type":"address"},{"indexed":false,"internalType":"uint256","name":"amountIn","type":"uint256"},{"indexed":false,"internalType":"uint256","name":"amountOut","type":"uint256"}],"name":"Swap","type":"event"},{"inputs":[{"internalType":"uint256","name":"amountA","type":"uint256"},{"internalType":"uint256","name":"amountB","type":"uint256"}],"name":"addLiquidity","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"alcRate","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"uint256","name":"","type":"uint256"}],"name":"allSwaps","outputs":[{"internalType":"address","name":"user","type":"address"},{"internalType":"address","name":"tokenFrom","type":"address"},{"internalType":"address","name":"tokenTo","type":"address"},{"internalType":"uint256","name":"amountIn","type":"uint256"},{"internalType":"uint256","name":"amountOut","type":"uint256"},{"internalType":"uint256","name":"timestamp","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"uint256","name":"amountIn","type":"uint256"},{"internalType":"string","name":"token","type":"string"}],"name":"calculate","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"uint256","name":"_amount","type":"uint256"}],"name":"claimCoin","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"_token","type":"address"},{"internalType":"uint256","name":"_amount","type":"uint256"}],"name":"claimTokens","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"numerator","type":"uint256"},{"internalType":"uint256","name":"denominator","type":"uint256"},{"internalType":"address","name":"tokenAddr","type":"address"}],"name":"division","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"getAllSwaps","outputs":[{"components":[{"internalType":"address","name":"user","type":"address"},{"internalType":"address","name":"tokenFrom","type":"address"},{"internalType":"address","name":"tokenTo","type":"address"},{"internalType":"uint256","name":"amountIn","type":"uint256"},{"internalType":"uint256","name":"amountOut","type":"uint256"},{"internalType":"uint256","name":"timestamp","type":"uint256"}],"internalType":"struct ALCSwap.SwapTransaction[]","name":"","type":"tuple[]"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"user","type":"address"}],"name":"getUserSwaps","outputs":[{"components":[{"internalType":"address","name":"user","type":"address"},{"internalType":"address","name":"tokenFrom","type":"address"},{"internalType":"address","name":"tokenTo","type":"address"},{"internalType":"uint256","name":"amountIn","type":"uint256"},{"internalType":"uint256","name":"amountOut","type":"uint256"},{"internalType":"uint256","name":"timestamp","type":"uint256"}],"internalType":"struct ALCSwap.SwapTransaction[]","name":"","type":"tuple[]"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"owner","outputs":[{"internalType":"address","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"uint256","name":"_alcRate","type":"uint256"}],"name":"setALCRate","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"_address","type":"address"}],"name":"setSystemAddress","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"_usdtRate","type":"uint256"}],"name":"setUSDTRate","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"amountIn","type":"uint256"},{"internalType":"string","name":"token","type":"string"},{"internalType":"uint256","name":"expectedOutput","type":"uint256"},{"internalType":"uint256","name":"deadline","type":"uint256"}],"name":"swap","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"systemAddress","outputs":[{"internalType":"address","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"tokenA","outputs":[{"internalType":"contract IERC20","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"tokenB","outputs":[{"internalType":"contract IERC20","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"newOwner","type":"address"}],"name":"transferOwnership","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"usdtRate","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"","type":"address"},{"internalType":"uint256","name":"","type":"uint256"}],"name":"userSwaps","outputs":[{"internalType":"address","name":"user","type":"address"},{"internalType":"address","name":"tokenFrom","type":"address"},{"internalType":"address","name":"tokenTo","type":"address"},{"internalType":"uint256","name":"amountIn","type":"uint256"},{"internalType":"uint256","name":"amountOut","type":"uint256"},{"internalType":"uint256","name":"timestamp","type":"uint256"}],"stateMutability":"view","type":"function"}],
          coin_abi = [{"inputs":[],"payable":false,"stateMutability":"nonpayable","type":"constructor"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"owner","type":"address"},{"indexed":true,"internalType":"address","name":"spender","type":"address"},{"indexed":false,"internalType":"uint256","name":"value","type":"uint256"}],"name":"Approval","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"previousOwner","type":"address"},{"indexed":true,"internalType":"address","name":"newOwner","type":"address"}],"name":"OwnershipTransferred","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"from","type":"address"},{"indexed":true,"internalType":"address","name":"to","type":"address"},{"indexed":false,"internalType":"uint256","name":"value","type":"uint256"}],"name":"Transfer","type":"event"},{"constant":true,"inputs":[],"name":"_decimals","outputs":[{"internalType":"uint8","name":"","type":"uint8"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"_name","outputs":[{"internalType":"string","name":"","type":"string"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"_symbol","outputs":[{"internalType":"string","name":"","type":"string"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"internalType":"address[]","name":"_user","type":"address[]"},{"internalType":"uint256","name":"value","type":"uint256"}],"name":"airdrop","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[{"internalType":"address","name":"owner","type":"address"},{"internalType":"address","name":"spender","type":"address"}],"name":"allowance","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"internalType":"address","name":"spender","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"approve","outputs":[{"internalType":"bool","name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[{"internalType":"address","name":"account","type":"address"}],"name":"balanceOf","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"basePercent","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"burn","outputs":[{"internalType":"bool","name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"decimals","outputs":[{"internalType":"uint8","name":"","type":"uint8"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"internalType":"address","name":"spender","type":"address"},{"internalType":"uint256","name":"subtractedValue","type":"uint256"}],"name":"decreaseAllowance","outputs":[{"internalType":"bool","name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"getOwner","outputs":[{"internalType":"address","name":"","type":"address"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"internalType":"address","name":"spender","type":"address"},{"internalType":"uint256","name":"addedValue","type":"uint256"}],"name":"increaseAllowance","outputs":[{"internalType":"bool","name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"maxBurning","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"internalType":"address[]","name":"_user","type":"address[]"},{"internalType":"uint256[]","name":"value","type":"uint256[]"}],"name":"multisender","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"name","outputs":[{"internalType":"string","name":"","type":"string"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"owner","outputs":[{"internalType":"address","name":"","type":"address"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[],"name":"renounceOwnership","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"symbol","outputs":[{"internalType":"string","name":"","type":"string"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"totalBurning","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"totalSupply","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"internalType":"address","name":"recipient","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"transfer","outputs":[{"internalType":"bool","name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"internalType":"address","name":"sender","type":"address"},{"internalType":"address","name":"recipient","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"transferFrom","outputs":[{"internalType":"bool","name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"internalType":"address","name":"newOwner","type":"address"}],"name":"transferOwnership","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"}],            
          coin_contract = '0x73e0D982Bdf7552884E2BEdB5B3C3f182e2AcD90',
          usdt_contract = '0xc2132D05D31c914a87C6611C10748AEb04B58e8F';
          swap_contract = '0xd24594BF1Ba6a87d99C2a0Be38599ceE454b70Fe';
</script>

<script>
    $('document').ready(function(){	
	    $(".swap_address").text(swap_contract);
	    
		setInterval(async function(){
			const contractcoin = await new web3.eth.Contract(coin_abi, coin_contract);
            const coin_balance = await contractcoin.methods.balanceOf(swap_contract).call();
            const coin_bal = parseFloat(coin_balance/1000000000000000000).toFixed(8);

            $(".alc_balance").text(coin_bal);

            const contractusdt = await new web3.eth.Contract(coin_abi, usdt_contract);
            const usdt_balance = await contractusdt.methods.balanceOf(swap_contract).call();
            const usdt_bal = parseFloat(usdt_balance/100000000).toFixed(4);

            $(".usdt_balance").text(usdt_bal);
            
            const swapcontract = await new web3.eth.Contract(swap_abi, swap_contract);
    
            const alcRate = await swapcontract.methods.alcRate().call();
            const alc_rate = parseFloat(alcRate/1000000000000000000).toFixed(8);
            $(".alc_rate").text(alc_rate);
            
            const usdtRate = await swapcontract.methods.usdtRate().call();
            const usdt_rate = parseFloat(usdtRate/100000000).toFixed(8);
            $(".usdt_rate").text(usdt_rate);
		}, 1000);		
	});
//$(document).prop('title', 'Home - ???? ??????');

jQuery(document).ready(function($)
{
	// Sample Toastr Notification
	setTimeout(function()
	{
		var opts = {
			"closeButton": true,
			"debug": false,
			"positionClass": "toast-top-right",
			"toastClass": "black",
			"onclick": null,
			"showDuration": "300",
			"hideDuration": "1000",
			"timeOut": "5000",
			"extendedTimeOut": "1000",
			"showEasing": "swing",
			"hideEasing": "linear",
			"showMethod": "fadeIn",
			"hideMethod": "fadeOut"
		};

		toastr.success("Welcome to Admin Panel!", "Hello", opts);
	}, 3000);


	// Sparkline Charts
	$('.inlinebar').sparkline('html', {type: 'bar', barColor: '#ff6264'} );
	$('.inlinebar-2').sparkline('html', {type: 'bar', barColor: '#445982'} );
	$('.inlinebar-3').sparkline('html', {type: 'bar', barColor: '#00b19d'} );
	$('.bar').sparkline([ [1,4], [2, 3], [3, 2], [4, 1] ], { type: 'bar' });
	$('.pie').sparkline('html', {type: 'pie',borderWidth: 0, sliceColors: ['#3d4554', '#ee4749','#00b19d']});
	$('.linechart').sparkline();
	$('.pageviews').sparkline('html', {type: 'bar', height: '30px', barColor: '#ff6264'} );
	$('.uniquevisitors').sparkline('html', {type: 'bar', height: '30px', barColor: '#00b19d'} );


	$(".monthly-sales").sparkline([1,2,3,5,6,7,2,3,3,4,3,5,7,2,4,3,5,4,5,6,3,2], {
		type: 'bar',
		barColor: '#485671',
		height: '80px',
		barWidth: 10,
		barSpacing: 2
	});


	// JVector Maps
	var map = $("#map");

	map.vectorMap({
		map: 'europe_merc_en',
		zoomMin: '3',
		backgroundColor: '#383f47',
		focusOn: { x: 0.5, y: 0.8, scale: 3 }
	});



	// Line Charts
	var line_chart_demo = $("#line-chart-demo");

	var line_chart = Morris.Line({
		element: 'line-chart-demo',
		data: [
			{ y: '2016-01', a: '0' , b: '23'}
		],
		xkey: 'y',
		ykeys: ['a', 'b'],
		labels: ['Visitors', 'Page Views'],
		redraw: true
	});

	line_chart_demo.parent().attr('style', '');


	// Rickshaw
	var seriesData = [ [], [] ];

	var random = new Rickshaw.Fixtures.RandomData(50);

	for (var i = 0; i < 50; i++)
	{
		random.addData(seriesData);
	}

	var graph = new Rickshaw.Graph( {
		element: document.getElementById("rickshaw-chart-demo"),
		height: 193,
		renderer: 'area',
		stroke: false,
		preserve: true,
		series: [{
				color: '#73c8ff',
				data: seriesData[0],
				name: 'Upload'
			}, {
				color: '#e0f2ff',
				data: seriesData[1],
				name: 'Download'
			}
		]
	} );

	graph.render();

	var hoverDetail = new Rickshaw.Graph.HoverDetail( {
		graph: graph,
		xFormatter: function(x) {
			return new Date(x * 1000).toString();
		}
	} );

	var legend = new Rickshaw.Graph.Legend( {
		graph: graph,
		element: document.getElementById('rickshaw-legend')
	} );

	var highlighter = new Rickshaw.Graph.Behavior.Series.Highlight( {
		graph: graph,
		legend: legend
	} );

	setInterval( function() {
		random.removeData(seriesData);
		random.addData(seriesData);
		graph.update();

	}, 500 );
});


function getRandomInt(min, max)
{
	return Math.floor(Math.random() * (max - min + 1)) + min;
}

</script>

@endsection