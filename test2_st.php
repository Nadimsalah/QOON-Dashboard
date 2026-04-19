<?php
require "conn.php";
$res=$con->query("DESCRIBE BoostsByShop");
while($row=$res->fetch_assoc()) {
    print_r($row);
}
?>
