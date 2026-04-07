<?php

class Person {

	private static string $type = "Human";

	private string $name;

	public function __construct(string $name = "John", int $age = 0){
		$this->setName($name);
	}

	public function getName(): string{
		return $this->name;
	}

	public function getType(): string{
		return self::$type;
	}

	public static function getTypeStatically():string{
		return self::$type;
	}

	private function setName(string $name):void{
		$this->name = $name;
	}
}

$x = new Person();
$y = new Person("Klaus", 35);

echo $x->getName() . PHP_EOL; // "John"...
echo $y->getName() . PHP_EOL; // "Klaus"...
echo $y->getType() . " == " . $x->getType() . PHP_EOL;
echo Person::getTypeStatically();