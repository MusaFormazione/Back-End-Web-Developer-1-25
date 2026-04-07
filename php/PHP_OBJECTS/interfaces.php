<?php

interface EngineInterface {
	public function start(): void;
	public function end(): void;
}
abstract class TermEngine implements EngineInterface {

	public function start(): void {
		// do something magic
	}

	public function end(): void {
		// do something magic
	}
}
abstract class ElectricEngine implements EngineInterface {

	public function start(): void {
		// TODO: Implement start() method.
	}

	public function end(): void {
		// TODO: Implement end() method.
	}
}


class BmwElectric extends ElectricEngine {

}

final class RenaultTermEngine extends TermEngine {

}

class RenaultSpecialTermEngine extends TermEngine{

	private TermEngine $internalTermEngine;

	private function switchOnLights(){}


	public function __construct(RenaultTermEngine $renaultTermEngine){
		$this->internalTermEngine = $renaultTermEngine;
	}

	public function start():void{
		$this->internalTermEngine->start();
		$this->switchOnLights();
	}

}

class TeslaEngine extends ElectricEngine{

	private function switchOnLights(){}

	public function start():void{
		parent::start();
		$this->switchOnLights();
	}

}


abstract class Car {

	private EngineInterface $engine;

	// Dipendenza
	public function __construct(EngineInterface $engine){
		$this->engine = $engine;
	}

	public function startEngine(){
		$this->engine->start();
	}
	public function endEngine(){
		$this->engine->end();
	}

}

class Twingo extends Car {
	public function __construct(){
		$engine = new RenaultTermEngine();
		parent::__construct($engine);
	}
}

$engine = new TeslaEngine();

$car = new Twingo();
$car->startEngine();
//echo Engine::ATOM;
echo date(DateTimeInterface::ATOM);


trait CarTrait{

	public function something(){

	}

}

trait EngineTrait {

	public function somethingElse(){

	}

}

class SpecialCase {
	use CarTrait;
	use EngineTrait;
}

$x = new SpecialCase();
$x->something();
$x->somethingElse();

// S.O.L.I.D.