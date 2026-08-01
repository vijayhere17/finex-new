jQuery(document).ready(function() {
	// call function whene page load
    document.getElementById('userwallet').readOnly = true;
});

jQuery('.btn-submit').bind('click', function(e) {
    e.preventDefault();
    if (validate()) {
        processregister();
    }
});


jQuery('.btn-connect').bind('click', async function(e) {
    e.preventDefault();
    
    if (window.ethereum) {
        accounts = await ethereum.request({ method: 'eth_requestAccounts' });
        window.web3 = new Web3(window.ethereum);
        
        is_connected = true;
        
        $(".btn-connect").hide();
        $(".btn-submit").show();

        await window.ethereum.request({
            method: 'wallet_switchEthereumChain',
            params: [{ chainId: web3.utils.toHex(chainId) }]
        });

        $("#userwallet").val(accounts[0]);
    } else {
        is_connected = false;
        $(".btn-connect").show();
        $(".btn-submit").hide();
        $("#userwallet").val('');
    }  
});

function validate() {
    var sponsor_id = $("#sponsor_id").val();
    var firstname = $("#firstname").val();
    var lastname = $("#lastname").val();

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
   
    if (firstname == '') {
        erroralert('Please enter a first name');
        return false;
    }
    
    if (lastname == '') {
        erroralert('Please enter a last name.');
        return false;
    }

    if (!jQuery("#terms").is(":checked")) {
        erroralert('Please accept tangentstake terms of services.');
        return false;
    }

    return true;
}

function processregister() {
    var firstname = $("#firstname").val();
    var lastname = $("#lastname").val();
    var userwallet = $("#userwallet").val();
    
    var sponsor_id = $("#sponsor_id").val();
    var leg = $("#leg").val();

    var reqObj = {
        _token: token,
        firstname : firstname,
        lastname : lastname,
        userwallet : userwallet,
        sponsor_id : sponsor_id,
        leg: leg,
        isapi: 0
    };

    blockui();

    $.ajax({
        type: 'POST',
        url: BASEPATH + "/submit-sign-up",
        data: reqObj,
        dataType: 'json',
        success: function(result) {
            if (result.success) {
                successalert('You have successfully sign up! Login details send your register email id.')
                // successalert('Dear '+firstname+' '+lastname+',<br>You have successfully Signup!<br><b>Login Details</b><br>Username : '+result.username+'<br>Password : '+password+'<br><hr>More details send your email id.');
                resetformdata();
            } else {
                erroralert(result.error);
            }
            unblockui();
        }
    });
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