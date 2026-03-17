<?php


$likes = [];
$likes = ["Mario"];
//$likes = ["Mario", "Giovanni"];
//$likes = ["Mario", "Giovanni", "Luca"];
//$likes = ["Mario", "Giovanni", "Luca","X"];
//$likes = ["Mario", "Giovanni", "Luca","X","rr", "sdd"];

switch(count($likes)){
	case 0:
		echo "no one like this";
		break;
	case 1:
		echo "$likes[0] likes this";
		break;
	case 2:
		echo "$likes[0] and $likes[1] like this";
		break;
	case 3:
		echo "$likes[0], $likes[1] and $likes[2] like this";
		break;
	default:
		echo "$likes[0], $likes[1] and " . count($likes) - 2 . " like this";
}

//if(count($likes) == 0){
//	echo "no one like this";
//}
//else if(count($likes) == 1){
//	echo "$likes[0] likes this";
//}
//else if(count($likes) == 2){
//	echo "$likes[0] and $likes[1] like this";
//}
//else if(count($likes) == 3){
//	echo "$likes[0], $likes[1] and $likes[2] like this";
//}
//else if(count($likes) > 3){
//	echo "$likes[0], $likes[1] and " . count($likes) - 2 . " like this";
//}