<?php

$var1 = 'Antonio';
$var2 = 'Ciao';
$var3;

echo 'Ciao ' . $var1;
echo $var1 . ' ' . $var2;
echo "$var1 $var2";

var_dump($var3);

$varx = 0;
$varx++; //+1

//
$varx += 5; //+5
$varx -= 5; //-5
$varx *= 5; //*5
$varx /= 5; // /5



$string = "x";
$string = "x" . "y"; // xy
$string .= "z"; // xyz


$result = $string == $varx;
$result = $string != $varx;
$result = $string <> $varx;

//
$result = $string > $varx;
$result = $string < $varx;
$result = $string >= $varx;
$result = $string <= $varx;
