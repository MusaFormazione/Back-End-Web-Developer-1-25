<?php

$myArr = [1, 2, 13, 4, 5];
$result = [];

foreach($myArr as $value){
	$result[] = $value * -1;
}
?>
<pre>
	<?php
	print_r($result);
	?>
</pre>

<pre>
	<?php
	print_r($myArr);
	?>
</pre>
