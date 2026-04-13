<?php
$dbhost = "145.223.33.118";
$dbuser = "qoon_Qoon";
$dbpass = ";)xo6b(RE}K%";
$dbname = "qoon_Qoon";
$con = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

$res = $con->query("SHOW TABLES");
while($row = $res->fetch_row()){
    if(stripos($row[0], 'Product') !== false || stripos($row[0], 'Item') !== false || stripos($row[0], 'Wallet') !== false){
        echo $row[0] . "\n";
    }
}
?>
