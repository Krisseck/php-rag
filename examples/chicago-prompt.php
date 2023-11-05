<?php

require('../vendor/autoload.php');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Load backend & LLM

$backend = new Krisseck\PhpRag\Backend\SolrBackend($_ENV['SOLR_HOST'], $_ENV['SOLR_PORT'], $_ENV['SOLR_CORE']);
#$backend = new Krisseck\PhpRag\Backend\ElasticBackend($_ENV['ELASTIC_HOST'], $_ENV['ELASTIC_API_KEY'], $_ENV['ELASTIC_INDEX']);

$llm = new Krisseck\PhpRag\Llm\ReplicateLlm($_ENV['REPLICATE_API_KEY'], $_ENV['REPLICATE_MODEL_VERSION']);
#$llm = new  Krisseck\PhpRag\Llm\KoboldAiHordeLlm($_ENV['KOBOLDAI_HORDE_API_KEY'], []);
#$llm = new Krisseck\PhpRag\Llm\OpenAiLlm($_ENV['OPENAI_API_KEY'], $_ENV['OPENAI_MODEL']);

// User prompt, can be changed
$prompt = "What is Chicago's name based on?";

// Clear all data in index
$backend->clearIndex();

// Input example data

$files = glob("./data/*.txt");

foreach($files as $file) {

    $backend->indexContent(crc32($file), file_get_contents($file));

}

// Run the query

$documents = $backend->search($prompt);

if($response = $llm->query($prompt, $documents)) {

    echo 'GOT RESPONSE' . PHP_EOL;
    echo trim($response) . PHP_EOL;

} else {

    echo 'COULDN\'T GET RESPONSE' . PHP_EOL;

}