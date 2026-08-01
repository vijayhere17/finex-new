let rid = 0;

let coin_rate = PHP2JS.data.coin_rate;
let contract_addr = PHP2JS.data.usdt_con_addr;
let contract_abi = JSON.parse(PHP2JS.data.usdt_con_abi);

let payable_coin = 0;

const deposit_addr = PHP2JS.data.to_address;

// FinexVault (BSC) — funds are held by the smart contract, not an EOA deposit wallet.
const blockchainEnabled = !!(PHP2JS.data.blockchain_enabled);
const finexVaultAddress = PHP2JS.data.finex_vault_address || '';
let finexVaultAbi = [];
try {
    finexVaultAbi = typeof PHP2JS.data.finex_vault_abi === 'string'
        ? JSON.parse(PHP2JS.data.finex_vault_abi || '[]')
        : (PHP2JS.data.finex_vault_abi || []);
} catch (e) {
    finexVaultAbi = [];
}
const bscChainId = parseInt(PHP2JS.data.bsc_chain_id || 97, 10);

jQuery(document).ready(function() {
    // Card "Activate" selects that fixed slot and starts the existing USDT payment flow.
    jQuery('.btn-activate-slot').on('click', function(e) {
        e.preventDefault();

        var stakeId = $(this).data('stakeid');
        var amount = $(this).data('amount');
        var slotNumber = $(this).data('slot');

        if (!stakeId) {
            erroralert('This slot package is not configured yet. Please contact support.');
            return false;
        }

        selectSlot(stakeId, amount, slotNumber);
        processstake();
    });
});

// Select a fixed slot package (no manual amount entry).
function selectSlot(stakeId, amount, slotNumber)
{
    $("#topup_amount").val(amount);

    $("input[name=package]").prop('checked', false);
    $("input[name=package][stakeid='" + stakeId + "']").prop('checked', true);

    if (!$("input[name=paymentmode]").is(':checked')) {
        $("#payment_alc").prop('checked', true);
    }

    $("#txt_amount").text('$' + Number(amount).toLocaleString());
    if (slotNumber) {
        $("#txt_next_slot").text('Slot ' + slotNumber);
    }

    getcalculation();
}

function getcalculation()
{
    var amount = parseFloat($("#topup_amount").val());

    if (isNaN(amount)) {
        amount = 0;
    }

    var cap = parseFloat($("input[name=package]:checked").attr('cap'));
    if (isNaN(cap)) {
        cap = 0;
    }

    $("#txt_cap").text((cap > 0 ? cap.toFixed(2) : '0') + "x");

    var apy = parseFloat(window.directRoiPercent);
    if (isNaN(apy)) {
        apy = parseFloat(window.slotMeta && window.slotMeta.direct_roi_percent);
    }
    if (isNaN(apy)) {
        apy = 0;
    }

    var payable = (amount / coin_rate).toFixed(8);
    payable_coin = payable;

    $("#txt_apy").text(apy.toFixed(0) + "%");
    $("#txt_payable").text(payable);

    if (amount > 0) {
        $("#txt_amount").text('$' + Number(amount).toLocaleString());
    }

    $("#amount_error").hide().text('');
}

jQuery('.btn-submit').bind('click', function(e) {
    e.preventDefault();
    processstake();
});

async function processstake()
{
    if(!$("input[name='package']").is(':checked'))
    {
        erroralert('Please activate your next eligible slot');
        return false;
    }

    var selectedState = $("input[name='package']:checked").attr('data-state');
    if (selectedState && selectedState !== 'ready')
    {
        erroralert('Only the next eligible slot can be activated');
        return false;
    }

    if(!$("input[name='paymentmode']").is(':checked'))
    {
        erroralert('Please select payment option');
        return false;
    }

    const decimal = $("input[name='paymentmode']:checked").attr('decimal');
    const payment = $("input[name='paymentmode']:checked").attr('value');

    const amount = $("#topup_amount").val();
    const slotAmount = $("input[name='package']:checked").attr('stakeamount');

    if(amount == '' || amount <= 0)
    {
        erroralert('Please select a slot to activate');
        return false;
    }

    // Amount must exactly match the selected fixed slot — no custom values.
    if(parseFloat(amount) !== parseFloat(slotAmount))
    {
        erroralert('Investment amount must match the selected slot');
        return false;
    }

    // Refresh payable from the fixed slot amount before sending chain tx.
    getcalculation();

    blockui();

    // Vault path: approve USDT → FinexVault.invest(slot, sponsor, offchainId)
    if (blockchainEnabled && finexVaultAddress && finexVaultAbi.length) {
        await investViaFinexVault(payment, decimal, amount);
        return;
    }

    // TEMP fallback (no vault configured): pending admin approval without chain tx.
    submitHashRequest(0, payment, 0, 'TEST-PENDING-' + Date.now());
}

