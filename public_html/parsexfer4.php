<?php
$mydata = simplexml_load_file('xfer4.xml');
$str = $mydata->mixed;
$array = str_getcsv($str);
$n = sizeof($array);
for ($i = 0; $i < $n; $i++){
    print $array[$i]."<br/>";
}
