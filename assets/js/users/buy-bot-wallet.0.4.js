let coin_rate = PHP2JS.data.coin_rate;
let payable_coin = 0;

jQuery(document).ready(function() {
	$(".ew_balance").text(PHP2JS.data.balance);

    // Amounts are $50 multiples, so only whole digits are allowed in the field.
    $('#topup_amount').on('input keyup', function() {
        var clean = this.value.replace(/[^0-9]/g, '');
        if (this.value !== clean) { this.value = clean; }
        getcalculation();
    });
});

// Finds the ROI tier package covering the amount (max_amount 0 = open-ended top tier).
function gettierpackage(amount)
{
    var tiers = PHP2JS.data.packages || [];
    var match = null;

    for (var i = 0; i < tiers.length; i++)
    {
        var min = parseFloat(tiers[i].amount);
        var max = parseFloat(tiers[i].max_amount);

        if (amount >= min && (max <= 0 || isNaN(max) || amount <= max))
        {
            match = tiers[i];
        }
    }

    return match;
}

function getcalculation()
{
    var amount = parseFloat($("#topup_amount").val());

    if (isNaN(amount)) { amount = 0; }

    var tier = gettierpackage(amount);
    var apy = (tier != null) ? parseFloat(tier.percantage) : 0;

    var payable = parseFloat(amount / coin_rate).toFixed(8);
        payable_coin = payable;

    $("#txt_daily").text(apy + '% Daily');
    $("#txt_daily_income").text(((amount * apy) / 100).toFixed(2));

    $("#txt_amount").text(amount.toFixed(4));
    $("#txt_payable").text(payable);

    validateamount();
}

function validateamount()
{
    var value = $("#topup_amount").val();
    var amount = parseFloat(value);
    var msg = '';

    if (value != '')
    {
        if (isNaN(amount) || amount < 50)
        {
            msg = 'Minimum topup amount is $50.';
        }
        else if (amount % 50 != 0)
        {
            msg = 'Amount must be a multiple of $50 (e.g. 50, 100, 150 ...).';
        }
    }

    if (msg == '')
    {
        $("#amount_error").hide().text('');
        return true;
    }

    $("#amount_error").text(msg).show();
    return false;
}

jQuery('.btn-submit').bind('click', function(e) {
    e.preventDefault();
    processstake();
});

async function processstake()
{
    const amount = $("#topup_amount").val();
    const username = $("#username").val();

    if(amount == '')
    {
        erroralert('Please enter a topup amount');
        return false;
    }

    if(amount <= 0)
    {
        erroralert('Please enter a valid topup amount');
        return false;
    }

    if(amount < 50)
    {
        erroralert('Minimum topup amount is $50');
        return false;
    }

    if(amount % 50 != 0)
    {
        erroralert('Please enter topup amount $50 multiple');
        return false;
    }

    if(username == '')
    {
        erroralert('Please enter a topup user wallet');
        return false;
    }

    blockui();

    // ---------------------------------------------------------------------------

    try {
        const tier = gettierpackage(parseFloat(amount));
        const stake_id = (tier != null) ? tier.id : 1;

        var reqObj = {
            _token: token,
            stake_id : stake_id,
            payment : 0,
            amount : amount,
            username : username,
        };

        $.ajax({
            type: 'POST',
            url: BASEPATH + "/process-submit-buy-bot-wallet",
            data: reqObj,
            dataType: 'json',
            success: function(result) {
                if (result.success) {
                    unblockui();
                    successalert(result.message)
                    window.location.href = BASEPATH+'/topup-history';
                } else {
                    erroralert(result.error);
                    unblockui();
                }
            }
        });
    } catch(err) {
        console.log(err)
        erroralert(err.message || "An unexpected error occurred.");
        unblockui();
    }
}
