<?php

include("includes/header.php");
include("includes/loggedin.php");

?>

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
        <label for="phone">Phone:</label>
        <input type="number" class="form-control" name="phone" id="phone"><br>
    </div>
    <div class="form-group">
        <label for="description">Description</label>
        <textarea rows="10" class="form-control" id="description" cols="5"></textarea>
    </div>
    
    <button type="submit" class="btn btn-default" id="registerButton">Register</button>
    <a href="login.php" class="btn btn-default">Login</a>
</form>



<?php

include("includes/footer.php");

?>




      
