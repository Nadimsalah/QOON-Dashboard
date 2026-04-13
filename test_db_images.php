<?php
$dbhost = "145.223.33.118";
$dbuser = "qoon_Qoon";
$dbpass = ";)xo6b(RE}K%";
$dbname = "qoon_Qoon";
$con = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

if(!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "--- SLIDERS ---\n";
$r1 = mysqli_query($con, "SELECT SliderID, SliderPhoto FROM Sliders LIMIT 5");
while($row = mysqli_fetch_assoc($r1)) {
    echo $row['SliderID'] . ': ' . $row['SliderPhoto'] . "\n";
}

echo "\n--- CATEGORIES ---\n";
$r2 = mysqli_query($con, "SELECT CategoryId, Photo FROM Categories LIMIT 5");
while($row = mysqli_fetch_assoc($r2)) {
    echo $row['CategoryId'] . ': ' . $row['Photo'] . "\n";
}

echo "\n--- FOODS (PRODUCTS) ---\n";
$r3 = mysqli_query($con, "SELECT FoodID, FoodPhoto FROM Foods LIMIT 5");
while($row = mysqli_fetch_assoc($r3)) {
    echo $row['FoodID'] . ': ' . $row['FoodPhoto'] . "\n";
}
?>