async function investViaFinexVault(payment, decimal, amount)
{
    try {
        await connectwallet();

        const slotNumber = parseInt(
            $("input[name=package]:checked").attr('slotnumber')
            || $("input[name=package]:checked").attr('data-slot')
            || (window.slotMeta && window.slotMeta.next_slot)
            || '1',
            10
        );

        if(decimal == 18)
        {
            var amountwei = web3.utils.toWei(payable_coin.toString(), 'ether');
        }
        else if(decimal == 6)
        {
            var amountwei = web3.utils.toWei(amount.toString(), 'mwei');
        }
        else
        {
            var amountwei = web3.utils.toWei(payable_coin.toString(), 'ether');
        }

        const usdt = new web3.eth.Contract(contract_abi, contract_addr);
        const vault = new web3.eth.Contract(finexVaultAbi, finexVaultAddress);

        let balance = await usdt.methods.balanceOf(accounts[0]).call();
        if (BigInt(balance) < BigInt(amountwei)) {
            erroralert("Insufficient USDT balance to activate this slot.");
            unblockui();
            return;
        }

        // Ensure allowance for vault
        let allowance = await usdt.methods.allowance(accounts[0], finexVaultAddress).call();
        if (BigInt(allowance) < BigInt(amountwei)) {
            const approveTx = usdt.methods.approve(finexVaultAddress, amountwei);
            let gasprice = await web3.eth.getGasPrice();
            gasprice = Math.round(gasprice * 1.2);
            let gas_estimate = await approveTx.estimateGas({ from: accounts[0] });
            gas_estimate = Math.round(gas_estimate * 1.2);
            await approveTx.send({
                from: accounts[0],
                gas: web3.utils.toHex(gas_estimate),
                gasPrice: web3.utils.toHex(gasprice),
            });
        }

        // Sponsor wallet from referral (Laravel user.username of sponsor) if provided
        let sponsor = (window.sponsorWalletAddress || PHP2JS.data.sponsor_wallet || '0x0000000000000000000000000000000000000000');
        if (!sponsor || sponsor === '') {
            sponsor = '0x0000000000000000000000000000000000000000';
        }

        const offchainId = rid || 0;
        const investTx = vault.methods.invest(slotNumber, sponsor, offchainId);

        let gasprice2 = await web3.eth.getGasPrice();
        gasprice2 = Math.round(gasprice2 * 1.2);
        let gas2 = await investTx.estimateGas({ from: accounts[0] });
        gas2 = Math.round(gas2 * 1.2);

        await investTx.send({
            from: accounts[0],
            gas: web3.utils.toHex(gas2),
            gasPrice: web3.utils.toHex(gasprice2),
        }).on('transactionHash', (hash) => {
            submitHashRequest(rid, payment, 1, hash);
        }).on('receipt', (receipt) => {
            if (receipt.status) {
                submitHashRequest(rid, payment, 2, receipt.transactionHash);
            }
        }).on('error', (error) => {
            erroralert(error.message || "Vault investment failed.");
            unblockui();
        });
    } catch (err) {
        console.log(err);
        erroralert(err.message || "An unexpected error occurred.");
        unblockui();
    }
}

async function submitHashRequest(id, payment, status, hash)
{
    const stake_id = $("input[name=package]:checked").attr('stakeid');
    const amount = $("#topup_amount").val();

    var reqObj = {
        _token: token,
        id : id,
        stake_id : stake_id,
        payment : payment,
        amount : amount,
        status : status,
        hash : hash
    };

    $.ajax({
        type: 'POST',
        url: BASEPATH + "/process-submit-buy-bot",
        data: reqObj,
        dataType: 'json',
        success: function(result) {
            if (result.success) {
                rid = result.id;
                // status 0 = Pending (test mode) | status 2 = Success after on-chain pay
                if(status == 0 || status == 2)
                {
                    unblockui();
                    successalert(result.message || 'Topup request submitted. Waiting for admin approval.');
                    window.location.href = BASEPATH+'/bot-request';
                }
            } else {
                erroralert(result.error);
                unblockui();
            }
        }
    });
}
