<?php

# Crea una funzione che riceve un array di numeri (l'array potrebbe essere gestito come params)
# Restituire un altro Array in cui tutti i valori sono elevati al quadrato.

function squareCollection(array $collection, callable $squareFunc): array{

	foreach($collection as $key => $value){
		$collection[$key] = $squareFunc($value);
	}

	return $collection;
}
function convertArrayValuesToString(array $collection): string{

	$result = "";

	foreach($collection as $key => $value){
		if($key > 0){
			$result .= ", ";
		}
		$result .= $value;
	}

	return "[ " . $result . " ]";
}

$result = squareCollection([2, 4, 20], fn($x) => $x * $x);

echo  convertArrayValuesToString($result);
