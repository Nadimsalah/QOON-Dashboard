<?php
require "conn.php";
$res = mysqli_query($con, "SELECT table_name, column_name FROM information_schema.columns WHERE column_name LIKE '%FoodID%' AND table_schema = 'qoon_Qoon'");
if ($res) {
    while($row = mysqli_fetch_assoc($res)) {
        echo "Table: " . $row['table_name'] . " | Column: " . $row['column_name'] . "\n";
    }
}
?>
