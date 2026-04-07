<?php

$addresses = [
	'via tal dei tali 1, 20100 Milano (MI) Italia',
	[ 'via tal dei tali 2', 'Genova', 'GE', '10000', 'Italia' ],
	2,
	null,
	new stdClass(),
	[ 'Mario', 'Rossi' ]
];

function printAddress(array $addressesContainer):void{

	foreach($addressesContainer as $value){

		$processed = false;

		switch($value){
			case is_string($value);
				$processed = true;
				echo $value;
			break;
			case is_array($value) && count($value) > 2;
				$processed = true;
				echo implode(" ", $value);
			break;
			case is_numeric($value);
		    case is_object($value);
		    case is_null($value);
		    case is_callable($value);
		    case is_bool($value);
				echo "invalid";
			break;
		}

		if($processed){
			echo PHP_EOL;
		}
	}
}

printAddress($addresses);