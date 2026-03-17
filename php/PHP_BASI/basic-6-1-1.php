<?php

function reverseWords(string $input):string{

	$separator = " ";

	$words = explode($separator, $input);

	foreach($words as $key => $word){
		$words[$key] = reverseSingleWord($word);
	}

	return implode($separator, $words);
}
function split(string $sentence, string $delimiter = " "):array{

	$result = [];
	$wordsCount = 0;
	$word = "";

	for($i=0; $i<strlen($sentence); $i++){
		if($sentence[$i] == $delimiter){
			$word = "";
			$wordsCount++;
		}

		$word .= $sentence[$i];
		$result[$wordsCount] = $word;
	}

	return $result;
}
function joinCollection(array $input, string $separator = " "):string{

	$result = "";

	foreach($input as $key => $word){
		if($key>0){
			$result .= $separator;
		}

		$result .= $word;
	}

	return $result;
}
function reverseSingleWord(string $input):string{
	$result = "";

	for($i=(strlen($input)-1); $i>=0; $i--){
		$result .= $input[$i];
	}

	return $result;
}

#echo reverseWords("ciao!"); #"!oaic";

//echo reverseWords("ciao Anto!"); #"oaic !ontA";
echo reverseWords("This is only an example!"); #"oaic !ontA";