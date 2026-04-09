<?php
$con = new mysqli("145.223.33.118", "qoon_Qoon", ";)xo6b(RE}K%", "qoon_Qoon");
$res = mysqli_query($con, "SHOW COLUMNS FROM BoostsByShop");
while ($row = mysqli_fetch_assoc($res)) {
    echo $row['Field'] . "\n";
}
?>
