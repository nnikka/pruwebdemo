<?php 

include("includes/header.php");
include("includes/loggedout.php");
$product_name = isset($_GET['name']) ? $_GET['name'] : null;
?>


<a href="includes/logout.php" class="btn btn-default" style="float: right !important">Logout</a>
<a href="company.php" class="btn btn-default">Company Page</a>




<a href="addParty.php?name=<?php echo $product_name;?>" class="btn btn-default" style="float: right !important">Add Party</a>

<a class="btn btn-default" id="showMeProductParties">Show me this product's parties</a>

<h2></h2>


<h2>Parties</h2>

<ul class="list-group" id="products">
    
</ul>


<?php include("includes/footer.php"); ?>