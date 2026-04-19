<?php
 require "conn.php";

$updates = isset($_POST['updates']) ? $_POST['updates'] : null;

if (is_array($updates)) {
    foreach ($updates as $update) {
        if (!isset($update["CategoryId"]) || !isset($update["PercForOrder"])) continue;
        $CategoryId = (int)$update["CategoryId"];
        $PercForOrder = floatval($update["PercForOrder"]);
        $sql = "UPDATE Categories SET PercForOrder = '$PercForOrder' WHERE CategoryId = $CategoryId";
        mysqli_query($con, $sql);
    }
} else {
    // Fallback for legacy requests just in case
    $CategoryId   = isset($_POST["CategoryId"]) ? (int)$_POST["CategoryId"] : 0;
    $PercForOrder = isset($_POST["PercForOrder"]) ? floatval($_POST["PercForOrder"]) : 0;
    if ($CategoryId > 0) {
        $sql = "UPDATE Categories SET PercForOrder = '$PercForOrder' WHERE CategoryId = $CategoryId";
        mysqli_query($con, $sql);
    }
}

die;
mysqli_close($con);

?>

