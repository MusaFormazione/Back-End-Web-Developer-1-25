<?php


# Crea una funzione che riceve un array di numeri (l'array potrebbe essere gestito come params)
# Restituire un altro Array in cui tutti i valori sono elevati al quadrato.

function squareCollection( mixed $a, mixed $b, int|float $c ): array {

	$args = func_get_args();

	foreach ( $args as $key => $value ) {

		if(!is_int($value))
			continue;

		$args[ $key ] = MySqrt($value);
	}

	return $args;
}

function MySqrt(int $p1){
	return $p1 * $p1;
}

function convertArrayValuesToString( array $collection ): string {

	$result = "";

	foreach ( $collection as $key => $value ) {
		if ( $key > 0 ) {
			$result .= ", ";
		}
		$result .= $value;
	}

	return "[ " . $result . " ]";
}

$result = squareCollection( 2, "6", 20.5, 6, 7, "2" );

echo convertArrayValuesToString( $result );
