<?php

// Array Numerico
$array_numerico = array("Antonio", "Gino", "Pino");
$array_numerico['test'] = "Joe";



// Array associativi
$array_associativo = array(
	'test' => 124,
	"test1" => "prova"
);
$array_associativo[] = "test";
//var_dump($array_associativo);
//exit;
// Array multidimensionale

$students0 = array(
	0 => array("Antonio", 44, "Developer"),
	1 => array("Mario", 30, "Cuoco"),
	2 => array("Sabrina", 20, "Designer")
);

$students1 = array(
	array("Name" => "Antonio", "Age" => 44, "Job" => "Developer"),
	array("Name" => "Mario",  "Age" => 30, "Job" =>"Cuoco"),
	array("Name" => "Sabrina", "Age" => 20, "Job" => "Designer")
);

$arr0 = array("test" => "Fiat");
$arr1 = array("test" => "Volvo");
$arr2 = $arr0 + $arr1;

//var_dump($arr2);


//
$y = 10;
$x = ($y >= 10 && $y < 20) ? 5 : null; // 5

// se $y > 10  && $y < 20
// allora $x = 5
// altrimenti $x = null

$z = null;
$x = $z ?? 5;

// se $z is null
// allora $x = 5
// altrimenti $x = $z


//echo "HelloWorld";
print "HelloWorld";