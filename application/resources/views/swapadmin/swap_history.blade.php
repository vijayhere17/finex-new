@extends('swapadmin.master')
@section('title', '')
@section('extra')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.12/datatables.min.css"/>
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/responsive/1.0.2/css/dataTables.responsive.css"/>
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css"/>
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css"/>
<style>
	.dataTables_length {
        display: none;
	}
	.dt-buttons {
		margin-top: 16px;
	}
</style>	
@endsection
@section('content')
<ol class="breadcrumb bc-3">
	<li>
		<a href="{{URL::to('/')}}/swapadmin/home"><i class="entypo-home"></i>Home</a>
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
				    <div class="form-group">
						<div class="col-sm-12">
							<table id="tblData" border="0" width="100%" align="center" class="table table-bordered datatable">
								<thead>
									<tr class="seprator">
										<th>#</th>
										<th>Address</th>
										<th>From</th>
										<th>To</th>
										<th>Txn. Date</th>
									</tr>
								</thead>
								<tbody id="txn_list">
								</tbody>
							</table>
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
    <script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.12/datatables.min.js"></script>
	<script type="text/javascript" src="//cdn.datatables.net/responsive/1.0.2/js/dataTables.responsive.js"></script>
	<script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
	<script type="text/javascript" src="//cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
	<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
	<script type="text/javascript" src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
	<script type="text/javascript" src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
	<script type="text/javascript" src="//cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
	<script type="text/javascript" src="//cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js"></script>
	
	<script>
	    let ah = 0;

        async function getAllTxnList()
        { 
            var swapContract = await new web3.eth.Contract(swap_abi, swap_contract);
            var allswap = await swapContract.methods.getAllSwaps().call();
            
            var txn_list = ``;
        
            allswap.forEach((list) => {
        
                ah++;
                
                if(coin_contract.toLowerCase() == list.tokenFrom.toLowerCase())
                {
                    var fd = 1e18; var td = 1e6; var fc = 'EDU'; var tc = 'USDT'
                }
                else 
                {
                    var fd = 1e6; var td = 1e18; var fc = 'USDT'; var tc = 'EDU'
                }
                
                //
                const timestamp = list.timestamp; // Given timestamp
                const date = new Date(timestamp * 1000); // Convert to milliseconds
                
                // Format the date
                const options = { month: 'short', day: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true, timeZone: 'UTC' };
                const formattedDate = date.toLocaleDateString('en-US', options).replace(',', '');
                
                // Calculate "time ago"
                const now = new Date();
                const diffSeconds = Math.floor((now.getTime() - date.getTime()) / 1000);
                let timeAgo = '';
                
                if (diffSeconds < 60) {
                    timeAgo = `${diffSeconds} sec ago`;
                } else if (diffSeconds < 3600) {
                    timeAgo = `${Math.floor(diffSeconds / 60)} min ago`;
                } else if (diffSeconds < 86400) {
                    timeAgo = `${Math.floor(diffSeconds / 3600)} hrs ago`;
                } else {
                    timeAgo = `${Math.floor(diffSeconds / 86400)} days ago`;
                }
                
                // Construct the final output
                const dateResult = `${timeAgo} (${date.toUTCString()})`;
                //
        
                txn_list +=  `	<tr class="seprator">
									<td>${ah}</td>
									<td>${list.user}</td>
									<td>${parseFloat(list.amountIn/fd).toFixed(4)} ${fc}</td>
									<td>${parseFloat(list.amountOut/td).toFixed(4)} ${tc}</td>
									<td>${dateResult}</td>
								</tr>`;
            });
        
            $("#txn_list").html(txn_list);
        }
        
        window.onload = async function() {    
            setInterval(async function() {
                if (ah == 0) { 
                    await getAllTxnList();
                }
            }, 1000);
        }

	</script>
@endsection
