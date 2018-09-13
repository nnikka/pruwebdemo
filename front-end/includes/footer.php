</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
<script language="javascript" type="text/javascript" src="assets/contract_abi.js"></script>
<script src="assets/web3.min.js"></script>
<script>

$(document).ready(function(){
    var contractAddress;
    var contractAbi;
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
			contractAddress = "0x67b7525d01ba2576ed34e24b016a8ada8955a06b";
			$.ajaxSetup({async: false});
			$.get("assets/contract_abi.js", function(data) { contractAbi = JSON.parse(data); }, "text");
			companyApp = new web3.eth.Contract(contractAbi, contractAddress);
      $.ajaxSetup({async: true});
      companyApp.methods.createContract("gio", "ss","ss","sdf","551").send({from:userAccount})
          .on("receipt",function(receipt){
            console.log(receipt);
          })
          .on("error",function(err){
            console.log(err);
          });
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
          makeDefaultSwall("Good Job", "You registered successfully", "success");
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
