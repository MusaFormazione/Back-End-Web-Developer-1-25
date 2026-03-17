<?php
// blocco1
$i = 1;
// blocco2
while($i <= 10){
	echo $i;
	// blocco3
	$i++;
}

for($i = 1; $i <= 10; $i++){
	continue;
	echo $i;

	break;
}

foreach ([0, 1, 2, 3] as $value){
	echo $value;//0 - 1
	echo $value;//1 - 2
	echo $value;//2 - 3
	echo $value;//3 - 4
}

$arrayAssoc = ["test1" => "prova", "test2"=> "prova", "test3"=>"prova"];
foreach($arrayAssoc as $key => $value){
	echo $key; // test1
	echo $value; // prova
}
