<?php

include("includes/header.php");
include("includes/loggedin.php");

?>

<h1>Register a new company</h1>
<form method="post">
   
    <div class="form-group">
        <label for="name">Password</label>
        <input type="password"  class="form-control" name="password" id="password"><br>
    </div>
    <button type="submit" class="btn btn-default" id="preRegister">Get keys</button>

</form>



<?php

include("includes/footer.php");

?>




      
