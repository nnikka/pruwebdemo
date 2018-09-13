</div>


<?php 
   $company_name = isset($_SESSION['company']) ? $_SESSION['company'] : null;
   
?>

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

    //Apps
    var companyFactoryApp;
    var companyApp;
    var productApp;


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
			companyFactoryAddress = "0xC252A358290CC85D5ad684A8d6736520E7a33ea3";
			$.ajaxSetup({async: false});
      $.get("assets/company_factory.js", function(data) { companyFactoryAbi = JSON.parse(data); }, "text");
      $.get("assets/company.js", function(data) { companyAbi = JSON.parse(data); }, "text");
      $.get("assets/product.js", function(data) { productAbi = JSON.parse(data); }, "text");
			companyFactoryApp = new web3.eth.Contract(companyFactoryAbi, companyFactoryAddress);
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
      <?php echo "var company_name = '" .$company_name . "';"; ?>

      if(name == "" && description == "") makeDefaultSwall("Bad Luck", "Provide both fields",  "error");
      else if(company_name == null) makeDefaultSwall("Bad Luck", "Please login again from scratch",  "error");
      else{
        companyFactoryApp.methods.company_products(web3.utils.asciiToHex(company_name)).call({from:userAccount}).then(companyAddress=>{
          if(!/^0x0+$/.test(companyAddress)){
            companyApp = new web3.eth.Contract(companyAbi, companyAddress);
            companyApp.methods.createProduct(web3.utils.asciiToHex(name),description).send({from:userAccount})
                  .on('receipt',function(receipt){
                    makeDefaultSwall("Good job", "your transaction has been included in a block. wait 20 seconds, go back to the company page and click show me products again");
                  })
                  .on("error",function(error){
                      makeDefaultSwall("Error", "Sorry your transaction won't be included in a block. See metamask error or try again", "error");
                  });
          }else{
            makeDefaultSwall("Not found", "company address not exists. there must be an error");
          }
        });
      }
    });

    $('#showMeProducts').click(function(e){
      <?php echo "var company_name = '" .$company_name . "';"; ?>
      companyFactoryApp.methods.company_products(web3.utils.asciiToHex(company_name)).call({from:userAccount}).then(companyAddress=>{
        if(!/^0x0+$/.test(companyAddress)){
          companyApp = new web3.eth.Contract(companyAbi, companyAddress);
          companyApp.methods.getCompanyInformation().call().then(companyInformation=>{
            console.log(companyInformation);
            $('#company_name').val(companyInformation[0]);
            $('#company_email').val(companyInformation[1]);
            $('#company_phone').val(companyInformation[2]);
            $('#company_description').val(companyInformation[3]);
            $('#company_pub_key').val(companyInformation[4]);
          
            companyApp.methods.getAllProducts().call().then(allProducts=>{
              var html;
              for(var i=0;i<allProducts.length;i++){
                html+="<li class='list-group-item'">+allProducts[i]+"</li>";
              }
              $("#products").html(html);
            });
          });
          
        }else{
          makeDefaultSwall("Not found", "company address not exists. there must be an error");
        }
      });
        
    });

    

    /* functions */

    function getAllProducts(){

    }

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
