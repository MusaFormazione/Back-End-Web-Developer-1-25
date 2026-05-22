<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FORM-LIVE</title>
	<style>
        body { font-family: Arial, serif; margin: 20px; }
        input { padding: 8px; margin: 5px 0; }
        button { padding: 10px 20px; background-color: #007bff; color: white; border: none; cursor: pointer; }
        .risultato { background-color: #e7f3ff; padding: 15px; margin-top: 20px; border-left: 4px solid #007bff; }
	</style>
</head>
<body>

<?php
echo "<br><br>";
echo "REQUEST METHOD prop  =====================================<br>";
var_dump($_SERVER['REQUEST_METHOD']);
echo "<br><br>";
echo "GET prop =====================================<br>";
var_dump($_GET);
echo "<br><br>";
echo "POST prop =====================================<br>";
var_dump($_POST);


?>
</body>
</html>