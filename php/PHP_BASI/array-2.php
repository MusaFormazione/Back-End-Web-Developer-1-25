<?php

$test = [0, 1, 2];

// cambiare
$test[0] = "prova";
// riferire
echo $test[0];
// eliminarlo
unset($test[0]);

// $test = [1, 2];
// $test = [,1,2]
// $test = ["prova",1,2]



//$test = ["prova", 1, 2];

$test[] = "prova";

//$test = [0, 1, 2, "prova"];
