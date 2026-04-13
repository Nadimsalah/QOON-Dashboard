<?php
require "conn.php";
$res = mysqli_query($con, "SHOW TABLES");
echo "TABLES IN DB:\n";
while($row = mysqli_fetch_array($res)) {
    echo $row[0] . "\n";
}
?>
