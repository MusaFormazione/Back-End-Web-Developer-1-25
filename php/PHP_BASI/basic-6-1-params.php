<?php


# Crea una funzione che riceve un array di numeri (l'array potrebbe essere gestito come params)
# Restituire un altro Array in cui tutti i valori sono elevati al quadrato.

function squareCollection( int $a, int $b, int $c ): array {

	$args = func_get_args();

	foreach ( $args as $key => $value ) {

		if(!is_int($value))
			continue;

		$args[ $key ] = $value * $value;
	}

	return $args;
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

$result = squareCollection( 2, 4, 20, 6, 7, "test" );

echo convertArrayValuesToString( $result );
