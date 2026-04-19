<?php
$con = mysqli_connect('145.223.33.118', 'qoon_Qoondb', 'rP1?x#0jD8h]', 'qoon_Qoondb');
$res = mysqli_query($con, 'DESCRIBE Shops');
while($row = mysqli_fetch_assoc($res)) { echo $row['Field'] . " | "; }
echo "\n";
