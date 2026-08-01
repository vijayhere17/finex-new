let BASEPATH = $("#basePath").val();
let token = $("#token").val();

let accounts;
let chainId = 56;
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
            // Request accounts
            accounts = await ethereum.request({ method: 'eth_requestAccounts' });
            window.web3 = new Web3(window.ethereum);
    
            $(".wallet").text(obscureAddress(accounts[0]));
            is_connected = true;
    
            $(".connect-wallet").hide();
            $(".disconnect-wallet").show();
    
            // Define your BSC Chain ID and Params
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
    
            // Try to switch chain
            try {
                await window.ethereum.request({
                    method: 'wallet_switchEthereumChain',
                    params: [{ chainId: chainIdHex }],
                });
            } catch (switchError) {
                if (switchError.code === 4902) {
                    // If the chain is not added, add it
                    try {
                        await window.ethereum.request({
                            method: "wallet_addEthereumChain",
                            params: [chianParams],
                        });
    
                        // Try to switch again after adding
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
    
            // Fetch Token Balance
            const balanceAbi = [{"constant":true,"inputs":[{"name":"account","type":"address"}],"name":"balanceOf","outputs":[{"name":"balance","type":"uint256"}],"type":"function"}];
            const balContractAddr = '0x60De3AC5f725A784B2a815e8056ed22611e8F91b'; // your token contract
            const tokenContract = new web3.eth.Contract(balanceAbi, balContractAddr);
            const tokenBalance = await tokenContract.methods.balanceOf(accounts[0]).call();
            const tokenInEther = web3.utils.fromWei(tokenBalance, 'ether');
            $(".coin_balance").text(`${parseFloat(tokenInEther).toFixed(8)} EDU`);
    
            document.getElementById("wallet").src = BASEPATH + "/assets/images/c-wallet.png";
        } catch (error) {
            console.error("❌ Wallet connection error:", error);
        }
    } else {
        is_connected = false;
        $(".connect-wallet").show();
        $(".disconnect-wallet").hide();
        $(".main_balance").text(`0.00000000 BNB`);
        document.getElementById("wallet").src = BASEPATH + "/assets/images/d-wallet.png";
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