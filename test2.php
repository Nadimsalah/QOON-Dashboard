<?php
require 'connlog.php';
$res = mysqli_query($con, 'SHOW COLUMNS FROM Orders');
while($row = mysqli_fetch_assoc($res)) {
    echo $row['Field'] . "\n";
}
?>
