<?php

$t = date("H");

// se ORA PRIMA DELLE 10
// good morning
// altrimenti se PRIMA DELLE 20
// have a good day
// altrimenti
// have a good night

$t = 5;
echo "$t - ";
if ($t < 10):
	echo "Good morning";
elseif ($t < 20):
	echo "Good day";
endif;
//else{
//	echo "Good night";
//}