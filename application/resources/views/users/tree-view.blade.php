@php use Illuminate\Support\Facades\Auth; @endphp

@extends('users.master')
@section('extra')
<style type="text/css">
    .downborder{
        border-top: 1px solid;
        width: 50%;
        border-left: 1px solid;
        border-right: 1px solid;
        height: 20px;
        margin-bottom: 0rem;
    }

    .ubox{
        border: 1px solid;
        width: 150px;
        margin-bottom: -5px;
        border-radius: 5px;
        margin-top: 5px;
    }

    .dtree{
        margin-bottom: -4px;
    }

    .up{
        font-size: 12px;
        margin-top: 5px;
        margin-bottom: 7px;
        white-space: nowrap;
        width: 85px;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
@endsection
@section('content')

<input id="hdnCurrentID" type="hidden" value="{{ Auth::user()->id }}" />
<input id="hdnParentID" type="hidden" value="" />
<input id="searchID" type="hidden" value="" />
<input id="hdnLoginID" type="hidden" value="{{ Auth::user()->id }}" />

<div class="pc-container">
    <div class="pc-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ URL::to('/') }}/dashboard">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="javascript:">Referrals & Downline</a></li>
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
                        <h5>Tree View</h5>
                    </div>
                    <div class="card-body table-border-style">
                        <div class="row">
                            <div class="col-sm-3">
                                <x-input name="username" type="text" id="username" placeholder="Search Username" value="" />
                            </div>
                            <div class="col-sm-3">
                                <label class="form-label" for="first-name" id="user_name"></label>
                            </div>
                            <div class="col-sm-6" align="right">
                                <button id="btnLevelUp" class="btn btn-info" style="display: none;">Level Up</button>&nbsp;&nbsp;
                                <button id="btnGoBack" class="btn btn-primary" style="display: none;">Go To Home</button>
                            </div>

                            <div class="col-sm-12">
                                <div class="table-responsive">
                                    <table width="100%" border="0">
                                        <tr>
                                            <td align="center" colspan="16">
                                                <div class="dtree" id="p0"></div>
                                                <div class="dtree">|</div>
                                                <div align="center">
                                                    <p class="downborder"></p>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td align="center" colspan="8">
                                                <div class="dtree" id="p1"></div>
                                                <div class="dtree">|</div>
                                                <div align="center">
                                                    <p class="downborder"></p>
                                                </div>
                                            </td>
                                            <td align="center" colspan="8">
                                                <div class="dtree" id="p2"></div>
                                                <div class="dtree">|</div>
                                                <div align="center">
                                                    <p class="downborder"></p>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td align="center" colspan="4">
                                                <div class="dtree" id="p3"></div>
                                                <div class="dtree">|</div>
                                                <div align="center">
                                                    <p class="downborder"></p>
                                                </div>
                                            </td>
                                            <td align="center" colspan="4">
                                                <div class="dtree" id="p4"></div>
                                                <div class="dtree">|</div>
                                                <div align="center">
                                                    <p class="downborder"></p>
                                                </div>
                                            </td>
                                            <td align="center" colspan="4">
                                                <div class="dtree" id="p5"></div>
                                                <div class="dtree">|</div>
                                                <div align="center">
                                                    <p class="downborder"></p>
                                                </div>
                                            </td>
                                            <td align="center" colspan="4">
                                                <div class="dtree" id="p6"></div>
                                                <div class="dtree">|</div>
                                                <div align="center">
                                                    <p class="downborder"></p>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td align="center" colspan="2">
                                                <div class="dtree" id="p7"></div>
                                            </td>
                                            <td align="center" colspan="2">
                                                <div class="dtree" id="p8"></div>
                                            </td>
                                            <td align="center" colspan="2">
                                                <div class="dtree" id="p9"></div>
                                            </td>
                                            <td align="center" colspan="2">
                                                <div class="dtree" id="p10"></div>
                                            </td>
                                            <td align="center" colspan="2">
                                                <div class="dtree" id="p11"></div>
                                            </td>
                                            <td align="center" colspan="2">
                                                <div class="dtree" id="p12"></div>
                                            </td>
                                            <td align="center" colspan="2">
                                                <div class="dtree" id="p13"></div>
                                            </td>
                                            <td align="center" colspan="2">
                                                <div class="dtree" id="p14"></div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>        
                    </div>
                </div>
            </div> 
        </div>
    </div>
</div>
@endsection
@section('jscontent')
<script src="{{ URL::to('/') }}/assets/js/users/tree-view.0.3.js"></script>

<div class="modal fade" id="viewUserModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table">
                        <tbody id="userdetails"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
  
@endsection
