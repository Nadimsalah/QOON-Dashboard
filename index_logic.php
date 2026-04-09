<?php
// Preserved from original index.php (Lines 340-600)
$res = mysqli_query($con, "SELECT CreatedAtOrders, OrderState FROM Orders");

$jun = date('Y-01-01');
$feb = date('Y-02-01');
$march = date('Y-04-01');
$April = date('Y-04-01');
$May = date('Y-05-01');
$Jun = date('Y-06-01');
$jul = date('Y-07-01');
$Aug = date('Y-08-01');
$Sep = date('Y-09-01');
$Oct = date('Y-10-01');
$NOv = date('Y-11-01');
$Dec = date('Y-12-01');

$junNum = $febNum = $MarchNum = $AprilNum = $MayNum = $JunNum = $JulyNum = $AugNum = $SepNum = $OctNum = $NovNum = $DecNum = 0;

$x = date("Y-m-d");
$pieces = explode("-", $x);
$list = array();
$month = $pieces[1];
$year = $pieces[0];

for ($d = 1; $d <= 31; $d++) {
    $time = mktime(12, 0, 0, $month, $d, $year);
    if (date('m', $time) == $month)
        $list[] = date('Y-m-d', $time);
}

$Day1 = $Day2 = $Day3 = $Day4 = $Day5 = $Day6 = $Day7 = $Day8 = $Day9 = $Day10 = 0;
$Day11 = $Day12 = $Day13 = $Day14 = $Day15 = $Day16 = $Day17 = $Day18 = $Day19 = $Day20 = 0;
$Day21 = $Day22 = $Day23 = $Day24 = $Day25 = $Day26 = $Day27 = $Day28 = $Day29 = $Day30 = $Day31 = 0;

$Days1 = $Days2 = $Days3 = $Days4 = $Days5 = $Days6 = $Days7 = $Days8 = $Days9 = $Days10 = 0;
$Days11 = $Days12 = $Days13 = $Days14 = $Days15 = $Days16 = $Days17 = $Days18 = $Days19 = $Days20 = 0;
$Days21 = $Days22 = $Days23 = $Days24 = $Days25 = $Days26 = $Days27 = $Days28 = $Days29 = $Days30 = $Days31 = 0;

$res_orders = mysqli_query($con, "SELECT CreatedAtOrders FROM Orders");
while ($row = mysqli_fetch_assoc($res_orders)) {
    $ca = $row["CreatedAtOrders"];
    foreach ($list as $i => $d) {
        if (strpos($ca, $d) !== false) {
            ${"Day" . ($i + 1)}++;
            break;
        }
    }

    // Year months
    if ($ca >= $jun && $ca < $feb)
        $junNum++;
    elseif ($ca >= $feb && $ca < $march)
        $febNum++;
    elseif ($ca >= $march && $ca < $April)
        $MarchNum++;
    elseif ($ca >= $April && $ca < $May)
        $AprilNum++;
    elseif ($ca >= $May && $ca < $Jun)
        $MayNum++;
    elseif ($ca >= $Jun && $ca < $jul)
        $JunNum++;
    elseif ($ca >= $jul && $ca < $Aug)
        $JulyNum++;
    elseif ($ca >= $Aug && $ca < $Sep)
        $AugNum++;
    elseif ($ca >= $Sep && $ca < $Oct)
        $SepNum++;
    elseif ($ca >= $Oct && $ca < $NOv)
        $OctNum++;
    elseif ($ca >= $NOv && $ca < $Dec)
        $NovNum++;
    elseif ($ca >= $Dec)
        $DecNum++;
}

// User open app logic (Traffic)
$res_traffic = mysqli_query($con, "SELECT CreatedAtUserOpenApp FROM UserOpenApp");
$junNum1 = $febNum1 = $MarchNum1 = $AprilNum1 = $MayNum1 = $JunNum1 = $JulyNum1 = $AugNum1 = $SepNum1 = $OctNum1 = $NovNum1 = $DecNum1 = 0;
while ($row = mysqli_fetch_assoc($res_traffic)) {
    $ca = $row["CreatedAtUserOpenApp"];
    foreach ($list as $i => $d) {
        if (strpos($ca, $d) !== false) {
            ${"Days" . ($i + 1)}++;
            break;
        }
    }

    if ($ca >= $jun && $ca < $feb)
        $junNum1++;
    elseif ($ca >= $feb && $ca < $march)
        $febNum1++;
    elseif ($ca >= $march && $ca < $April)
        $MarchNum1++;
    elseif ($ca >= $April && $ca < $May)
        $AprilNum1++;
    elseif ($ca >= $May && $ca < $Jun)
        $MayNum1++;
    elseif ($ca >= $Jun && $ca < $jul)
        $JunNum1++;
    elseif ($ca >= $jul && $ca < $Aug)
        $JulyNum1++;
    elseif ($ca >= $Aug && $ca < $Sep)
        $AugNum1++;
    elseif ($ca >= $Sep && $ca < $Oct)
        $SepNum1++;
    elseif ($ca >= $Oct && $ca < $NOv)
        $OctNum1++;
    elseif ($ca >= $NOv && $ca < $Dec)
        $NovNum1++;
    elseif ($ca >= $Dec)
        $DecNum1++;
}

