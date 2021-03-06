</div>


<?php 
   $company_name = isset($_SESSION['company']) ? $_SESSION['company'] : null;
   $product_name = isset($_GET['name']) ? $_GET['name'] : null;
   $party_name = isset($_GET['prod_name']) ? $_GET['prod_name'] : null;
   $public_key = isset($_GET['public_key']) ? $_GET['public_key'] : null;
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
<script language="javascript" type="text/javascript" src="assets/company_factory.js"></script>
<script language="javascript" type="text/javascript" src="assets/company.js"></script>
<script language="javascript" type="text/javascript" src="assets/product.js"></script>
<script src="assets/web3.min.js"></script>
<script src="assets/public_key_bundle.js"></script>
<script src="assets/elliptic.js"></script>
<script src="assets/ethereumjs-wallet.js"></script>
<script src="assets/buffer.js"></script>
<script src="assets/uuid.js"></script>

<!-- ipfs -->
<script src="https://unpkg.com/ipfs-api/dist/index.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/ipfs/dist/index.min.js"></script>

<script>

$(document).ready(function(){
    var util = require("ethereumjs-util");
    var Wallet = require("ethereumjs-wallet");
   
    var elliptic = require("elliptic");
    var EC = require('elliptic').ec;

    //ipfs
    var ipfs = window.IpfsApi({"host":'ipfs.infura.io',"port":'5001',"protocol":"https"});
   
    
    var companyFactoryAddress;
    
    //abis
    var companyFactoryAbi;
    var companyAbi;
    var productAbi;

    //Apps
    var companyFactoryApp;
    var companyApp;
    var productApp;

    //uuid
    var uuidsExists = false;
    var uuidCount = 0;
    var uuidsArray=[];


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
			companyFactoryAddress = "0xB12e02F17219466619c31ae6d6AD9c4931089318";
			$.ajaxSetup({async: false});
      $.get("assets/company_factory.js", function(data) { companyFactoryAbi = JSON.parse(data); }, "text");
      $.get("assets/company.js", function(data) { companyAbi = JSON.parse(data); }, "text");
      $.get("assets/product.js", function(data) { productAbi = JSON.parse(data); }, "text");
			companyFactoryApp = new web3.eth.Contract(companyFactoryAbi, companyFactoryAddress);
      $.ajaxSetup({async: true});
    }
    
    $("#preRegister").on('click',function(e){
      e.preventDefault();
      var password = $('#password').val();
      if(password.length <= 6) makeDefaultSwall("sorry", "provide more than 6 length password");
      else{
        var ec = new EC('secp256k1');
        var key = ec.genKeyPair();
        var private_key_elliptic = key.priv.toString(16);
        var pubPoint = key.getPublic();
        /*This prefix represents the encoding of the public key:
            0x04 - both x and y of the elliptic curve point follows
            0x02,0x03 - only x follows (y is either odd or even depending on the prefix)
        */
        var public_key_elliptic = pubPoint.encode('hex').substring(2); //meaning it has 04 in front.
        var private_key_buffer  = Buffer.Buffer.from(private_key_elliptic, 'hex');
        var wallet = Wallet.fromPrivateKey(private_key_buffer);
        var keyStoreText = wallet.toV3String(password);
        $(document).on("click","#download_key_store",function() {
          downloadKeyStore("keystore.2018", keyStoreText); 
        });
        $("#privatekeykeystore").html(private_key_elliptic);
        $("#keystorecontent").show();

        $('#keystorecontent').append("<a class='btn btn-success' href='register.php?public_key="+public_key_elliptic+"'>Next</a>");
      }
      
    })
    
    $("#registerButton").click(function(e){
      e.preventDefault();
      var name = $('#name').val();
      var email = $('#email').val();
      var description = $('#description').val();
      var phone = $('#phone').val();
      
      if(name == "" || email == "" || description == "" || phone == "") makeDefaultSwall("Sorry", "Provide all fields", "error");
      else{
        <?php echo "var public_key_elliptic = '" .$public_key . "';"; ?>
        var msg = web3.utils.sha3('hello!');
        web3.eth.sign(msg, userAccount, function (err, result) { 
            let sig = result;
            const {v, r, s} = util.fromRpcSig(sig);
            const pubKey  = util.ecrecover(util.toBuffer(msg), v, r, s);
            const addrBuf = util.pubToAddress(pubKey);
            const addr    = util.bufferToHex(addrBuf);
            var pub_key = pubKey.toString("hex");
            $.ajax({
              method:"POST",
              url:"api.php",
              data:{function_name:"register_company",name:name, phone:phone, description:description, email:email,public_key_elliptic:public_key_elliptic, pub_key:pub_key}
            }).done(function(success){
              if(success == 200){
                $("#smartContractLoader").show();
                companyFactoryApp.methods.createContract(web3.utils.utf8ToHex(name.toString()), email.toString(), phone.toString() , description.toString(), pub_key.toString()).send({from:userAccount})
                .on("receipt",function(receipt){
                  $("#smartContractLoader").hide();
                  makeButtonSwall("Congrats","Your info is saved in smart contract", "success", false, "login.php");
                  
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
      }
    

    })

   

    
    function getKeyStoreFile(){
      var fileToLoad = document.getElementById("keystore").files[0];

      var fileReader = new FileReader();
      fileReader.onload = function(fileLoadedEvent){
          var textFromFileLoaded = fileLoadedEvent.target.result;
          const myWallet = Wallet.fromV3(textFromFileLoaded, "123", true);
      };
      
      fileReader.readAsText(fileToLoad, "UTF-8");
    }




    $('#loginButton').click(function(e){
      e.preventDefault();
      var fileToLoad = document.getElementById("keystore").files[0];
      var password = $('#password').val();
      if(password == "" || fileToLoad == undefined) makeDefaultSwall("Sorry", "please upload a keystore and provide a password to unlock it.");
      else{
        var fileReader = new FileReader();
        fileReader.onload = function(fileLoadedEvent){
            var textFromFileLoaded = fileLoadedEvent.target.result;
            try{
              var myWallet = Wallet.fromV3(textFromFileLoaded, password, true);
              var public_key =  myWallet.getPublicKey().toString("hex");
              var private_key = myWallet.getPrivateKey().toString("hex");
              window.localStorage.setItem('public_key', public_key);
              window.localStorage.setItem('private_key', private_key);
              $.ajax({
                  method:"POST",
                  url:"api.php",
                  data:{function_name:"login_company", public_key_elliptic:public_key}
              }).done(function(success){
                if(success == 200){
                  window.location.href = "company.php";
                }else{
                  makeDefaultSwall("Sorry", "company doesn't exist", "error");
                }
              });
            }catch(err){
              makeDefaultSwall("Sorry", "Your keystore file is incorrect or password to unlock it is not right","error");
            }
        };
      
        fileReader.readAsText(fileToLoad, "UTF-8");
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
              $("#smartContractLoader").show();
              productApp.methods.addParty(web3.utils.utf8ToHex(party_name), party_quantity.toString(),party_description).send({from:userAccount})
              .on('receipt',function(receipt){
                $("#smartContractLoader").hide();
                makeButtonSwall("Congrats","You've added new party", "success", false, "product_parties.php?name="+product_name);
              })
              .on("error",function(error){
                $("#smartContractLoader").hide();
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
                  html+="<a href='party.php?name="+web3.utils.hexToUtf8(parties[i])+"&prod_name="+product_name+"'"+"><li class='list-group-item'>"+web3.utils.hexToUtf8(parties[i])+"</li></a>";
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
      <?php echo "var product_name = '" .$party_name . "';"; ?>; 
        companyFactoryApp.methods.company_products(web3.utils.utf8ToHex(company_name)).call({from:userAccount}).then(companyAddress=>{
          if(!/^0x0+$/.test(companyAddress)){
            companyApp = new web3.eth.Contract(companyAbi, companyAddress);
            companyApp.methods.product_address(web3.utils.utf8ToHex(product_name)).call({from:userAccount}).then(productAddress=>{
              productApp = new web3.eth.Contract(productAbi, productAddress);
              productApp.methods.getParty(web3.utils.utf8ToHex(party_name)).call().then(party_info=>{
                uuidsExists = (party_info[3] == "") ? false : true;
                uuidCount = party_info[1];
                var html="";
                html+="<li  class='list-group-item'>"+(new Date(party_info[0] * 1000))+"</li>";
                html+="<li  class='list-group-item'>"+(party_info[1])+"</li>";
                html+="<li  class='list-group-item'>"+(party_info[2])+"</li>";
                html+="<li  class='list-group-item'>"+(party_info[3])+"</li>";
                if(!uuidsExists) html +="<br><a class='btn btn-default' id='generateUuids'>Generate UUIDs</a>";
                $('#party_info').html(html);
                if(uuidsExists){
                  ipfs.files.get(party_info[3], function (err, files) {
                    var uuidArray =  uuidsArray = JSON.parse("[" + files[0].content.toString() + "]")[0];
                    var uuids = "<h2>Generated UUIDS Which you have to sign with your private key to get QR codes</h2>";

                    for(var i=0;i<uuidArray.length;i++){
                      uuids+="<li  class='list-group-item'>"+uuidArray[i]+"</li>";
                    }
                    uuids+="<a class='btn btn-success' id='sign_uuids'>Sign UUids</a>";
                    $('#generated_uuids').html(uuids);

                  })
                }
              })
            })
          }else{
            makeDefaultSwall("Not found", "company address not exists. there must be an error", "error");
          }
        });
    })

    $(document).on("click", "#sign_uuids",function(){
      var private_key = window.localStorage.getItem('private_key');
      var public_key = window.localStorage.getItem('public_key');
      var query = "INSERT INTO MyTable ( signature, uuid, public_key ) VALUES ";
      for(var i=0;i<uuidsArray.length;i++){
        var signatureObject = web3.eth.accounts.sign(uuidsArray[i], userAccount);
        var signature = signatureObject.signature;
        query += "('" + signature + "', '" + uuidsArray[i] + "', '" + public_key + "')";
        if(uuidsArray.length - 1 == i){
          query += ";";
        }else{
          query += ",";
        }
      } 
      $.ajax({
            method:"POST",
            url:"api.php",
            data:{function_name:"add_signature", sql:query }
        }).done(function(success){
          if(success == 200){
            makeButtonSwall("Congrats","You signed uuids successfuly", "success", false, "login.php");
          }else{
            makeDefaultSwall("Sorry", "Something bad happened during signing","error");
          }
        });
    })
   
      

    
  
    
    $(document).on("click","#generateUuids",function() {
        if(!uuidsExists && uuidsExists != undefined && uuidCount != 0){
          var uuidv1 = require('uuid/v1');
          var uuidArray = [];
          let uuid_num = uuidCount;
          $('#generate_uuids').show();
          for(let i = 0; i < uuid_num; i++){
            uuidArray[i] = uuidv1();
          }
          var bufferFile = Buffer.Buffer.from(JSON.stringify(uuidArray));
          ipfs.files.add(bufferFile,(error,result)=>{
            $('#generate_uuids').hide();
            const ipfsHash = result[0].hash;
            <?php echo "var company_name = '" .$company_name . "';"; ?>;
            <?php echo "var party_name = '" .$product_name . "';"; ?>;
            <?php echo "var product_name = '" .$party_name . "';"; ?>; 
            companyFactoryApp.methods.company_products(web3.utils.utf8ToHex(company_name)).call({from:userAccount}).then(companyAddress=>{
              if(!/^0x0+$/.test(companyAddress)){
                companyApp = new web3.eth.Contract(companyAbi, companyAddress);
                companyApp.methods.product_address(web3.utils.utf8ToHex(product_name)).call({from:userAccount}).then(productAddress=>{
                  productApp = new web3.eth.Contract(productAbi, productAddress);
                  $("#smartContractLoader").show();
                  productApp.methods.saveIpfsUuid(web3.utils.utf8ToHex(party_name), ipfsHash).send({from:userAccount})
                  .on('receipt',function(receipt){
                      $("#smartContractLoader").hide();
                      makeButtonSwall("Congrats","You've generated new uuids", "success", false, "party.php?name="+party_name+"&prod_name="+product_name);
                  })
                  .on("error",function(error){
                    $("#smartContractLoader").hide();
                    makeDefaultSwall("Sorry", "generating new uuids couldn't happen. see metamask error for more info","error");
                  });

                })
              }
            })
          })
        }
      })
                  
   


    /* functions */

    function downloadKeyStore(filename, text){
      var element = document.createElement('a');
      element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));
      element.setAttribute('download', filename);

      element.style.display = 'none';
      document.body.appendChild(element);

      element.click();

      document.body.removeChild(element);
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
