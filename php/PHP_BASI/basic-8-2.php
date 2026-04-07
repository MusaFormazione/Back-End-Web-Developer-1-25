<?php

# Funzione che calcola i giorni che mancano al compleanno

$birthDay = "28/03/1981";
$birthDay = "1981-03-29";
$birthDay = "1981-02-28";
$invalidDate = "Maremma";
$zeroBirthDate = "1970-01-01"; // 0

/**
 * @throws Exception
 */
function birthDayCalculator(string $birthDate): int{

	$birthDateTimeStamp = strtotime($birthDate);

	if( $birthDateTimeStamp === false )
		throw new Exception("Invalid date passed: $birthDate");

	$secondsInaDay = 86400;

	$currentTimeStamp = time();
	$currentTimeStampProperties = getDate($currentTimeStamp);
	$currentYear = $currentTimeStampProperties['year'];
	$currentTimeStamp = mktime(0, 0, 0, $currentTimeStampProperties['mon'], $currentTimeStampProperties['mday'], $currentYear);

	$birthDateTimeProperties = getDate($birthDateTimeStamp);
	$birthDayTimeStamp = mktime(0, 0, 0, $birthDateTimeProperties['mon'], $birthDateTimeProperties['mday'], $currentYear);

	return abs(
		round(($birthDayTimeStamp - $currentTimeStamp) / $secondsInaDay)
	);
}

echo birthDayCalculator($zeroBirthDate);