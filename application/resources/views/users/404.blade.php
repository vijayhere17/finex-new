@extends('users.master')
@section('extra')
@endsection
@section('content')
<div class="pc-container">
    <div class="pc-content">
        <div class="row">
            <!-- [ form-element ] start -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Opps!</h5>
                    </div>
                    <div class="card-body table-border-style">
                        <div class="table-responsive">
                            <center>
                                <img src="{{ URL::to('/') }}/assets/images/404.png" style="max-width: 50%;"/>
                                <br><br><br>
                                <h2>Opps! Page not found...</h2>
                            </center>
                        </div>
                    </div>
                </div>
            </div> 
        </div>
    </div>
</div>
@endsection
@section('jscontent')
@endsection
