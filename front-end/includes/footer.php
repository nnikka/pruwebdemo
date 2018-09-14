</div>


<?php 
   $company_name = isset($_SESSION['company']) ? $_SESSION['company'] : null;
   $product_name = isset($_GET['name']) ? $_GET['name'] : null;
   
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
			companyFactoryAddress = "0x87193adD843e47a7BdcB76Da55fE906d3162C317";
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
          $("#smartContractLoader").show();
          companyFactoryApp.methods.createContract(web3.utils.utf8ToHex(name.toString()), email.toString(), phone.toString() , description.toString(), pub_key.toString()).send({from:userAccount})
          .on("receipt",function(receipt){
            $("#smartContractLoader").hide();
            makeButtonSwall("Congrats","Your info is saved in smart contract", "success", false, "login.php");
            console.log(receipt)
          })
          .on("error",function(err){
            $("#smartContractLoader").hide();
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
        companyFactoryApp.methods.company_products(web3.utils.utf8ToHex(company_name)).call({from:userAccount}).then(companyAddress=>{
          if(!/^0x0+$/.test(companyAddress)){
            companyApp = new web3.eth.Contract(companyAbi, companyAddress);
            $("#smartContractLoader").show();
            companyApp.methods.createProduct(web3.utils.utf8ToHex(name),description).send({from:userAccount})
                  .on('receipt',function(receipt){
                    $("#smartContractLoader").hide();
                    makeButtonSwall("Good Job", "your transaction has been included in a block. wait 20 seconds, go back to the company page and click show me products again", "success", false, "company.php");
                  })
                  .on("error",function(error){
                    $("#smartContractLoader").hide();
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
      companyFactoryApp.methods.company_products(web3.utils.utf8ToHex(company_name)).call({from:userAccount}).then(companyAddress=>{
        if(!/^0x0+$/.test(companyAddress)){
          companyApp = new web3.eth.Contract(companyAbi, companyAddress);
          companyApp.methods.getCompanyInformation().call().then(companyInformation=>{
            var companyInfoHtml="";
            companyInfoHtml+="<li  class='list-group-item'>"+web3.utils.hexToUtf8(companyInformation[0])+"</li>";
            companyInfoHtml+="<li  class='list-group-item'>"+companyInformation[1]+"</li>";
            companyInfoHtml+="<li  class='list-group-item'>"+companyInformation[2]+"</li>";
            companyInfoHtml+="<li  class='list-group-item'>"+companyInformation[3]+"</li>";
            companyInfoHtml+="<li  class='list-group-item'>"+companyInformation[4]+"</li>";
            $('#company_info').html(companyInfoHtml);

            companyApp.methods.getAllProducts().call().then(allProducts=>{
              var html="";
              for(var i=0;i<allProducts.length;i++){
                var product = web3.utils.hexToUtf8(allProducts[i]);
                console.log(product);
                html+="<a href='product_parties.php?name="+product+"'"+"><li class='list-group-item'>"+product+"</li></a>";
              }
              
              $("#products").html(html);
            });
          });
          
        }else{
          makeDefaultSwall("Not found", "company address not exists. there must be an error");
        }
      });
        
    });

    $('#addParty').on('click',function(e){
      e.preventDefault();
      var party_name = $("#party_name").val();
      var party_description = $("#party_description").val();
      var party_quantity = $("#party_quantity").val();
      
      if(party_name == "" || party_description == "" || party_quantity == "") makeDefaultSwall("Bad Luck", "Provide all fields","error");
      else{
        <?php echo "var product_name = '" .$product_name . "';"; ?>;
        <?php echo "var company_name = '" .$company_name . "';"; ?>;
        console.log(product_name);
        companyFactoryApp.methods.company_products(web3.utils.utf8ToHex(company_name)).call({from:userAccount}).then(companyAddress=>{
          if(!/^0x0+$/.test(companyAddress)){
            companyApp = new web3.eth.Contract(companyAbi, companyAddress);
            companyApp.methods.product_address(web3.utils.utf8ToHex(product_name)).call({from:userAccount}).then(productAddress=>{
              productApp = new web3.eth.Contract(productAbi, productAddress);
              productApp.methods.addParty(web3.utils.utf8ToHex(party_name),party_quantity,party_description).send({from:userAccount})
                    .on('receipt',function(receipt){
                      makeButtonSwall("Congrats","You've added new party", "success", false, "product_parties.php?name="+product_name);
                    })
                    .on("error",function(error){
                      makeDefaultSwall("Sorry", "product couldn't be added. see metamask error for more info","error");
                    });
            })
                  
          }else{
            makeDefaultSwall("Not found", "company address not exists. there must be an error", "error");
          }
        });
      }
    })

    $('#showMeProductParties').on('click',function(e){
      e.preventDefault();
      <?php echo "var company_name = '" .$company_name . "';"; ?>;
      <?php echo "var product_name = '" .$product_name . "';"; ?>;
      console.log(product_name);
      console.log(company_name);
        companyFactoryApp.methods.company_products(web3.utils.utf8ToHex(company_name)).call({from:userAccount}).then(companyAddress=>{
          if(!/^0x0+$/.test(companyAddress)){
            companyApp = new web3.eth.Contract(companyAbi, companyAddress);
            companyApp.methods.product_address(web3.utils.utf8ToHex(product_name)).call({from:userAccount}).then(productAddress=>{
              productApp = new web3.eth.Contract(productAbi, productAddress);
              productApp.methods.getPartyNames().call().then(parties=>{
                var html = "";
                console.log(parties);
                for(var i=0;i<parties.length;i++){
                  html+="<a href='party.php?name="+web3.utils.hexToUtf8(parties[i])+"'"+"><li class='list-group-item'>"+web3.utils.hexToUtf8(parties[i])+"</li></a>";
                }
                $('#products').html(html);
              })
            })
          }else{
            makeDefaultSwall("Not found", "company address not exists. there must be an error", "error");
          }
        });
    })

    $('#showMePartyInformation').on('click',function(){
      
      <?php echo "var company_name = '" .$company_name . "';"; ?>;
      <?php echo "var party_name = '" .$product_name . "';"; ?>;
        
        companyFactoryApp.methods.company_products(web3.utils.utf8ToHex(company_name)).call({from:userAccount}).then(companyAddress=>{
          if(!/^0x0+$/.test(companyAddress)){
            companyApp = new web3.eth.Contract(companyAbi, companyAddress);
            companyApp.methods.product_address(web3.utils.utf8ToHex(party_name)).call({from:userAccount}).then(productAddress=>{
              productApp = new web3.eth.Contract(productAbi, productAddress);
              productApp.methods.getParty(web3.utils.utf8ToHex(party_name)).call().then(party_info=>{
                var html="";
                console.log(party_info);
                html+="<li  class='list-group-item'>"+(party_info[0])+"</li>";
                html+="<li  class='list-group-item'>"+(party_info[1])+"</li>";
                html+="<li  class='list-group-item'>"+(party_info[2])+"</li>";
                $('#party_info').html(html);
              })
            })
          }else{
            makeDefaultSwall("Not found", "company address not exists. there must be an error", "error");
          }
        });
    })

    

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
  
    function makeButtonSwall(title,text,type, cancelButton, redirectUri){
      swal({
          title: title,
          text: text,
          type: type,
          showCancelButton:cancelButton,
          confirmButtonColor: '#DD6B55',
          confirmButtonText: 'Yes!',
          cancelButtonText: 'No.'
        },
        function(isConfirm){
          if(isConfirm){
            location.href = redirectUri;
          }else{

          }
        }
      )
    }

   
});
</script>

</body>
</html>
