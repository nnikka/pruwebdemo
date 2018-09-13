

pragma solidity ^0.4.0;

contract Company {

    string private name;
    string private email;
    string private phone;
    string private description;
    string private pub_key;
    address private owner;
    address[] private products;
    mapping (string => address) map;
    
    constructor(string _name, string _email, string _phone, string _description, string _pub_key, address _sender) public {
        name = _name;
        email = _email;
        phone = _phone;
        description = _description;
        pub_key = _pub_key;
        owner = _sender;
    }
    
    modifier isOwner()
    {
        require(msg.sender == owner, "You're not the owner.");
        _;
    }

    
    function getName() public view returns(string){
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