<?php

function startAndEndTheSame(string $s0, string|int $s1):bool{

	if(is_null($s1))
		return false;

	if(is_int($s1))
		return false;

	return $s0[0] == $s1[0] &&
	       $s0[strlen($s0)-1] == $s1[strlen($s1)-1];
}

function testFunc(): int|null|string{
	return "test";
}