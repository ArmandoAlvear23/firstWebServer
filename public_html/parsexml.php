<?php
$mydata = simplexml_load_file('xfer1.xml');
Echo $mydata->insert->person->pname." ";
Echo $mydata->insert->person->street." ";
Echo $mydata->insert->person->city." ";
?>
