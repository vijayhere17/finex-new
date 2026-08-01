@extends('admin.master')
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
		<h2>Members Summary</h2>
	</div>
</div>

<div class="row">
	<div class="col-sm-3">
		<div class="tile-stats tile-blue">
			<div class="icon"><i class="entypo-users"></i></div>
			<div class="num" data-start="0" data-end="{{ $today_member }}" data-postfix="" data-duration="1500" data-delay="0" onClick="#" style="cursor:pointer;">{{ $today_member }}</div>
			<h3>Today Members Joined</h3>
		</div>
	</div>
	<div class="col-sm-3">
		<div class="tile-stats tile-blue">
			<div class="icon"><i class="entypo-users"></i></div>
			<div class="num" data-start="0" data-end="{{ $today_a_member }}" data-postfix="" data-duration="1500" data-delay="0" onClick="#" style="cursor:pointer;">{{ $today_a_member }}</div>
			<h3>Today Activated Members</h3>
		</div>
	</div>

	<div class="col-sm-3">
		<div class="tile-stats tile-blue">
			<div class="icon"><i class="entypo-users"></i></div>
			<div class="num" data-start="0" data-end="{{ $total_member }}" data-postfix="" data-duration="1500" data-delay="0" onClick="#" style="cursor:pointer;">{{ $total_member }}</div>
			<h3>Total Joined Member</h3>
		</div>
	</div>

	<div class="col-sm-3">
		<div class="tile-stats tile-green">
			<div class="icon"><i class="entypo-users"></i></div>
			<div class="num" data-start="0" data-end="{{ $total_a_member }}" data-postfix="" data-duration="1500" data-delay="0" onClick="#" style="cursor:pointer;">{{ $total_a_member }}</div>
			<h3>Total Activated Member</h3>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<h2>Stake Summary</h2>
	</div>
</div>

<div class="row">
    <div class="col-sm-3">
		<div class="tile-stats tile-green">
			<div class="icon"><i class="entypo-users"></i></div>
			<div class="num" data-start="0" data-end="{{ $today_business }}" data-postfix="" data-duration="1500" data-delay="0" onClick="#" style="cursor:pointer;">{{ $today_business }}</div>
			<h3>Today Stake</h3>
		</div>
	</div>
	
	<div class="col-sm-3">
		<div class="tile-stats tile-green">
			<div class="icon"><i class="entypo-users"></i></div>
			<div class="num" data-start="0" data-end="{{ $weekly_business }}" data-postfix="" data-duration="1500" data-delay="0" onClick="#" style="cursor:pointer;">{{ $weekly_business }}</div>
			<h3>Weekly Stake</h3>
		</div>
	</div>
	
	<div class="col-sm-3">
		<div class="tile-stats tile-green">
			<div class="icon"><i class="entypo-users"></i></div>
			<div class="num" data-start="0" data-end="{{ $monthly_business }}" data-postfix="" data-duration="1500" data-delay="0" onClick="#" style="cursor:pointer;">{{ $monthly_business }}</div>
			<h3>Monthly Stake</h3>
		</div>
	</div>
	
	<div class="col-sm-3">
		<div class="tile-stats tile-green">
			<div class="icon"><i class="entypo-users"></i></div>
			<div class="num" data-start="0" data-end="{{ $total_business }}" data-postfix="" data-duration="1500" data-delay="0" onClick="#" style="cursor:pointer;">{{ $total_business }}</div>
			<h3>Total Stake</h3>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<h2>Earning Summary</h2>
	</div>
</div>

