<?php 
include("includes/header.php");
include("includes/loggedout.php");
?>

<h1>Add Prduct</h1>

<form>
    <div class="form-group">
        <label for="name">Product Name:</label>
        <input type="name" class="form-control" name="name" id="name"><br>
    </div>
    <div class="form-group">
        <label for="description">Product Description</label>
        <textarea type="text"  class="form-control" name="description" id="description"></textarea><br>
    </div>
    <button type="submit" class="btn btn-default" id="addProductButton">Add Product</button>

</form>


<?php include("includes/footer.php");?>
