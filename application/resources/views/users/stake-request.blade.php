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
                            <li class="breadcrumb-item"><a href="javascript:">Topup Now</a></li>
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
                                        <th>Request</th>
                                        <th>Amount ($)</th>
                                        <th>Txn. Hash</th>
                                        <th>Status</th>
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
<script src="{{ URL::to('/') }}/assets/js/users/stake-request.0.6.js"></script>
<script>
    function initializeCountdowns(id, date) 
    {
        const listItem = document.getElementById('locker_timer_'+id);
        
        // Get the target date
        const countDownDate = new Date(date).getTime();
        
        // Update the countdown every 1 second
        const interval = setInterval(() => {
             
            // Get current time
            const now = new Date().getTime();
        
            // Calculate the distance
            const distance = countDownDate - now;
        
            // Time calculations
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
            // Display the countdown
            if (distance >= 0) {
                listItem.innerHTML = `${days}d ${hours}h ${minutes}m ${seconds}s`;
            } else {
                // If the countdown has finished
                clearInterval(interval);
                listItem.innerHTML = "Mature";
            }
        }, 1000);
    }
    
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
