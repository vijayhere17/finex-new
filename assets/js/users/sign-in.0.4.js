jQuery(document).ready(function() {
	// call function whene page load
    // document.getElementById('userwallet').readOnly = true;
});


// jQuery('.btn-connect').bind('click', async function(e) {
//     e.preventDefault();
    
//     if (window.ethereum) {
//         accounts = await ethereum.request({ method: 'eth_requestAccounts' });
//         window.web3 = new Web3(window.ethereum);
        
//         is_connected = true;
        
//         $(".btn-connect").hide();
//         $(".btn-submit").show();

//         await window.ethereum.request({
//             method: 'wallet_switchEthereumChain',
//             params: [{ chainId: web3.utils.toHex(chainId) }]
//         });

//         $("#userwallet").val(accounts[0]);
//     } else {
//         is_connected = false;
//         $(".btn-connect").show();
//         $(".btn-submit").hide();
//         $("#userwallet").val('');
//     }  
// });

jQuery('.btn-connect').bind('click', async function(e) {
    e.preventDefault();

    if (window.ethereum) {
        try {
            accounts = await ethereum.request({ method: 'eth_requestAccounts' });
            window.web3 = new Web3(window.ethereum);

            is_connected = true;

            $(".btn-connect").hide();
            $(".btn-submit").show();
            
            // Fill wallet input
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

            // Try to switch to ALTB
            try {
                await window.ethereum.request({
                    method: 'wallet_switchEthereumChain',
                    params: [{ chainId: chainIdHex }],
                });
            } catch (switchError) {
                if (switchError.code === 4902) {
                    // Chain not found — try adding it
                    try {
                        await window.ethereum.request({
                            method: 'wallet_addEthereumChain',
                            params: [chianParams],
                        });

                        // Try switching again after adding
                        await window.ethereum.request({
                            method: 'wallet_switchEthereumChain',
                            params: [{ chainId: chainIdHex }],
                        });
                    } catch (addError) {
                        console.error("❌ Failed to add BSC network:", addError);
                        return;
                    }
                } else {
                    console.error("❌ Failed to switch network:", switchError);
                    return;
                }
            }
        } catch (err) {
            console.error("❌ Wallet connection failed:", err);
        }
    } else {
        is_connected = false;
        $(".btn-connect").show();
        $(".btn-submit").hide();
        $("#userwallet").val('');
    }
});

jQuery('.btn-submit').bind('click', function(e) {
    e.preventDefault();
    if (validate()) {
        processlogin();
    }
});

function validate() {
    if (!is_connected) {
        erroralert('Please connect dapp wallet');
        return false;
    }
    
    if ($("#userwallet").val() == '') {
        erroralert('Please connect wallet!');
        return false;
    }

    return true;
}

function processlogin() {
    var wallet = $("#userwallet").val();
  
    var reqObj = {
        _token: token,
        wallet : wallet
    };

    blockui();

    $.ajax({
        type: 'POST',
        url: BASEPATH + "/submit-sign-in",
        data: reqObj,
        dataType: 'json',
        success: function(result) {
            if (result.success) {
                window.location.href = BASEPATH+'/dashboard';
                resetformdata();
            } else {
                erroralert(result.error);
            }
            unblockui();
        }
    });
}

function resetformdata() {
    $("#username").val('');
    $("#password").val('');
}