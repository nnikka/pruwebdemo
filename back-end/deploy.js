const Web3 = require('web3')
const fs = require('fs')
const path = require('path')
const HDWalletProvider = require("truffle-hdwallet-provider");


let AbiCompanyFactory,BytecodeCompanyFactory;


var contentOfCompanyFactory = fs.readFileSync("Contract_Files/CompanyFactory.json",'utf8');

abiCompanyFactory = JSON.parse(JSON.parse(contentOfCompanyFactory)['interface']);
byteCodeCompanyFactory = (JSON.parse(contentOfCompanyFactory)['bytecode']);


const provider = new HDWalletProvider(
    'butter salmon fabric unique weather boil someone transfer arrest ship blade direct',
    'https://rinkeby.infura.io/AyX1Z7pHB4WTlfKIJE7h'
)

const web3 = new Web3(provider);

fs.writeFile(path.join(__dirname, '../front-end')+"/assets/contract_abi.js", JSON.stringify(abiCompanyFactory), 'utf8', (err,result)=>{
    if(err) throw err;
});

web3.eth.getAccounts().then(accounts=>{
   let myAccount = accounts[0];
   var myContract = new web3.eth.Contract(abiCompanyFactory)
   .deploy({data:byteCodeCompanyFactory, arguments: []})
   .send({
        from: myAccount,
        gas: 1500000,
        gasPrice:20000000000 //price is in wei.
    }).then(data => {
        var address =data.options.address;
        console.log(address);
    })
},err=>{
    throw err;
})