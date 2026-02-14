<?php
include '../db/config.php';

$q = mysqli_query($conn,"SELECT * FROM packages");

while($row = mysqli_fetch_assoc($q)){
?>

<div class="card">
<h3><?php echo $row['name']; ?></h3>
<p>₹<?php echo $row['price']; ?></p>
<button>Add To Cart</button>
</div>

<?php } ?>
