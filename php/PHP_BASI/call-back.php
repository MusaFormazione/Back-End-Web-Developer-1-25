<?php


$callback = function(){
return 2 . "<br />";
};

function callbleTest(){
	return "callable-test" . PHP_EOL;
}

function executor(callable|string $callback){
	echo $callback();
}

executor($callback);

executor('callbleTest');

executor(function(){
	return "Test";
});
echo "<br>";

$c = "c";

executor2(function(string $a, string $b) use($c){
	return $a . " . " . $b . " . " . $c;
});

function fx(){
	return 0;
}


executor2(fn(string $a, string $b) => $a . " . " . $b . " . " . $c);

function executor2(callable|string $callback){
	echo $callback("a", "b");
}

