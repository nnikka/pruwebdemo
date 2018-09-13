
pragma solidity ^0.4.0;


contract Product {

    bytes32 private name;
    string private description;
    address private owner;

    constructor(bytes32 _name, string _description, address sender) public{
        name = _name;
        description = _description;
        owner = sender;
    }

    function getName() public view returns(bytes32){
        return name;
    }

    function getDescription() public view returns(string){
        return description;
    }

}