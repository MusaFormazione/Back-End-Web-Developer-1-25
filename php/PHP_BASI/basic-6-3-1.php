<?php


/**
 * @param int|float $leftOperand
 * @param int|float $rightOperand
 * @param callable<int|float,int|float,int|float> $operation
 *
 * @return int|float
 */
function calculator(int|float $leftOperand, int|float $rightOperand, callable $operation):int|float{
	return $operation($leftOperand, $rightOperand);
}

$sum = fn(int|float $leftOperand, int|float $rightOperand) => $leftOperand + $rightOperand;
$dif = fn(int|float $leftOperand, int|float $rightOperand) => $leftOperand - $rightOperand;
$mul = fn(int|float $leftOperand, int|float $rightOperand) => $leftOperand * $rightOperand;
$div = fn(int|float $leftOperand, int|float $rightOperand) => $leftOperand * $rightOperand;

echo calculator(5, 5, $mul);