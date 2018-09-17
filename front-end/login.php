<?php 
include("includes/header.php");
include("includes/loggedin.php");
?>

<h1>Login Company</h1>
<form method="post">
    
    <div class="form-group">
        <label for="keystore">Upload KeyStore File</label>
        <input type="file" class="form-control" name="keystore" id="keystore"><br>
    </div>
    <div class="form-group">
        <label for="keystore">Password for keystore</label>
        <input type="password" class="form-control" name="password" id="password"><br>
    </div>
   
  
    <button type="submit" class="btn btn-default" id="loginButton">Login</button>
    <a href="pre_register.php" class="btn btn-default">Register</a>
</form>


<?php include("includes/footer.php");?>
