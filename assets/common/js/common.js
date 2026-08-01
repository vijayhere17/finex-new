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
    return address.substring(0, 15) + '...'+address.substring(address.length - 8, address.length);
}

setTimeout(async () => {
    if (window.ethereum) {
        connectwallet();
    }
}, 0);

async function connectwallet(){
    if (window.ethereum) {
        accounts = await ethereum.request({ method: 'eth_requestAccounts' });
        window.web3 = new Web3(window.ethereum);
        
        $(".wallet").text(obscureAddress(accounts[0]));
        is_connected = true;
        
        $(".connect-wallet").hide();
        $(".disconnect-wallet").show();

        await window.ethereum.request({
            method: 'wallet_switchEthereumChain',
            params: [{ chainId: web3.utils.toHex(chainId) }]
        });

        await ethereum.request({ method: 'eth_getBalance', params: [accounts[0], 'latest'] }).then((balance) => {
            const balanceInEther = web3.utils.fromWei(balance, 'ether');
            $(".bnb_balance").text(`${balanceInEther} BNB`);
        }).catch((error) => {
            $("#bnb_balance").text(`0.00000000 BNB`);
            console.log('Error getting balance:'+ error);
        });

        document.getElementById("wallet").src = BASEPATH+"/assets/images/c-wallet.png";
    } else {
        is_connected = false;
        $(".connect-wallet").show();
        $(".disconnect-wallet").hide();
        $("#bnb_balance").text(`0.00000000 BNB`);

        document.getElementById("wallet").src = BASEPATH+"/assets/images/d-wallet.png";
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