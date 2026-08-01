@extends('admin.master')
@section('title', '')
@section('extra')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.12/datatables.min.css"/>
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/responsive/1.0.2/css/dataTables.responsive.css"/>
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css"/>
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css"/>
<style>
	.dataTables_length{
		position: absolute;
		margin-top: 20px;
		margin-bottom: 20px;
	}

	.dt-buttons{
		margin-bottom: 50px;
	}

	@media screen and (max-width: 640px){
		.dataTables_wrapper .dataTables_length, .dataTables_wrapper .dataTables_filter {
			float: none;
			text-align: center;
			position: relative;
			margin-top: auto;
			margin-bottom: 20px;
		}

		.dt-buttons{
			margin-bottom: 20px;
		}
	}
</style>

<style>
    /* chat_box */
    .chat_box {
      display: flex;
      flex-direction: column;
      height: 100%;
    }
    .chat_box > * {
      padding: 16px;
    }

    /* head */
    .head {
      display: flex;
      align-items: center;
    }
    .head .user {
      display: flex;
      align-items: center;
      flex-grow: 1;
    }
    .head .user .avatar {
      margin-right: 8px;
    }
    .head .user .avatar img {
      display: block;
      border-radius: 50%;
    }
    .head .bar_tool {
      display: flex;
    }
    .head .bar_tool i {
      padding: 5px;
      width: 30px;
      height: 30px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    /* body */
    .body .bubble {
      display: inline-block;
      padding: 10px;
      margin-bottom: 5px;
      border-radius: 15px;
    }
    .body .bubble p {
      color: #f9fbff;
      font-size: 14px;
      text-align: left;
      line-height: 1.4;
    }
    .body .incoming {
      text-align: left;
    }
    .body .incoming .bubble {
      background-color: #b2b2b2;
    }
    .body .outgoing {
      text-align: right;
    }
    .body .outgoing .bubble {
      background-color: #79c7c5;
    }

    /* foot */
    .foot {
      display: flex;
    }
    .foot .msg {
      flex-grow: 1;
    }

    @keyframes  bounce {
      50% {
        transform: translate(0, 5px);
      }
      100% {
        transform: translate(0, 0);
      }
    }
    .ellipsis {
      display: inline-block;
      width: 5px;
      height: 5px;
      border-radius: 50%;
      background-color: #b7b7b7;
    }
    .dot_1 {
      /* animation: name duration timing-function delay iteration-count */
      animation: bounce 0.8s linear 0.1s infinite;
    }
    .dot_2 {
      animation: bounce 0.8s linear 0.2s infinite;
    }
    .dot_3 {
      animation: bounce 0.8s linear 0.3s infinite;
    }

    /* width */
    ::-webkit-scrollbar {
      width: 5px;
    }

    /* Track */
    ::-webkit-scrollbar-track {
      background: #f1f1f1;
    }

    /* Handle */
    ::-webkit-scrollbar-thumb {
      background: #888;
    }

    /* Handle on hover */
    ::-webkit-scrollbar-thumb:hover {
      background: #555;
    }

	::-webkit-scrollbar {
		display: none;
	}
</style>
@endsection
@section('content')
<ol class="breadcrumb bc-3">
	<li>
		<a href="{{URL::to('/')}}/admin/home"><i class="entypo-home"></i>Home</a>
	</li>
	<li class="active">
		<strong>Swap Txn. Logs</strong>
	</li>
</ol>
<div class="row">
</div>
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-primary" data-collapsed="0">
			<div class="panel-heading">
				<div class="panel-title">
					Swap Txn. Logs
				</div>
			</div>
		</div>
		<table id="tblTickets" border="0" width="100%" align="center" class="table table-bordered datatable">
			<thead>
				<tr class="seprator">
					<th>No</th>
					<th>Swap Date</th>
					<th>Wallet Addr.</th>
					<th>Receiver Addr.</th>
					<th>From Coin</th>
					<th>To Coin</th>
					<th>Swap Amount</th>
					<th>Rate</th>
					<th>Charge</th>
					<th>Receive Amount</th>
					<th>Sender Txn. Hash</th>
					<th>Status</th>
					<th>Receiver Txn. Hash</th>
					<th>Last Date</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
			<tfoot>
			</tfoot>
		</table>
	</div>
</div>
<br />
@endsection
@section('jscontent')
	<input type="hidden" id="memberid" value="0">

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
	<script src="{{ URL::asset('assets/admin/js/custom/swap-txn.logs.js') }}"></script>
@endsection