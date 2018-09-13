

const fs = require('fs');
const path = require('path');
const solc = require('solc')
const directory_of_ready_contracts = 'Contract_Files';

fs.readdir(directory_of_ready_contracts, (err, files) => {
    if (err) throw err;
    for (const file of files) {
        fs.unlink(path.join(directory_of_ready_contracts, file), err => {
            if (err) throw err;
        });
    }
});


var input = {
    'Company.sol': fs.readFileSync(path.resolve(__dirname, "Contracts", "Company.sol"),"utf8"),
    'CompanyFactory.sol': fs.readFileSync(path.resolve(__dirname, "Contracts", "CompanyFactory.sol"),"utf8"),
    'Product.sol': fs.readFileSync(path.resolve(__dirname, "Contracts", "Product.sol"),"utf8"),
}


var output = solc.compile({ sources: input }, 1)

for( let contract in output.contracts ) {
    let name = contract.split(":")[1];
    let contractJson = JSON.stringify(output.contracts[contract]);
    fs.writeFile(directory_of_ready_contracts+"/"+name+".json", contractJson, 'utf8', (err,result)=>{
        if(err) throw err;
    });
   
}