<?php

     $dbhost = "145.223.33.118";
    $dbuser = "qoon_Qoon";
    $dbpass = ";)xo6b(RE}K%";
    $dbname = "qoon_Qoon";
    try {
    $con = new mysqli($dbhost, $dbuser, $dbpass,$dbname) ;
    if ($con -> connect_error) {
      echo "Failed to connect to MySQL: " . $con -> connect_error;
      exit();
    }
    } catch(Exception $e) {
      echo "Connection failed: " . $e->getMessage();
    }

  $DomainNamee ="https://qoon.app/dash/";
  
?>
