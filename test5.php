<?php
require 'connlog.php';

$res = mysqli_query($con, "SELECT * FROM Money LIMIT 1");
$row = mysqli_fetch_assoc($res);
print_r($row);

?>
