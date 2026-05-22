<!DOCTYPE html>
<html>
<head>
	<title>Personality</title>
</head>
<body>

<form method="GET" action="<?php echo $_SERVER['PHP_SELF'] ?>">
	Select your personality:
	<select name="attributes" multiple>
		<option value="perky">Perky</option>
		<option value="morose">Morose</option>
		<option value="thinking">Thinking</option>
		<option value="feeling">Feeling</option>
		<option value="thrifty">Thrifty</option>
		<option value="shopper">Shopper</option>
	</select>
	<input type="submit" name="s" value="Record my personality!">
</form>

<?php
if (array_key_exists( 's', $_GET)){

	var_dump($_GET);

}
?>

</body>
</html>
