<?php

require 'vendor/autoload.php';

use NeuronAI\Agent\Agent;
use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\Providers\AIProviderInterface;
use NeuronAI\Providers\Anthropic\Anthropic;
use NeuronAI\Tools\Toolkits\MySQL\MySQLSchemaTool;
use NeuronAI\Tools\Toolkits\MySQL\MySQLSelectTool;

class SakilaAgent extends Agent
{
	protected function provider(): AIProviderInterface{

		// Fornire i dettagli per collegarsi ad un LLM

		$iniFile = dirname(__FILE__, 3) . DIRECTORY_SEPARATOR . "settings.ini";

		if(!file_exists($iniFile)){
			throw new Exception("Settings file not found: $iniFile");
		}

		$settings = parse_ini_file($iniFile);

		return (new Anthropic(
			key: $settings['key'],
			model: $settings['model'],
			parameters: [], // Add custom params (temperature, logprobs, etc)
		))->systemPromptBlocks([
			['type' => 'text', 'text' => "You are a helpful assistant.", 'cache_control' => ['type' => 'ephemeral']]
		]);
	}

	protected function tools(): array{

		$dsn = "mysql:host=localhost;port=3307;dbname=sakila;charset=utf8mb4";
		$user = "root";
		$pass = ""; // password

		return [
			// Connect to a MySQL database
			MySQLSchemaTool::make(
				new \PDO($dsn, $user, $pass),
			),
			MySQLSelectTool::make(
				new \PDO($dsn, $user, $pass),
			)
		];
	}
}

$agent = SakilaAgent::make();

echo "Welcome to sakila agent. Ask for queries! Type 'exit' to quit." . PHP_EOL;
writeUser();

while(FALSE !== ($input = fgets(STDIN))){

	if(trim($input) === "exit"){
		break;
	}

	$message = $agent->chat(new UserMessage($input))->getMessage();
	writeAssistantMessage($message);
	writeUser();
};


function writeAssistantMessage($message){
	echo "Assistant: " . $message->getContent() . PHP_EOL . PHP_EOL;
}

function writeUser(){
	echo "You: ";
}