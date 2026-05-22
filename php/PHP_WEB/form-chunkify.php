<!DOCTYPE html>
<html>
<head>
	<title>Chunkify Form</title>
</head>
<body>

<strong>Metodo:</strong><br>
<?php
var_dump($_SERVER['REQUEST_METHOD']);
?>

<strong>POST:</strong><br>
<?php
var_dump($_POST);
?>

<?php

if ( $_SERVER['REQUEST_METHOD'] == 'POST' && array_key_exists( 'chunkify', $_POST ) ) {

	$word = $_POST['word'];
	$number = $_POST['number'];

	$chunks = ceil( strlen($word) / $number );

	echo "The {$number} - letter chunks of '{$word}' are: <br />";

	for ( $i = 0; $i < $chunks; $i++ ) {
		$chunk = substr( $word, $i * $number, $number );
		printf( "%d: %s <br />", $i + 1, $chunk );
	}

	echo "<br />";
}
?>


<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
	<input type="hidden" name="chunkify">
	Enter a word: <input type="text" name="word" required><br />
	How long should the chunks be?<br />
	<input type="text" name="number" required><br />
	<input type="submit" value="chunkify">
</form>

</body>
</html>
