<?php

# stampare 1-2-3-4-5-6-7-8-9-10 con i loop

echo PHP_EOL . "==================================" . PHP_EOL;
echo "Esercizio 1" . PHP_EOL;

$result = "";

for($i = 1; $i <= 10; $i++){
	if($i > 1)
		$result .= "-";

	$result .= $i;
}

echo $result;

echo PHP_EOL . "==================================" . PHP_EOL;
echo "Esercizio 2" . PHP_EOL;

# sommare tutti i numeri tra 0 e 30

$result = 0;

for($i = 0; $i <= 30; $i++){
	$result += $i;
}

echo $result;


echo PHP_EOL . "==================================" . PHP_EOL;
echo "Esercizio 3" . PHP_EOL;

# costruire il pattern
//*
//* *
//* * *
//* * * *
//* * * * *

$result = "";

for($i = 1; $i <= 5; $i++){

	for($j = 1; $j <= $i; $j++){
		if($j > 1)
			$result .= " ";

		$result .= "*";
	}

	$result .= PHP_EOL;
}
echo $result;