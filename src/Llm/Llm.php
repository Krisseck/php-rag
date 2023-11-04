<?php

namespace Krisseck\PhpRag\Llm;

use Yethee\Tiktoken\EncoderProvider;

class Llm {

    /**
     * @var string Prefix string to be added to all inputs
     */
    private $inputPrefix = "You are an AI assistant that answers questions in a friendly manner, based on the given #SOURCE# documents. Here are some rules you always follow:" . PHP_EOL .
    "- Generate human readable, clear output." .  PHP_EOL .
    "- Generate only the requested output." . PHP_EOL .
    "- Just answer user's input directly." . PHP_EOL .
    "- Use professional language." . PHP_EOL .
    "- Only include facts and information based on the #SOURCE# documents." .  PHP_EOL;

    /**
     * @param $prompt string User's prompt
     * @param $documents array Array of related documents
     * @return string
     */
    protected function prepareInput($prompt, $documents) {

        $provider = new EncoderProvider();
        $encoder = $provider->getForModel('gpt-4');

        // Need to remove few tokens for the "Instruct/Response" suffix string.
        $context_token_count = (integer)$_ENV['CONTEXT_TOKEN_COUNT'] - count($encoder->encode($prompt)) - 20;

        $input = $this->inputPrefix;

        $input .= PHP_EOL . "#SOURCE#" . PHP_EOL;

        foreach($documents as $document) {

            $input .= $document . PHP_EOL;

            $tokens = $encoder->encode($input);

            if (count($tokens) > $context_token_count) {
                $input = $encoder->decode(array_slice($tokens, 0, $context_token_count));
                break;
            }
        }

        $input .= PHP_EOL . PHP_EOL . "### Instruction: " . PHP_EOL . $prompt . PHP_EOL . "### Response: " . PHP_EOL;

        return $input;

    }

}