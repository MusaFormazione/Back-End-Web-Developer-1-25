<?php

class BankAccount {

	private float $balance;
	private string $owner;

	public function __construct(string $owner){
		$this->owner = $owner;
		$this->balance = 0;
	}

	public function deposit(float $amount):void{
		$this->balance += $amount;
	}

	public function withdraw(float $amount):void{
		if ( $amount > $this->balance ) {
			echo "Fondi insufficienti\n";

			return;
		}
		$this->balance -= $amount;
	}

	public function getBalance():float{
		return $this->balance;
	}

	public function getOwner():string{
		return $this->owner;
	}

}


// Uso del sistema
$bankAccount = new BankAccount("Mario Rossi");
$bankAccount->deposit(1000 );
$bankAccount->withdraw(200 );
$owner = $bankAccount->getOwner();
$balance = $bankAccount->getBalance();

echo "Saldo attuale per $owner: $balance €\n";
