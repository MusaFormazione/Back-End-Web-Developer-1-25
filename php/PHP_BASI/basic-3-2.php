<?php

$ceu = array( "Italy"          => "Rome",
              "Luxembourg"     => "Luxembourg",
              "Belgium"        => "Brussels",
              "Denmark"        => "Copenhagen",
              "Finland"        => "Helsinki",
              "France"         => "Paris",
              "Slovakia"       => "Bratislava",
              "Slovenia"       => "Ljubljana",
              "Germany"        => "Berlin",
              "Greece"         => "Athens",
              "Ireland"        => "Dublin",
              "Netherlands"    => "Amsterdam",
              "Portugal"       => "Lisbon",
              "Spain"          => "Madrid",
              "Sweden"         => "Stockholm",
              "United Kingdom" => "London",
              "Cyprus"         => "Nicosia",
              "Lithuania"      => "Vilnius",
              "Czech Republic" => "Prague",
              "Estonia"        => "Tallin",
              "Hungary"        => "Budapest",
              "Latvia"         => "Riga",
              "Malta"          => "Valetta",
              "Austria"        => "Vienna",
              "Poland"         => "Warsaw"
);

$result = "";
foreach($ceu as $country => $capital){
	$result .= "The capital of $country is $capital";
	$result .= "<br />";
}
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Document</title>
</head>
<body>
<?=$result?>
</body>
</html>
