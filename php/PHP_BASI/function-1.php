<?php


function doThis(){
	echo "Done";
}


function doThat($input){
	echo "Done";
}

doThis();

doThat("test");


// byValue
// byReference

$test = 5;
$prova = $test; // COPIA IL VALORE -- PER I TEMPI
$prova = &$test; //
$prova = 6; // $test = 6



function doThatByValue($p1){
	// VENGONO COPIATI.. IL LORO VALORE
	$p1 = 5;
}

function doThatByReference(&$p1){
	// VIENE COPIATO IL RIFERIMENTO
	$p1 = 5;
}

$x = 0;
doThatByReference($x);
doThatByValue($x);
