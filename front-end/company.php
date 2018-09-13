<?php 

include("includes/header.php");
include("includes/loggedout.php");


?>
<p id="company_name"></p>
<p id="company_email"></p>
<p id="company_phone"><p>
<p id="company_description"></p>
<p id="company_pub_key">


<a href="includes/logout.php" class="btn btn-default" style="float: right !important">Logout</a>



<hr />

<a href="addproduct.php" class="btn btn-default" style="float: right !important">Add Product</a>

<a class="btn btn-default" id="showMeProducts">Show me products and company information</a>

<h2>Products</h2>

<ul class="list-group" id="products">

</ul>


<?php include("includes/footer.php"); ?>