<?php

$color = array('white', 'green', 'red', 'blue', 'black');

$result1 = "";
$result2 = "";

foreach($color as $value){
	$result1 .= $value . "<br />";
}

foreach ($color as $key => $value){
	if($key > 0){
		$result2 .= ",";
	}

	$result2 .= $value;
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
<h1>In colonna</h1>
<?=$result1?>
<h1>In riga</h1>
<?=$result2?>
</body>
</html>
