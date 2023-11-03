<?php

namespace Krisseck\PhpRag\Llm;

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

        // Need to remove few words for the "Instruct/Response" suffix string.
        $context_word_count = (integer)$_ENV['CONTEXT_WORD_COUNT'] - str_word_count($prompt) - 10;

        $input = $this->inputPrefix;

        $input .= PHP_EOL . "#SOURCE#" . PHP_EOL;

        foreach($documents as $document) {

            $input .= $document . PHP_EOL;

            if(str_word_count($input) > $context_word_count) {
                $input = $this->words($input, $context_word_count, '');
                break;
            }
        }


        $input .= PHP_EOL . PHP_EOL . "### Instruction: " . PHP_EOL . $prompt . PHP_EOL . "### Response: " . PHP_EOL;

        return $input;

    }

    /**
     * Limit the number of words in a string.
     * Based on Illuminate\Support\Str
     *
     * @param  string  $value
     * @param  int  $words
     * @param  string  $end
     * @return string
     */
    private function words($value, $words = 100, $end = '...')
    {
        preg_match('/^\s*+(?:\S++\s*+){1,'.$words.'}/u', $value, $matches);

        if (! isset($matches[0]) || strlen($value) === strlen($matches[0])) {
            return $value;
        }

        return rtrim($matches[0]).$end;
    }

}