<?php
$dbhost = "145.223.33.118";
$dbuser = "qoon_Qoon";
$dbpass = ";)xo6b(RE}K%";
$dbname = "qoon_Qoon";
$con = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

if(!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

$r1 = mysqli_query($con, "SELECT SliderPhoto FROM Sliders LIMIT 3");
if($r1) {
    while($row = mysqli_fetch_assoc($r1)) {
        echo 'Slider: ' . $row['SliderPhoto'] . "\n";
    }
} else {
    echo "Query Sliders failed: " . mysqli_error($con) . "\n";
}

$r2 = mysqli_query($con, "SELECT Photo FROM Categories LIMIT 3");
if($r2) {
    while($row = mysqli_fetch_assoc($r2)) {
        echo 'Cat: ' . $row['Photo'] . "\n";
    }
} else {
    echo "Query Cat failed: " . mysqli_error($con) . "\n";
}
?>
