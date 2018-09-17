pragma solidity ^0.4.0;


contract Product {

    bytes32 private name;
    string private description;
    address private owner;
    
    struct partyStruct{
        uint time;
        uint256 quantity;
        string description;
        string ipfsUuid;
    }
    mapping(bytes32=>partyStruct) public productParties;
    bytes32[] public partyNames;
    constructor(bytes32 _name, string _description, address sender) public{
        name = _name;
        description = _description;
        owner = sender;
    }

    function addParty(bytes32 _name, uint256 _quantity, string _description) public{
        partyStruct memory structure = partyStruct({
            time:block.timestamp,
            quantity:_quantity,
            description: _description,
            ipfsUuid:""
        });

        partyNames.push(_name);
        productParties[_name] = structure;
    }

    function getParty(bytes32 _name) public view returns(uint, uint256, string, string){
        partyStruct storage structure = productParties[_name];
        return (structure.time, structure.quantity, structure.description, structure.ipfsUuid);
    }

    function saveIpfsUuid(bytes32 _name, string _ipfsHash) public {
        partyStruct storage structure = productParties[_name];
        structure.ipfsUuid = _ipfsHash;
    }

    function getPartyNames() public view returns(bytes32[]){
        return partyNames;
    }

    function getProductInfo() public view returns(bytes32, string){
        return (name,description);
    }
}