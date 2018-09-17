<?php 

include("includes/header.php");
include("includes/loggedout.php");


?>


<a href="includes/logout.php" class="btn btn-default">Logout</a>






<a class="btn btn-default" id="showMePartyInformation">Show me Party INformation</a>
<a class="btn btn-default" href="company.php">Company Page</a>

<h2>Party Information</h2>
<ul class="list-group" id="party_info">


</ul>

<hr>
<ul class="list-group" id="generated_uuids">


</ul>


<?php include("includes/footer.php"); ?>