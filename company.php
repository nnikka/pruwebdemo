<?php 

include("includes/header.php");
include("includes/loggedout.php");

echo $_SESSION['company'];
?>

<a href="includes/logout.php" class="btn btn-default">Logout</a>

<?php include("includes/footer.php"); ?>