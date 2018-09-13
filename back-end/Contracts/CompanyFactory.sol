
pragma solidity ^0.4.0;

import "./Company.sol";

contract CompanyFactory {
   
    mapping(bytes32=>address) public company_products; 
    function createContract (bytes32 _name, string _email, string _phone, string _description, string _pub_key) public {
        address newCompany = new Company(_name, _email, _phone, _description, _pub_key, msg.sender);
        company_products[_name] = newCompany;
    } 
}
