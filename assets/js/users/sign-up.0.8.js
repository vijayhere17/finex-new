jQuery(document).ready(function() {
	// page ready
});

function getRegConfig() {
    var data = (typeof PHP2JS !== 'undefined' && PHP2JS.data) ? PHP2JS.data : {};
    var fallback = window.REG_FEE_CONFIG || {};

    return {
        to_address: data.to_address || fallback.to_address || '',
        usdt_con_addr: data.usdt_con_addr || fallback.usdt_con_addr || '',
        usdt_con_abi: data.usdt_con_abi || fallback.usdt_con_abi || '[]',
        registration_fee: parseFloat(data.registration_fee || fallback.registration_fee || 1)
    };
}

jQuery('.btn-submit').bind('click', function(e) {
    e.preventDefault();
    if (validate()) {
        processregister();
    }
});

jQuery('.btn-connect').bind('click', async function(e) {
    e.preventDefault();

    if (window.ethereum) {
        try {2
            accounts = await ethereum.request({ method: 'eth_requestAccounts' });
            window.web3 = new Web3(window.ethereum);

            is_connected = true;

            $(".btn-connect").hide();
            $(".btn-submit").show();

            $("#userwallet").val(accounts[0]);

            const chainIdHex = web3.utils.toHex(chainId);

            const chianParams = {
                chainId: chainIdHex,
                chainName: "BNB Smart Chain",
                rpcUrls: ["https://bsc-dataseed.binance.org/"],
                nativeCurrency: {
                    name: "BNB",
                    symbol: "BNB",
                    decimals: 18,
                },
                blockExplorerUrls: ["https://bscscan.com/"],
            };

            try {
                await window.ethereum.request({
                    method: 'wallet_switchEthereumChain',
                    params: [{ chainId: chainIdHex }],
                });
            } catch (switchError) {
                if (switchError.code === 4902) {
                    try {
                        await window.ethereum.request({
                            method: 'wallet_addEthereumChain',
                            params: [chianParams],
                        });

                        await window.ethereum.request({
                            method: 'wallet_switchEthereumChain',
                            params: [{ chainId: chainIdHex }],
                        });
                    } catch (addError) {
                        console.error("Failed to add BSC network:", addError);
                        return;
                    }
                } else {
                    console.error("Failed to switch network:", switchError);
                    return;
                }
            }
        } catch (err) {
            console.error("Wallet connection failed:", err);
        }
    } else {
        is_connected = false;
        $(".btn-connect").show();
        $(".btn-submit").hide();
        $("#userwallet").val('');
    }
});

function validate() {
    var sponsor_id = $("#sponsor_id").val();

    if (!is_connected) {
        erroralert('Please connect dapp wallet');
        return false;
    }

    if (sponsor_id == '') {
        erroralert('Please enter a sponsor id.');
        return false;
    }

    if ($("#userwallet").val() == '') {
        erroralert('Please connect wallet!');
        return false;
    }

    if (!jQuery("#terms").is(":checked")) {
        erroralert('Please accept terms of services.');
        return false;
    }

    

    return true;
}

async function processregister() {
    blockui();

    // Free Registration
    submitSignupAccount('');

    return;
}

function submitSignupAccount(registration_hash) {
    var firstname = $("#firstname").val();
    var lastname = $("#lastname").val();
    var email = $("#email").val();
    var userwallet = $("#userwallet").val();

    var sponsor_id = $("#sponsor_id").val();
    var leg = $("#leg").val();

    var reqObj = {
        _token: token,
        firstname: firstname,
        lastname: lastname,
        email: email,
        userwallet: userwallet,
        sponsor_id: sponsor_id,
        leg: leg,
        registration_hash: registration_hash || '',
        isapi: 0
    };

    $.ajax({
        type: 'POST',
        url: BASEPATH + "/submit-sign-up",
        data: reqObj,
        dataType: 'json',
        success: function(result) {
            if (result.success) {
                resetformdata();
                $("#auth-main").hide();
                successMessage();
            } else {
                erroralert(result.error);
            }
            unblockui();
        },
        error: function() {
            erroralert('Signup request failed. If fee was paid, contact support with your tx hash.');
            unblockui();
        }
    });
}

$("#ok").click(function () {
    $("#auth-main").show();
    successMessage(500);
});

function successMessage()
{
    $(".message").toggleClass("comein");
    $(".check").toggleClass("scaledown");
}

jQuery('#sponsor_id').on('change', function(e) {
    var sponsor_id = $("#sponsor_id").val();

    if(sponsor_id != '') {

        var reqObj = {
            _token: token,
            sponsor_id: sponsor_id
        };

        blockui();

        $.ajax({
            type: 'POST',
            url: BASEPATH + "/check-sponsor-id",
            data: reqObj,
            dataType: 'json',
            success: function(result) {
                if (result.success) {
                    $("#sponsor_name").text(result.name);
                } else {
                    erroralert(result.error);
                }
                unblockui();
            }
        });
    }
});

function resetformdata() {
    $("#firstname").val('');
    $("#lastname").val('');
    $("#email").val('');
    $("#mobile").val('');
    $("#userwallet").val('');
    $("#password").val('');
    $("#sponsor_id").val('');
    $("#sponsor_name").text('');
    $("#leg").val('');
    $('input[name="terms"]').prop('checked', false);
}
