const Web3Modal = window.Web3Modal.default;
const WalletConnectProvider = window.WalletConnectProvider.default;
const Fortmatic = window.Fortmatic;
const evmChains = window.evmChains;

const obscureAddress = (address) => {
    return address.substring(0, 6) + '...'+address.substring(address.length - 4, address.length);
}

let web3Modal, provider, web3, selectedAccount;

function init() 
{
    const providerOptions = {
        walletconnect: {
            package: WalletConnectProvider,
            options: {
                rpc: {
                    137: "https://polygon-rpc.com/", 
                },
                chainId: 137,
            },
        },
    };
  
    web3Modal = new Web3Modal({
        cacheProvider: true, 
        providerOptions, 
        disableInjectedProvider: false,
    });
}

async function onConnect() 
{
    try {
        provider = await web3Modal.connect();
        web3 = new Web3(window.ethereum);
    } catch(e) {
        console.log("Could not get a wallet connection", e);
        return;
    }

    const chainId = await web3.eth.getChainId();
    const desiredChainId = 137; // Polygon Mumbai Testnet

    if (chainId !== desiredChainId) 
    {
        await switchToCorrectChain(desiredChainId);
    }

    await fetchAccountData();
    
    // Subscribe to accounts change
    provider.on("accountsChanged", (accounts) => {
      fetchAccountData();
    });
  
    // Subscribe to chainId change
    provider.on("chainChanged", (chainId) => {
      fetchAccountData();
    });
  
    // Subscribe to networkId change
    provider.on("networkChanged", (networkId) => {
      fetchAccountData();
    });

    $(".connect-btn").hide();
    $(".connected-btn").show();

    $(".sel_address").html(obscureAddress(selectedAccount))
}

async function fetchAccountData() 
{
    const web3 = new Web3(provider);
  
    const chainId = await web3.eth.getChainId();
    
    // const chainData = evmChains.getChain(chainId);
    // console.log('chain name',chainData.name);
  
    const accounts = await web3.eth.getAccounts();
  
    // console.log("Got accounts", accounts);

    selectedAccount = accounts[0];

    const rowResolvers = accounts.map(async (address) => {
        const balance = await web3.eth.getBalance(address);
        const ethBalance = web3.utils.fromWei(balance, "ether");
        const humanFriendlyBalance = parseFloat(ethBalance).toFixed(4);

        // console.log('balance : ',humanFriendlyBalance);
    });
    
    await Promise.all(rowResolvers);
}

async function switchToCorrectChain(chainId) {
    const chainHex = "0x" + chainId.toString(16); // Convert to hex
    try {
        await window.ethereum.request({
            method: "wallet_switchEthereumChain",
            params: [{ chainId: chainHex }],
        });
        console.log(`Switched to chain ${chainHex}`);
    } catch (error) {
        
        console.error("Failed to switch network:", error);
    }
}

async function onDisconnect() 
{
    console.log("Killing the wallet connection", provider);
  
    // TODO: Which providers have close method?
    if(provider.close) 
    {
        await provider.close();
        await web3Modal.clearCachedProvider();
        provider = null;
    }
  
    selectedAccount = null;
}


window.addEventListener('load', async () => {
    await init();
    onConnect();
});
  