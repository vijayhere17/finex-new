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
	
	<div class="col-sm-6">
		<div class="tile-stats tile-green">
			<div class="icon"><i class="entypo-users"></i></div>
			<div class="num swap_dep_address" style="cursor:pointer;"></div>
			<h3>Swap Systems Address</h3>
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
    $('document').ready(function(){	
	    $(".swap_address").text(swap_contract);
	    
		setInterval(async function(){
			const contractcoin = await new web3.eth.Contract(coin_abi, coin_contract);
            const coin_balance = await contractcoin.methods.balanceOf(swap_contract).call();
            const coin_bal = parseFloat(coin_balance/1000000000000000000).toFixed(8);

            $(".alc_balance").text(coin_bal);

            const contractusdt = await new web3.eth.Contract(coin_abi, usdt_contract);
            const usdt_balance = await contractusdt.methods.balanceOf(swap_contract).call();
            const usdt_bal = parseFloat(usdt_balance/1000000).toFixed(6);

            $(".usdt_balance").text(usdt_bal);
            
            const swapcontract = await new web3.eth.Contract(swap_abi, swap_contract);
    
            const alcRate = await swapcontract.methods.alcRate().call();
            const alc_rate = parseFloat(alcRate/1000000000000000000).toFixed(8);
            $(".alc_rate").text(alc_rate);
            
            const usdtRate = await swapcontract.methods.usdtRate().call();
            const usdt_rate = parseFloat(usdtRate/1000000).toFixed(6);
            $(".usdt_rate").text(usdt_rate);
            
            const swapDepAddr = await swapcontract.methods.systemAddress().call();
            $(".swap_dep_address").text(swapDepAddr);
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