<div class="row">
    <div class="col-sm-3">
		<div class="tile-stats tile-blue">
			<div class="icon"><i class="entypo-users"></i></div>
			<div class="num" data-start="0" data-end="{{ $refer_bonus }}" data-postfix="" data-duration="1500" data-delay="0" onClick="#" style="cursor:pointer;">{{ $refer_bonus }}</div>
			<h3>Direct Income ($)</h3>
		</div>
	</div>
	<div class="col-sm-3">
		<div class="tile-stats tile-blue">
			<div class="icon"><i class="entypo-users"></i></div>
			<div class="num" data-start="0" data-end="{{ $refer_upline_bonus }}" data-postfix="" data-duration="1500" data-delay="0" onClick="#" style="cursor:pointer;">{{ $refer_upline_bonus }}</div>
			<h3>Daily ROI ($)</h3>
		</div>
	</div>
	<div class="col-sm-3">
		<div class="tile-stats tile-blue">
			<div class="icon"><i class="entypo-users"></i></div>
			<div class="num" data-start="0" data-end="{{ $binary_bonus }}" data-postfix="" data-duration="1500" data-delay="0" onClick="#" style="cursor:pointer;">{{ $binary_bonus }}</div>
			<h3>Team Level ROI Income ($)</h3>
		</div>
	</div>
	<div class="col-sm-3">
		<div class="tile-stats tile-blue">
			<div class="icon"><i class="entypo-users"></i></div>
			<div class="num" data-start="0" data-end="{{ $reward_salary_bonus ?? 0 }}" data-postfix="" data-duration="1500" data-delay="0" onClick="#" style="cursor:pointer;">{{ $reward_salary_bonus ?? 0 }}</div>
			<h3>Reward Salary ($)</h3>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-sm-3">
		<div class="tile-stats tile-green">
			<div class="icon"><i class="entypo-users"></i></div>
			<div class="num" data-start="0" data-end="{{ $booster_bonus ?? 0 }}" data-postfix="" data-duration="1500" data-delay="0" onClick="#" style="cursor:pointer;">{{ $booster_bonus ?? 0 }}</div>
			<h3>Booster ($)</h3>
		</div>
	</div>
	<div class="col-sm-3">
		<div class="tile-stats tile-green">
			<div class="icon"><i class="entypo-users"></i></div>
			<div class="num" data-start="0" data-end="{{ $locked_unlock_bonus ?? 0 }}" data-postfix="" data-duration="1500" data-delay="0" onClick="#" style="cursor:pointer;">{{ $locked_unlock_bonus ?? 0 }}</div>
			<h3>Locked Unlock ($)</h3>
		</div>
	</div>
	<div class="col-sm-3">
		<div class="tile-stats tile-orange">
			<div class="icon"><i class="entypo-users"></i></div>
			<div class="num" data-start="0" data-end="{{ $locked_reward_total ?? 0 }}" data-postfix="" data-duration="1500" data-delay="0" onClick="#" style="cursor:pointer;">{{ $locked_reward_total ?? 0 }}</div>
			<h3>Locked Remaining ($)</h3>
		</div>
	</div>
	<div class="col-sm-3">
		<div class="tile-stats tile-red">
			<div class="icon"><i class="entypo-users"></i></div>
			<div class="num" data-start="0" data-end="{{ $expired_reward_total ?? 0 }}" data-postfix="" data-duration="1500" data-delay="0" onClick="#" style="cursor:pointer;">{{ $expired_reward_total ?? 0 }}</div>
			<h3>Expired Locked ($)</h3>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-sm-3">
		<div class="tile-stats tile-blue">
			<div class="icon"><i class="entypo-users"></i></div>
			<div class="num" data-start="0" data-end="{{ $unlocked_reward_total ?? 0 }}" data-postfix="" data-duration="1500" data-delay="0" onClick="#" style="cursor:pointer;">{{ $unlocked_reward_total ?? 0 }}</div>
			<h3>Unlocked Total ($)</h3>
		</div>
	</div>
	<div class="col-sm-3">
		<div class="tile-stats tile-blue">
			<div class="icon"><i class="entypo-users"></i></div>
			<div class="num" data-start="0" data-end="{{ $generate_bonus }}" data-postfix="" data-duration="1500" data-delay="0" onClick="#" style="cursor:pointer;">{{ $generate_bonus }}</div>
			<h3>Total Generated ($)</h3>
		</div>
	</div>
