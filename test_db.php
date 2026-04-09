<?php
include "connlog.php";
if ($con) {
    echo "Connected successfully to " . $dbname;
    $res = mysqli_query($con, "SELECT 1");
    if ($res) {
        echo " - Query executed successfully";
    } else {
        echo " - Query failed: " . mysqli_error($con);
    }
} else {
    echo "Connection failed";
}
?>
