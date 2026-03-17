<?php

# Esercizio 1
# Trovare il maggiore tra 3 numeri;

$a = 54;
$b = 54;
$c = 54;


echo "==================================" . PHP_EOL;
echo "Esercizio 1" . PHP_EOL;

echo 'Il maggiore tra le 3 variabili è: ';

if ($a > $b && $a > $c){
	echo '$a con ' . $a;
} else if($c > $b) {
	echo '$c con ' . $c;
} else if($a != $b){
	echo '$b con ' . $b;
} else {
	echo "nessuna perchè sono tutti uguali...";
}


echo PHP_EOL . "==================================" . PHP_EOL;
echo "Esercizio 2" . PHP_EOL;

# Esercizio 2
# Trovare numeri pari o dispari

$test = 400;

if($test%2 == 0){
	echo "Il numero $test è pari";
} else {
	echo "Il numero $test è dispari";
}