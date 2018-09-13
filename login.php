<?php 
include("includes/header.php");
include("includes/loggedin.php");
?>

<h1>Login Company</h1>
<form method="post">
    
    <div class="form-group">
        <label for="email">Email address:</label>
        <input type="email" class="form-control" name="email" id="email"><br>
    </div>
    <div class="form-group">
        <label for="name">Password</label>
        <input type="password"  class="form-control" name="password" id="password"><br>
    </div>
  
    <button type="submit" class="btn btn-default" id="loginButton">Submit</button>
    <a href="register.php" class="btn btn-default">Register</a>
</form>


<?php include("includes/footer.php");?>
