<?php

abstract class Animale {

	private string $nome;

	function __construct( string $nome ) {
		$this->nome = $nome;
	}

	abstract protected function getTipo();

	public function presentati(){
		return "Mi chiamo ". $this->nome . ", e sono di tipo " . $this->getTipo();
	}

}

class Gatto extends Animale{
	protected function getTipo(){
		return "gatto";
	}
}

class Cane extends Animale{
	protected function getTipo(){
		return "cane";
	}
}

class Leone extends Animale{

	protected function getTipo() {
		return "Leone";
	}
}

$cane = new Cane("Pluto");
$gatto = new Gatto("Penny");
$leone = new Leone("Simba");

echo $cane->presentati() . PHP_EOL;
echo $leone->presentati() . PHP_EOL;
echo $gatto->presentati();