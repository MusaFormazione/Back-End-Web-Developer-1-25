<?php


$test = new class(){
	private $balance = 0;

	public function addBalance(float $amount){
		$this->balance += $amount;
	}

	public function getBalance(){
		return $this->balance;
	}
};

$test->addBalance(100);
$test->addBalance(10);
echo $test->getBalance();