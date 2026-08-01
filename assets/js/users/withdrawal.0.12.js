jQuery(document).ready(function() {
    $(".balance").text(PHP2JS.data.balance);
    
    document.getElementById('with_wallet').readOnly = true;
    
    document.getElementById('admin_charge').readOnly = true;
    document.getElementById('net_amount').readOnly = true;
    document.getElementById('coin_rate').readOnly = true;
    document.getElementById('usd_amount').readOnly = true;
    
    $("#with_wallet").val(PHP2JS.data.wallet_addr);
    $("#coin_rate").val(PHP2JS.data.coin_rate);
    
    $("#admin_charge").val('0.0000');
    $("#net_amount").val('0.0000');
    $("#usd_amount").val('0.0000');
    
    $("#amount").on("keyup change", function(e) {
        const rate = PHP2JS.data.coin_rate;
        let amount = $("#amount").val();
        let charge = (amount*admin_charge)/100;
        let net = amount-charge;
        let usd_amount = parseFloat(net/rate).toFixed(8);
        
        $("#admin_charge").val(parseFloat(charge).toFixed(4));
        $("#net_amount").val(parseFloat(net).toFixed(4));
        $("#usd_amount").val(usd_amount);
    })
});

jQuery('.btn-otp-submit').bind('click', function(e) {
    e.preventDefault();
    if (validate(false)) {
        processWithdrawal(false);
    }
});

jQuery('.btn-submit').bind('click', function(e) {
    e.preventDefault();
    if (validate(true)) {
        processWithdrawal(true);
    }
});

function validate(otpstatus) {
    var balance = $(".balance").text();
    var amount = $("#amount").val();
    var wallet = $("#with_wallet").val();
    var otp = $("#otp").val();
    
    if (parseFloat(balance) <= 0) {
        erroralert('Account balance is $0');
        return false;
    }
    
    if (parseFloat(amount) == '') {
        erroralert('Please enter a amount.');
        return false;
    }

    if (parseFloat(amount) <= 0) {
        erroralert('Please enter a valid amount.');
        return false;
    }

    if (parseFloat(amount) > parseFloat(balance)) {
        erroralert('Insufficient account balance.');
        return false;
    }
    
    if (wallet == '') {
        erroralert('Please update a withdrawal wallet address.');
        return false;
    }
    
    if(otpstatus){
        /* if (otp == '') {
            erroralert('Please enter a one - time password.');
            return false;
        } */
    }

    return true;
}

function processWithdrawal(status) {
    var n_status = true;
    
    var amount = $("#amount").val();
    var wallet = $("#with_wallet").val();
    var otp = '346789'; //$("#otp").val();
    
    var reqObj = {
        _token: token,
        amount : amount,
        wallet : wallet,
        otp : otp,
        status : n_status
    };

    blockui();

    $.ajax({
        type: 'POST',
        url: BASEPATH + "/process-withdrawal-request",
        data: reqObj,
        dataType: 'json',
        success: function(result) {
            if (result.success) {
                if(status){
                    successalert('Withdrawal request submited successfully!');
                    resetformdata(result.balance);
                }else{
                    successalert('OTP send your register email id!');
                    $(".btn-otp-submit").hide();
                    $(".btn-submit").show();
                }
            } else {
                erroralert(result.error);
            }
            unblockui();
        }
    });
}

function resetformdata(balance){
    $("#amount").val('');
    $("#with_wallet").val('');
    $("#otp").val('');
    $(".balance").text(balance);
}