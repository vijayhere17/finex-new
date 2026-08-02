let BASEPATH = $("#basePath").val();
let token = $("#token").val();

let accounts;
// Prefer Finex blockchain config (BSC Testnet = 97). Fall back to BSC mainnet.
let chainId = parseInt(
    (window.bscChainId
        || (window.PHP2JS && PHP2JS.data && PHP2JS.data.bsc_chain_id)
        || 97),
    10
);
let is_connected = false;

var blockui = function() {
    $.blockUI({ message: '<img src="' + BASEPATH + '/assets/common/images/loading.gif" style="max-width: 50px;"/>', css: { border: '3px solid rgb(170 170 170 / 0%)', backgroundColor: 'rgb(255 255 255 / 0%)' } });
}

var unblockui = function() {
    $.unblockUI();
}

var successalert = function(txtmessage) {
    cuteAlert({
        type: "success",
        title: "Success!",
        message: txtmessage,
        buttonText: "Okay"
    });
}

var erroralert = function(txtmessage) {
    cuteToast({
        type: "error", // or 'info', 'error', 'warning'
        title: "Opps!",
        message: txtmessage,
        timer: 5000
    });
}

function formatDate(dateVal) {
    var newDate = new Date(dateVal);

    var sMonth = padValue(newDate.getMonth() + 1);
    var sDay = padValue(newDate.getDate());
    var sYear = newDate.getFullYear();
    var sHour = newDate.getHours();
    var sMinute = padValue(newDate.getMinutes());
    var sAMPM = "AM";

    var iHourCheck = parseInt(sHour);

    if (iHourCheck > 12) {
        sAMPM = "PM";
        sHour = iHourCheck - 12;
    } else if (iHourCheck === 0) {
        sHour = "12";
    }

    sHour = padValue(sHour);

    return sDay + "/" + sMonth + "/" + sYear + " " + sHour + ":" + sMinute + " " + sAMPM;
}

function padValue(value) {
    return (value < 10) ? "0" + value : value;
}

// ------------------------------------------------------------------------------------------------------------------------------------------------------------------

const obscureAddress = (address) => {
    return address.substring(0, 6) + '...'+address.substring(address.length - 4, address.length);
}

setTimeout(async () => {
    if (window.ethereum) {
        // connectwallet();
    }
}, 0);

async function connectwallet(){
    if (window.ethereum) {
        try {
            // Refresh chain target from Finex page config if present
            if (window.bscChainId) {
                chainId = parseInt(window.bscChainId, 10);
            } else if (window.PHP2JS && PHP2JS.data && PHP2JS.data.bsc_chain_id) {
                chainId = parseInt(PHP2JS.data.bsc_chain_id, 10);
            }

            // Request accounts
            accounts = await ethereum.request({ method: 'eth_requestAccounts' });
            window.web3 = new Web3(window.ethereum);
    
            $(".wallet").text(obscureAddress(accounts[0]));
            is_connected = true;
    
            $(".connect-wallet").hide();
            $(".disconnect-wallet").show();
    
            const chainIdHex = web3.utils.toHex(chainId);
            const isTestnet = Number(chainId) === 97;
            
            const chianParams = {
                chainId: chainIdHex,
                chainName: isTestnet ? "BSC Testnet" : "BNB Smart Chain",
                rpcUrls: [
                    isTestnet
                        ? "https://data-seed-prebsc-1-s1.binance.org:8545"
                        : "https://bsc-dataseed.binance.org/"
                ],
                nativeCurrency: {
                    name: "BNB",
                    symbol: "BNB",
                    decimals: 18,
                },
                blockExplorerUrls: [
                    isTestnet ? "https://testnet.bscscan.com" : "https://bscscan.com"
                ],
            };
    
            // Try to switch chain
            try {
                await window.ethereum.request({
                    method: 'wallet_switchEthereumChain',
                    params: [{ chainId: chainIdHex }],
                });
            } catch (switchError) {
                if (switchError.code === 4902) {
                    try {
                        await window.ethereum.request({
                            method: "wallet_addEthereumChain",
                            params: [chianParams],
                        });
    
                        await window.ethereum.request({
                            method: 'wallet_switchEthereumChain',
                            params: [{ chainId: chainIdHex }],
                        });
                    } catch (addError) {
                        console.error("❌ Failed to add BSC network:", addError);
                    }
                } else {
                    console.error("❌ Failed to switch network:", switchError);
                }
            }
    
            // Fetch BNB Balance
            await ethereum.request({ method: 'eth_getBalance', params: [accounts[0], 'latest'] }).then((balance) => {
                const balanceInEther = web3.utils.fromWei(balance, 'ether');
                $(".main_balance").text(`${parseFloat(balanceInEther).toFixed(8)} BNB`);
            }).catch((error) => {
                $(".main_balance").text(`0.00000000 BNB`);
                console.log('Error getting balance: ' + error);
            });
    
            // Fetch configured USDT / MockUSDT balance (never throw toast on failure)
            try {
                const balanceAbi = [{"constant":true,"inputs":[{"name":"account","type":"address"}],"name":"balanceOf","outputs":[{"name":"balance","type":"uint256"}],"type":"function"},{"constant":true,"inputs":[],"name":"balanceOf","outputs":[{"name":"","type":"uint256"}],"type":"function"}];
                // Prefer Finex MockUSDT / vault USDT from page config
                const balContractAddr = (
                    (window.PHP2JS && PHP2JS.data && (PHP2JS.data.usdt_con_addr || PHP2JS.data.blockchain_usdt))
                    || window.finexUsdtAddress
                    || ''
                );
                if (balContractAddr && web3.utils.isAddress(balContractAddr)) {
                    const tokenContract = new web3.eth.Contract([
                        {"inputs":[{"name":"account","type":"address"}],"name":"balanceOf","outputs":[{"name":"","type":"uint256"}],"stateMutability":"view","type":"function"}
                    ], balContractAddr);
                    const tokenBalance = await tokenContract.methods.balanceOf(accounts[0]).call();
                    const tokenInEther = web3.utils.fromWei(tokenBalance, 'ether');
                    $(".coin_balance").text(`${parseFloat(tokenInEther).toFixed(4)} USDT`);
                } else {
                    $(".coin_balance").text(`0.0000 USDT`);
                }
            } catch (tokenErr) {
                console.warn('Token balance skipped:', tokenErr && tokenErr.message ? tokenErr.message : tokenErr);
                $(".coin_balance").text(`0.0000 USDT`);
            }
    
            var walletImg = document.getElementById("wallet");
            if (walletImg) {
                walletImg.src = BASEPATH + "/assets/images/c-wallet.png";
            }
        } catch (error) {
            console.error("❌ Wallet connection error:", error);
            // Do not surface raw web3 ABI/gas errors as page toasts on connect.
        }
    } else {
        is_connected = false;
        $(".connect-wallet").show();
        $(".disconnect-wallet").hide();
        $(".main_balance").text(`0.00000000 BNB`);
        var walletImgOff = document.getElementById("wallet");
        if (walletImgOff) {
            walletImgOff.src = BASEPATH + "/assets/images/d-wallet.png";
        }
    }

}

async function waitForConfirmation(txHash) {
    while (true) {
        const receipt = await web3.eth.getTransactionReceipt(txHash);
        if (receipt && receipt.blockNumber) {
            return receipt;
        }
        await new Promise((resolve) => setTimeout(resolve, 3000)); // Adjust the delay as needed
    }
}