<html>
<body>


<!-- Latest compiled and minified CSS -->
<meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<div class="container">
    <h1>Register Company</h1>
    <form method="post">
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control" name="name" id="name"><br>
        </div>
        <div class="form-group">
            <label for="email">Email address:</label>
            <input type="email" class="form-control" name="email" id="email"><br>
        </div>
        <div class="form-group">
            <label for="name">Password</label>
            <input type="password"  class="form-control" name="password" id="password"><br>
        </div>
        <div class="form-group">
            <label for="phone">Phone:</label>
            <input type="number" class="form-control" name="phone" id="phone"><br>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea rows="10" class="form-control" cols="5"></textarea>
        </div>
        <div class="form-group">
            <label for="pub_key">Public Key</label>
            <input type="text"  name="pub_key" id="pub_key" class="form-control"><br>
        </div>
        <button type="submit" class="btn btn-default" id="registerButton">Submit</button>
    </form>
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
