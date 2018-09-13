</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2"></script>
<script>

$(document).ready(function(){

    $("#registerButton").click(function(){
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
          makeDefaultSwall("Sorry", "Something went wrong", "error");
        }
      });
    })

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
