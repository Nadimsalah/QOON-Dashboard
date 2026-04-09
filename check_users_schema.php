<?php
require_once "conn.php";
$res = mysqli_query($con, "DESCRIBE Users");
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        echo $row['Field'] . " (" . $row['Type'] . ")\n";
    }
} else {
    echo "Error: " . mysqli_error($con);
}
?>
