<?php
require "conn.php";
$res = mysqli_query($con, "SHOW TABLES");
$out = "";
while($row = mysqli_fetch_array($res)) {
    $table = $row[0];
    $countRes = mysqli_query($con, "SELECT COUNT(*) FROM $table");
    $count = mysqli_fetch_array($countRes)[0];
    $out .= "$table ($count rows)\n";
}
file_put_contents("db_manifest.txt", $out);
echo "Manifest created.";
?>
