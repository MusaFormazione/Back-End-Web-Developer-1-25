<?php

class City {
	public string $name;

	public function __construct(string $name){
		$this->name = $name;
	}
}

$city0 = new City("MILANO");
$city1 = clone $city0;
$city1->name = "PAVIA";

echo $city0->name . " == " . $city1->name;

echo PHP_EOL;

$val0 = [ 5 ];
$val1 = $val0;
$val1[0] = 6;

echo $val0[0] . " == " . $val1[0];

class Driver{
	public string $name;
}

class RacingCar{
	public Driver $driver;

	public string $name;

	public function __construct(string $name, string $carName){
		$this->driver = new Driver();
		$this->driver->name = $name;
		$this->name = $carName;
	}

	public function __clone(){
		$this->driver =  clone $this->driver;
	}
}

$racingCar0 = new RacingCar("Mario Rossi", "xxx");
$racingCar1 = clone $racingCar0;
$racingCar1->name = "yyy";
$racingCar1->driver->name = "MAREMMA";
echo PHP_EOL;
echo $racingCar0->name . "|" . $racingCar0->driver->name;
echo PHP_EOL;
echo $racingCar1->name . "|" . $racingCar1->driver->name;
