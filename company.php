<?php 

include("includes/header.php");
include("includes/loggedout.php");

echo "Hello ". $_SESSION['company'];
?>

<a href="includes/logout.php" class="btn btn-default" style="float: right !important">Logout</a>



<hr />

<a href="addproduct.php" class="btn btn-default" style="float: right !important">Add Product</a>
<h2>Products</h2>
<ul class="list-group">
  <li class="list-group-item">First item</li>
  <li class="list-group-item">Second item</li>
  <li class="list-group-item">Third item</li>
</ul>


<?php include("includes/footer.php"); ?>