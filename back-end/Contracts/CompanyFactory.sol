
pragma solidity ^0.4.0;

import "./Company.sol";

contract CompanyFactory {
    address[] companies;

    function createContract (string _name, string _email, string _phone, string _description, string _pub_key) public {
        address newCompany = new Company(_name, _email, _phone, _description, _pub_key, msg.sender);
        companies.push(newCompany);
    } 
}
