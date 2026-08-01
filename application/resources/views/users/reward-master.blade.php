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
            @foreach($allrewards as $rank)
                <div class="col-md-6 col-lg-3">
                    <div class="card price-card price-popular">
                        <div class="card-body">
                            <div class="price-head">
                                <span class="badge f-12 bg-warning mb-3">Pending</span>
                                <h5 class="mb-0">{{ $rank->reward }}</h5>
                                <p class="text-muted">&nbsp;</p>
                                <div class="price-price mt-4">
                                    <img src="{{ URL::to('/') }}/assets/{{ $rank->img }}" style="max-width: 100%; border-radius: 10px;" />
                                </div>
                                <div class="d-grid">
                                    <a class="btn btn-primary mt-4" href="#">{{ $rank->main_leg }}% / {{ $rank->other_leg/2 }}% - {{ $rank->other_leg/2 }}% <br>Business Leg</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
@section('jscontent')
@endsection
