</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
<script language="javascript" type="text/javascript" src="assets/company_factory.js"></script>
<script language="javascript" type="text/javascript" src="assets/company.js"></script>
<script language="javascript" type="text/javascript" src="assets/product.js"></script>
<script src="assets/web3.min.js"></script>
<script>

$(document).ready(function(){
    var companyFactoryAddress;
    
    //abis
    var companyFactoryAbi;
    var companyAbi;
    var productAbi;
    
    var companyApp;
    var userAccount;
    if (typeof web3 !== 'undefined') {
      web3 = new Web3(web3.currentProvider);
      web3.eth.getAccounts().then((data)=>{
					if(data[0] !== userAccount) {
            userAccount = data[0];
            startApp();
          }
			})
    } else {
      alert("You must have metamask installed in order to use this application");
    }

    function startApp() {
			companyFactoryAddress = "0x97855cB400B66cc1905Afc30D10110C6d477d70A";
			$.ajaxSetup({async: false});
			$.get("assets/company_factory.js", function(data) { companyFactoryAbi = JSON.parse(data); }, "text");
      companyApp = new web3.eth.Contract(companyFactoryAbi, companyFactoryAddress);
      $.ajaxSetup({async: true});
    }
    
    
    
    $("#registerButton").click(function(e){
      e.preventDefault();
      var name = $('#name').val();
      var email = $('#email').val();
      var password = $('#password').val();
      var description = $('#description').val();
      var phone = $('#phone').val();
      var pub_key = $('#pub_key').val();
      $.ajax({
        method:"POST",
        url:"api.php",
        data:{function_name:"register_company",name:name, phone:phone, password:password, description:description, email:email, pub_key:pub_key}
      }).done(function(success){
        if(success == 200){
          makeDefaultSwall("Good Job", "You registered successfully, Confirm transaction in Metamask and wait for smart contract insertion", "success");
          companyApp.methods.createContract(web3.utils.asciiToHex(name.toString()), email.toString(), phone.toString() , description.toString(), pub_key.toString()).send({from:userAccount})
          .on("receipt",function(receipt){
            makeDefaultSwall("Congratulation", "Your info is saved in smart contract", "success");
            console.log(receipt)
          })
          .on("error",function(err){
            makeDefaultSwall("Upps..", "Your info isn't saved in smart contract", "error");
          });
        }else{
          alert(success);
          makeDefaultSwall("Sorry", "Something went wrong", "error");
        }
      });
    })

    $('#loginButton').click(function(e){
      e.preventDefault();
      var email = $("#email").val();
      var password = $('#password').val();
      if(email == "" || password == "") makeDefaultSwall("Not Good", "Provide Email and Password Both", "error");
      else {
          $.ajax({
            method:"POST",
            url:"api.php",
            data:{function_name:"login_company", email:email, password:password}
          }).done(function(success){
            if(success == 200){
              window.location.href = "company.php";
            }else{
              makeDefaultSwall("Sorry", "company doesn't exist", "error");
            }
         });
      }
    });

    $('#addProductButton').click(function(e){
      e.preventDefault();
      var name = $("#name").val();
      var description = $('#description').val();
      if(name != "" && description != "") makeDefaultSwall(name, description,  "success");
    });

    function makeDefaultSwall(header,body,status){
        swal(
          header,
          body,
          status
        );
    }

   
});
</script>

</body>
</html>
