<?php

require 'vendor/autoload.php';

use NeuronAI\Agent\Agent;
use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\Providers\AIProviderInterface;
use NeuronAI\Providers\Anthropic\Anthropic;

class MyAgent extends Agent {

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
}

$agent = MyAgent::make();

echo "Welcome to the chatbot! Type 'exit' to quit." . PHP_EOL;
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

//
//while(true){
//	$input = fgets($stdin);
//	$message = MyAgent::make()->chat(new UserMessage($input))->getMessage();
//
//	fwrite($stdout, "\n");
//
//	fwrite($stdout, "You");
//
//	if($input === "exit"){
//		break;
//	}
//}
//
//fclose($stdout);
//fclose($stdin);