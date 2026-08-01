@extends('users.master')
@section('extra')
@endsection
@section('content')
<div class="pc-container">
    <div class="pc-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ URL::to('/') }}/dashboard">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="javascript:">Topup Activation</a></li>
                            <li class="breadcrumb-item" aria-current="page">{{ $page_titel }}</li>
                        </ul>
                    </div>
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h2 class="mb-0">{{ $page_titel }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->

        <div class="row">
            <!-- [ form-element ] start -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Topup Report</h5>
                    </div>
                    <div class="card-body table-border-style">
                        <div class="table-responsive">
                            <table class="table" id="tableList">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Topup Date</th>
                                        <th>Topup Amount</th>
                                        <th>Remain. Days</th>
                                        <th>Status</th>
                                        <th>Security withdrawal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div> 
        </div>
    </div>
</div>
@endsection
@section('jscontent')
<script src="{{ URL::to('/') }}/assets/js/users/topup-report.0.4.js"></script>

<div class="modal fade" id="withdrawModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Security withdrawal</h4>
                <a href="javascript:closeWithdrawalModal()" class="close" data-dismiss="modal">&times;</a>
            </div>
            <div class="modal-body">
                <div class="row">
                    <form>
                        <div class="row g-4">
                            <div class="col-md-12">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="topup_amount" placeholder="Topup amount" value="0" readonly="">
                                    <label for="floatingInput">Topup amount</label>
                                </div> 
                            </div>
                            <div class="col-md-12">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="total_roi" placeholder="Total receive return" value="0" readonly="">
                                    <label for="floatingInput">Total receive return</label>
                                </div> 
                            </div>
                            <div class="col-md-12">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="withdrawal_charge" placeholder="Security withdrawal charge" value="0" readonly="">
                                    <label for="floatingInput">Security withdrawal charge</label>
                                </div> 
                            </div>
                            
                            <div class="col-md-12">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="capital_withdrawal" placeholder="Capital withdrawal" value="0" readonly="">
                                    <label for="floatingInput">Capital withdrawal</label>
                                </div> 
                                <label for="floatingInput">Note : You will withdrawal to stop your daily return.</label>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" onclick="processWithdrawal()" style="width: 100%;">Withdrawal</button>
            </div>
        </div>
    </div>
</div>

<script>
    function calculateMonthsBetween(startDate, endDate)
    {
        const start = new Date(startDate);
        const end = new Date(endDate);
    
        // Calculate the total months difference
        const totalMonths = (end.getFullYear() - start.getFullYear()) * 12 + (end.getMonth() - start.getMonth());
    
        return totalMonths;
    }
</script>
@endsection
