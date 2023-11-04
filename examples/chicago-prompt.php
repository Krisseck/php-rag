<?php

use Krisseck\PhpRag\Backend\SolrBackend;
use Krisseck\PhpRag\Llm\KoboldAiHordeLlm;
use Krisseck\PhpRag\Llm\ReplicateLlm;

require('../vendor/autoload.php');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Load backend & LLM

$backend = new SolrBackend($_ENV['SOLR_HOST'], $_ENV['SOLR_PORT'], $_ENV['SOLR_CORE']);

$llm = new ReplicateLlm($_ENV['REPLICATE_API_KEY'], $_ENV['REPLICATE_MODEL_VERSION']);
//$llm = new KoboldAiHordeLlm($_ENV['KOBOLDAI_HORDE_API_KEY'], []);

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
    echo $response . PHP_EOL;

    var_dump($response);

} else {

    echo 'COULDN\'T GET RESPONSE' . PHP_EOL;

}