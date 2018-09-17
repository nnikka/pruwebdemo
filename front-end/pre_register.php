<?php

include("includes/header.php");
include("includes/loggedin.php");

?>

<h1>Register a new company</h1>

<form method="post" id="formkeystore">
    <div class="form-group">
        <label for="name">Password</label>
        <input type="password"  class="form-control" name="password" id="password"><br>
    </div>
    <button type="submit" class="btn btn-default" id="preRegister">Get keys</button>
</form>

<div id="keystorecontent" class="key-store-container" style="text-align: center; display: none;">
    <button id="download_key_store" class="btn btn-success">Download Key Store File (UTC / JSON)</button>
    <br/>
    <br/>
    <h3 id="privatekeykeystore"></h3>
</div>



<?php

include("includes/footer.php");

?>




      