</div> 

<div class="row">
	<div class="col-md-12">
		<h2>Today Withdrawal Summery</h2>
	</div>
</div>


<div class="row">
    <div class="col-sm-3">
		<div class="tile-stats tile-orange">
			<div class="icon"><i class="entypo-users"></i></div>
			<div class="num" data-start="0" data-end="{{ $t_pending_w }}" data-postfix="" data-duration="1500" data-delay="0" onClick="#" style="cursor:pointer;">{{ $t_pending_w }}</div>
			<h3>Today Pending Withdrawal (Coin)</h3>
		</div>
	</div>
	<div class="col-sm-3">
		<div class="tile-stats tile-blue">
			<div class="icon"><i class="entypo-users"></i></div>
			<div class="num" data-start="0" data-end="{{ $t_processing_w }}" data-postfix="" data-duration="1500" data-delay="0" onClick="#" style="cursor:pointer;">{{ $t_processing_w }}</div>
			<h3>Today Processing Withdrawal (Coin)</h3>
		</div>
	</div>
	<div class="col-sm-3">
		<div class="tile-stats tile-green">
			<div class="icon"><i class="entypo-users"></i></div>
			<div class="num" data-start="0" data-end="{{ $t_success_w }}" data-postfix="" data-duration="1500" data-delay="0" onClick="#" style="cursor:pointer;">{{ $t_success_w }}</div>
			<h3>Today Success Withdrawal (Coin)</h3>
		</div>
	</div>
	<div class="col-sm-3">
		<div class="tile-stats tile-red">
			<div class="icon"><i class="entypo-users"></i></div>
			<div class="num" data-start="0" data-end="{{ $t_rejected_w }}" data-postfix="" data-duration="1500" data-delay="0" onClick="#" style="cursor:pointer;">{{ $t_rejected_w }}</div>
			<h3>Today Rejected Withdrawal (Coin)</h3>
		</div>
	</div>
</div>    


<div class="row">
	<div class="col-md-12">
		<h2>Total Withdrawal Summery</h2>
	</div>
</div>

<div class="row">
    <div class="col-sm-3">
		<div class="tile-stats tile-orange">
			<div class="icon"><i class="entypo-users"></i></div>
			<div class="num" data-start="0" data-end="{{ $pending_w }}" data-postfix="" data-duration="1500" data-delay="0" onClick="#" style="cursor:pointer;">{{ $pending_w }}</div>
			<h3>Pending Withdrawal (Coin)</h3>
		</div>
	</div>
	<div class="col-sm-3">
		<div class="tile-stats tile-blue">
			<div class="icon"><i class="entypo-users"></i></div>
			<div class="num" data-start="0" data-end="{{ $processing_w }}" data-postfix="" data-duration="1500" data-delay="0" onClick="#" style="cursor:pointer;">{{ $processing_w }}</div>
			<h3>Processing Withdrawal (Coin)</h3>
		</div>
	</div>
	<div class="col-sm-3">
		<div class="tile-stats tile-green">
			<div class="icon"><i class="entypo-users"></i></div>
			<div class="num" data-start="0" data-end="{{ $success_w }}" data-postfix="" data-duration="1500" data-delay="0" onClick="#" style="cursor:pointer;">{{ $success_w }}</div>
			<h3>Success Withdrawal (Coin)</h3>
		</div>
	</div>
	<div class="col-sm-3">
		<div class="tile-stats tile-red">
			<div class="icon"><i class="entypo-users"></i></div>
			<div class="num" data-start="0" data-end="{{ $rejected_w }}" data-postfix="" data-duration="1500" data-delay="0" onClick="#" style="cursor:pointer;">{{ $rejected_w }}</div>
			<h3>Rejected Withdrawal (Coin)</h3>
		</div>
	</div>
</div>    
@endsection
@section('jscontent')
<script>

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