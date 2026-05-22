<?php

class User{
	public string $nome;
	public string $email;
	private int $number;

	function __construct( $nome, $email ) {
		$this->nome = $nome;
		$this->email = $email;
		$this->number = rand(0, 100000);
	}

	// wakeup
	function __unserialize(array $data):void{
		$this->number = $data['number'];
		$this->nome = $data['nome'];
		$this->email = $data['email'];
	}

	// sleep
	public function __serialize():array{

		return [
			"number" => $this->number,
			"nome" => $this->nome,
			"email" => $this->email
		];
	}
}