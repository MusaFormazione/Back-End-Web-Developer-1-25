<?php

# print date in different formats

function printDateFormats():array{

	$currentDate = time();

	return [
		date("Y/m/d", $currentDate),
		date("y.m.d", $currentDate),
		date("d-m-y", $currentDate),
	];
}

var_dump(printDateFormats());