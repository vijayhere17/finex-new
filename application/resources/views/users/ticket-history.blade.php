@extends('users.master')
@section('extra')
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

    @keyframes bounce {
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
</style>
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
                            <li class="breadcrumb-item"><a href="javascript:">24/7 Support</a></li>
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
                        <h5>{{ $page_titel }}</h5>
                    </div>
                    <div class="card-body table-border-style">
                        <div class="table-responsive">
                            <table class="table" id="tblTicketHistory">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Ticket No</th>
                                        <th>Type</th>
                                        <th>Titel</th>
                                        <th>Status</th>
                                        <th>Created On</th>
                                        <th>Action</th>
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
<input type="hidden" id="memberid" value="{{ $member_id }}">
<input type="hidden" id="status" value="{{ $status }}">

<div class="modal fade" id="message-chat" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Ticket : </h5>
                <button type="button" class="close" onclick="closeopenmegbox();">&times;</button>
            </div>
    
            <div class="modal-body" style="padding: 0px 0px;">
                <div class="chat_box">
                    <input type="hidden" id="ticket_id" value="0">
                    <input type="hidden" id="lastmsgid" value="0">
                    <div class="body" id="chatmsg" style="height: 300px; overflow-y: scroll;"></div>
                </div>
            </div>
    
            <div class="modal-footer" style="display: unset;">
                <div class="row">
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="txt_message" placeholder="Type message...">
                    </div>
                    <div class="col-sm-2">
                        <button type="button" class="btn btn-success btn-send-msg">Send</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ URL::to('/') }}/assets/js/users/ticket-history.js"></script>
@endsection
