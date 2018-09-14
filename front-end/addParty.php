<?php 
include("includes/header.php");
include("includes/loggedout.php");
?>

<h1>Add Party</h1>

<form>
    <div class="form-group">
        <label for="name">Party Name:</label>
        <input type="name" class="form-control" name="party_name" id="party_name"><br>
    </div>
    <div class="form-group">
        <label for="description">Party Description</label>
        <textarea type="text"  class="form-control" name="party_description" id="party_description"></textarea><br>
    </div>
    <div class="form-group">
        <label for="description">Party Quantity</label>
        <input type="number"  class="form-control" name="party_quantity" id="party_quantity"></textarea><br>
    </div>
    <button type="submit" class="btn btn-default" id="addParty">Add Party</button>

</form>


<?php include("includes/footer.php");?>
