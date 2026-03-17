<?php $i = 0 ?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name"viewport" content="width=device-width, initial-scale=1">
	<title>Html</title>
</head>
<body>
	<ul>
	<?php while($i <= 10):?>
		<?php if(true):?>
			<li><?=$i++?></li>
		<?php endif ?>
	<?php endwhile ?>
	</ul>
</body>
</html>
