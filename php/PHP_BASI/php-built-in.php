<?php

//function test(){
//	return "maremma";
//}

?>

<pre>
	<?php
	if(function_exists("test")){
		echo "test"();
	} else {
		"...";
	}
	?>
</pre>

<pre>
<?php
print_r(get_loaded_extensions());
?>
</pre>


