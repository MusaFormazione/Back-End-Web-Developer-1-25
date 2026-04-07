<?php

var_dump(strtotime("30-6-2008"));

var_dump(strtotime("totto")); //universal time

$var = strtotime("2008-06-30 23:59:00");

var_dump(date("Y-m-d H:i:s", $_SERVER['REQUEST_TIME']));

echo mktime(0, 0,0, 1, 1, 1900);

var_dump(getdate());