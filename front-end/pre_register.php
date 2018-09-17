<?php

include("includes/header.php");
include("includes/loggedin.php");

?>

<h1>Register a new company</h1>
<br/>
<br/>
<br/>
<form method="post" id="formkeystore">
   
    <div class="form-group">
        <label for="name">Password</label>
        <input type="password"  class="form-control" name="password" id="password"><br>
    </div>

    <button type="submit" class="btn btn-default" id="preRegister">Get keys</button>

</form>

<div id="keystorecontent" class="key-store-container" style="text-align: center; display: none;">
    <button class="btn btn-success">Download Key Store File (UTC / JSON)</button>
    <br/>
    <br/>
    <h3 id="privatekeykeystore">713836913i5135jh1983ufojl3j1095u3j5pi13nrfi3185</h3>
</div>



<?php

include("includes/footer.php");

?>




      
