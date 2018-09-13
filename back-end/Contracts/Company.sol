

pragma solidity ^0.4.0;

import "./Product.sol";

contract Company {

    bytes32 private name;
    string private email;
    string private phone;
    string private description;
    string private pub_key;

    address private owner;
    
    mapping(bytes32=>address) public product_address;
    bytes32[] public product_names;
    
    constructor(bytes32 _name, string _email, string _phone, string _description, string _pub_key, address _sender) public {
        name = _name;
        email = _email;
        phone = _phone;
        description = _description;
        pub_key = _pub_key;
        owner = _sender;
    }


    function createProduct(bytes32 _name, string _description) public isOwner {
        address prod_address = new Product(_name, _description, msg.sender);
        product_names.push(_name);
        product_address[_name] = prod_address;
    }

    function getAllProducts() public view returns(bytes32[]){
        return product_names;
    }
    
    modifier isOwner()
    {
        require(msg.sender == owner, "You're not the owner.");
        _;
    }


    function getCompanyInformation() public view returns(bytes32,string, string, string, string){
        return (name,email,phone,description, pub_key);
    }
    
    function getName() public view returns(bytes32){
        return name;
    }
    function getEail() public view returns(string){
        return email;
    }
    function getPhone() public view returns(string){
        return phone;
    }
    function getDescription() public view returns(string){
        return description;
    }
    function getOwner() public view returns(address){
        return owner;
    }
}