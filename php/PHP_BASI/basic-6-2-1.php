<?php


# Funzione che riceve un intero.. e ritorna il NUMERO dei divisori....

function getDividersNumber(int $input): int{

	$result = 0;

	for($i = 1; $i <= $input; $i++){

		$remain = $input%$i;

		if($remain == 0){
			$result++;
		}
	}

	return $result;
}

//echo getDividersNumber(4); // 3 [1, 2, 4]
echo getDividersNumber(30);