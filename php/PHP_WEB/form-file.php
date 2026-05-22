

<!DOCTYPE html>
<html>
<head>
	<title>File Upload</title>
</head>
<body>

<form action="<?php echo $_SERVER['PHP_SELF'] ?>"
      enctype="multipart/form-data"
      method="POST">

	<input type="hidden" name="MAX_FILE_SIZE" VALUE="2097152">

	File name: <input type="file" name="toProcess">

	<input type="submit" value="Upload">

</form>

<strong>Config:</strong><br>

<strong>Post:</strong> <br>
<?php
var_dump($_POST);
?>
<br><br>

<strong>Files:</strong> <br>
<?php
var_dump($_FILES);
?>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST'
    && array_key_exists( 'toProcess', $_FILES)){

	if (is_uploaded_file($_FILES['toProcess']['tmp_name'])) {
		echo "File caricato con successo";
		$fileName = "uploadedFile.txt";
		move_uploaded_file($_FILES['toProcess']['tmp_name'],
			"{$fileName}");
	} else {
		echo "File non caricato";
	}
}
?>


</body>
</html>
