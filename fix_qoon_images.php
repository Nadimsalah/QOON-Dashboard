<?php
require "conn.php";

echo "<h1>Database Image Domain Migration (Jibler -> Qoon)</h1>";

function fix_table($con, $table, $column, $id_column) {
    echo "<h3>Updating table: $table</h3>";
    $query = "SELECT $id_column, $column FROM $table WHERE $column LIKE '%jibler.app%' OR $column LIKE '%jibler.ma%' OR $column LIKE '%db/db/photo/%' OR $column LIKE '%qoon.app/dash/%'";
    $result = mysqli_query($con, $query);

    if (!$result) {
        echo "<p style='color:red;'>Error querying $table: " . mysqli_error($con) . "</p>";
        return;
    }

    $count = 0;
    while ($row = mysqli_fetch_assoc($result)) {
        $id = $row[$id_column];
        $old_path = $row[$column];

        // Replace the domains and db/db to correct root domain qoon.app/
        $new_path = str_replace(
            ['https://jibler.app/db/db/', 'https://jibler.ma/db/db/', 'http://jibler.app/db/db/', 'https://dashboard.jibler.ma/dash/', 'https://jibler.app/dash/', 'https://jibler.ma/dash/', 'https://qoon.app/dash/'],
            'https://qoon.app/',
            $old_path
        );

        if ($new_path != $old_path) {
            $update = "UPDATE $table SET $column = '" . mysqli_real_escape_string($con, $new_path) . "' WHERE $id_column = '$id'";
            if (mysqli_query($con, $update)) {
                $count++;
            } else {
                echo "<p style='color:red;'>Failed to update ID $id in $table: " . mysqli_error($con) . "</p>";
            }
        }
    }
    echo "<p style='color:green;'>Updated $count records in $table!</p>";
}

// Fix Categories
fix_table($con, "Categories", "Photo", "CategoryId");

// Fix Sliders
fix_table($con, "Sliders", "SliderPhoto", "SliderID");

// Fix Partners Sliders
fix_table($con, "SliderPartner", "SliderPhoto", "SliderPartnerID");

// Fix Foods / Products
fix_table($con, "Foods", "FoodPhoto", "FoodID");

// Fix Shops
fix_table($con, "Shops", "ShopLogo", "ShopID");
fix_table($con, "Shops", "ShopCover", "ShopID");

echo "<h2>Migration Complete! Please check your mobile app or dashboard.</h2>";
?>
