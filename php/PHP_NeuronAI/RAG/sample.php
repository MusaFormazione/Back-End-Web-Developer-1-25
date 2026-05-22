<?php

use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\Providers\AIProviderInterface;
use NeuronAI\Providers\Anthropic\Anthropic;
use NeuronAI\RAG\DataLoader\FileDataLoader;
use NeuronAI\RAG\Embeddings\EmbeddingsProviderInterface;
use NeuronAI\RAG\Embeddings\OllamaEmbeddingsProvider;
use NeuronAI\RAG\RAG;
use NeuronAI\RAG\VectorStore\FileVectorStore;
use NeuronAI\RAG\VectorStore\VectorStoreInterface;

require 'vendor/autoload.php';


class MyRagAgent extends RAG
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

	protected function embeddings(): EmbeddingsProviderInterface
	{
		return new OllamaEmbeddingsProvider(
			model: 'mxbai-embed-large'
		);
	}

	protected function vectorStore(): VectorStoreInterface
	{
		return new FileVectorStore(__DIR__.'/storage/');
	}
}


$agent = MyRagAgent::make();

$agent->addDocuments(FileDataLoader::for(__DIR__.'/docs/me.txt')->getDocuments());
$agent->addDocuments(FileDataLoader::for(__DIR__.'/docs/jd.txt')->getDocuments());

echo "Welcome to RAG agent. Ask for queries! Type 'exit' to quit." . PHP_EOL;
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