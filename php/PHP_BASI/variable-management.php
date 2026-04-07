<?php
//
//$var = 5; // 5
//$var = 6; // 6
//$var1 = $var;
//
////unset($var1);
//$var1 = "";
////$var1 = 0;
////$var1 = false;
////$var1 = null; // ISSET FALSE | EMPTY TRUE
//unset($var1);
//
//if(isset($var1)){
//	echo "ISSET TRUE";
//} else {
//	echo "ISSET FALSE";
//}
//echo PHP_EOL;
//
////
//if(empty($var1)){
//	echo "EMPTY TRUE";
//} else {
//	echo "EMPTY FALSE";
//}
//echo PHP_EOL;
//if(!isset($var1) || is_null($var1)){
//	echo "IS NULL TRUE";
//} else {
//	echo "IS NULL FALSE";
//}
//// $v contiene tutti i dati f(value[n])
//
//echo PHP_EOL;
//function xxx($p1){
//	echo $p1;
//}
//$callable = function(){
//
//};
//
//if(is_callable($callable)){
//	echo "IS CALLABLE TRUE";
//} else{
//	echo "IS CALLABLE FALSE";
//}
//
//call_user_func("xxx", "Hello");
//"xxx"("Test");
//
//$TEST = function(){
//	for($i=0; $i<10; $i++){
//		yield "iterable $i";
//	}
//};
//
//$TEST1 = function(){
//	return "test";
//};
//
//$TEST2 = function(){
//	return ["test1", "test2"];
//};
//
//
//echo PHP_EOL . "ITERABLE" .is_iterable($TEST());
//
//echo PHP_EOL;
//
//$iterable = $TEST();
//
//foreach($iterable as $value){
//	echo $value . PHP_EOL;
//}
//
//$test = "EUR29.99";
//var_dump(floatVal($test));
//var_dump(gettype(floatVal($test)));
//
//var_dump(settype($test, "float"));
//var_dump($test);
//
//
//$x = "cc";
//$t = (bool) $x;
//var_dump($t);

//$numero = 5;
//$array = (array) $numero;
//var_dump($array); // Output: array(1) { [0]=> int(5) }
//$object  = (object) $array;
//var_dump($object);
////
//$oggetto = new stdClass();
//$oggetto->nome = "Marco";
//$array_from_object = (array) $oggetto;
//var_dump($array_from_object); // Output: array(1) { ["nome"]=> string("Marco") }
//

$a = "10 banane";
$b = 5;
var_dump($a . $b);  // int(15) - converte "10 banane" → 10

// Booleani in numeri
$c = true;
$d = false;
var_dump($c + 1);   // int(2)
var_dump($d + 1);   // int(1)

// String "0" è falsy
if ("0") {
	echo "Vero";  // NON si esegue
}

var_dump(0 === "0");              // bool(true)
var_dump(0 === "");               // bool(true)

$result = "5 mele" + "3 pere";
var_dump($result);  // int(8) - converte entrambi