// Hourly & Weekly logic simplified for template
$t = time();
$today = date("Y-m-d", $t);
$oneclock = $twoclock = $threeclock = $fourclock = $fiveclock = $sexclock = $sevenclock = $eightclock = $nineclock = $tenclock = $tenoneclock = $tentwoclock = $tenthreeclock = $tenfourclock = $tenfiveclock = $tensexclock = $tensevenclock = $teneightclock = $tennineclock = $tentenclock = $twentyoneclock = $twentytwoclock = $twentythreeclock = $twentyfourclock = 0;
$oneclock1 = $twoclock1 = $threeclock1 = $fourclock1 = $fiveclock1 = $sexclock1 = $sevenclock1 = $eightclock1 = $nineclock1 = $tenclock1 = $tenoneclock1 = $tentwoclock1 = $tenthreeclock1 = $tenfourclock1 = $tenfiveclock1 = $tensexclock1 = $tensevenclock1 = $teneightclock1 = $tennineclock1 = $tentenclock1 = $twentyoneclock1 = $twentytwoclock1 = $twentythreeclock1 = $twentyfourclock1 = 0;

$res_hourly = mysqli_query($con, "SELECT CreatedAtUserOpenApp FROM UserOpenApp WHERE CreatedAtUserOpenApp LIKE '$today%'");
while ($row = mysqli_fetch_assoc($res_hourly)) {
    $hour = (int) date('H', strtotime($row['CreatedAtUserOpenApp']));
    $names = ['one', 'two', 'three', 'four', 'five', 'sex', 'seven', 'eight', 'nine', 'ten', 'tenone', 'tentwo', 'tenthree', 'tenfour', 'tenfive', 'tensex', 'tenseven', 'teneight', 'tennine', 'tenten', 'twentyone', 'twentytwo', 'twentythree', 'twentyfour'];
    if (isset($names[$hour]))
        ${$names[$hour] . 'clock'}++;
}

$res_hourly_orders = mysqli_query($con, "SELECT CreatedAtOrders FROM Orders WHERE CreatedAtOrders LIKE '$today%'");
while ($row = mysqli_fetch_assoc($res_hourly_orders)) {
    $hour = (int) date('H', strtotime($row['CreatedAtOrders']));
    $names = ['one', 'two', 'three', 'four', 'five', 'sex', 'seven', 'eight', 'nine', 'ten', 'tenone', 'tentwo', 'tenthree', 'tenfour', 'tenfive', 'tensex', 'tenseven', 'teneight', 'tennine', 'tenten', 'twentyone', 'twentytwo', 'twentythree', 'twentyfour'];
    if (isset($names[$hour]))
        ${$names[$hour] . 'clock1'}++;
}

$Monday = date('Y-m-d', strtotime('this week Monday'));
$days_of_week = ['Mon', 'Tues', 'Wednes', 'Thurs', 'Fri', 'Satur', 'Sun'];
foreach ($days_of_week as $d) {
    ${$d . 'Order'} = ${$d} = ${$d . 'Rev'} = ${$d . 'Fees'} = 0;
}

$res_week = mysqli_query($con, "SELECT CreatedAtOrders FROM Orders WHERE CreatedAtOrders >= '$Monday'");
while ($row = mysqli_fetch_assoc($res_week)) {
    $day_name = date('D', strtotime($row['CreatedAtOrders']));
    $map = ['Mon' => 'Mon', 'Tue' => 'Tues', 'Wed' => 'Wednes', 'Thu' => 'Thurs', 'Fri' => 'Fri', 'Sat' => 'Satur', 'Sun' => 'Sun'];
    if (isset($map[$day_name]))
        ${$map[$day_name] . 'Order'}++;
}

$res_week_traffic = mysqli_query($con, "SELECT CreatedAtUserOpenApp FROM UserOpenApp WHERE CreatedAtUserOpenApp >= '$Monday'");
while ($row = mysqli_fetch_assoc($res_week_traffic)) {
    $day_name = date('D', strtotime($row['CreatedAtUserOpenApp']));
    $map = ['Mon' => 'Mon', 'Tue' => 'Tues', 'Wed' => 'Wednes', 'Thu' => 'Thurs', 'Fri' => 'Fri', 'Sat' => 'Satur', 'Sun' => 'Sun'];
    if (isset($map[$day_name]))
        ${$map[$day_name]}++;
}

// 4. Weekly Revenue aggregation
$res_rev = mysqli_query($con, "SELECT CreatedAtOrders, OrderPrice FROM Orders WHERE CreatedAtOrders >= '$Monday'");
if($res_rev) {
    while ($row = mysqli_fetch_assoc($res_rev)) {
        $day_name = date('D', strtotime($row['CreatedAtOrders']));
        $map = ['Mon' => 'Mon', 'Tue' => 'Tues', 'Wed' => 'Wednes', 'Thu' => 'Thurs', 'Fri' => 'Fri', 'Sat' => 'Satur', 'Sun' => 'Sun'];
        if (isset($map[$day_name])) {
            ${$map[$day_name] . 'Rev'} += (float)$row['OrderPrice'];
        }
    }
}

// 5. Weekly Fees aggregation
$res_fees = mysqli_query($con, "SELECT CreatedAtFeesTransaction, Money FROM FeesTransaction WHERE CreatedAtFeesTransaction >= '$Monday'");
if($res_fees) {
    while ($row = mysqli_fetch_assoc($res_fees)) {
        $day_name = date('D', strtotime($row['CreatedAtFeesTransaction']));
        $map = ['Mon' => 'Mon', 'Tue' => 'Tues', 'Wed' => 'Wednes', 'Thu' => 'Thurs', 'Fri' => 'Fri', 'Sat' => 'Satur', 'Sun' => 'Sun'];
        if (isset($map[$day_name])) {
            ${$map[$day_name] . 'Fees'} += (float)$row['Money'];
        }
    }
}
?>