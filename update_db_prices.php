<?php
include 'db/config.php';

// Check if packages table has price column, if not add it
$check = mysqli_query($conn, "SHOW COLUMNS FROM packages LIKE 'price'");
if (mysqli_num_rows($check) == 0) {
    mysqli_query($conn, "ALTER TABLE packages ADD COLUMN price INT DEFAULT 500");
    echo "Price column added to packages table.<br>";
}

// Update demo prices
mysqli_query($conn, "UPDATE packages SET price = 350 WHERE name LIKE '%CBC%'");
mysqli_query($conn, "UPDATE packages SET price = 450 WHERE name LIKE '%Thyroid%' OR name LIKE '%TSH%'");
mysqli_query($conn, "UPDATE packages SET price = 600 WHERE name LIKE '%Lipid%'");
mysqli_query($conn, "UPDATE packages SET price = 150 WHERE name LIKE '%Sugar%'");
mysqli_query($conn, "UPDATE packages SET price = 500 WHERE price = 0 OR price IS NULL");

echo "Database updated with package prices successfully. You can delete this file now.";
?>
