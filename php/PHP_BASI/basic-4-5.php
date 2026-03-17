<?php

include "functions.php";

$s0 = "Hello";
$s1 = "Haoo";

$p1 = $s0[0] == $s1[0];

if(startAndEndTheSame($s0, $s1)){
	echo "OK/true";
} else {
	echo "KO/false";
}
startAndEndTheSame('d', 'd');

startAndEndTheSame("ddkd", 212);

echo testFunc();