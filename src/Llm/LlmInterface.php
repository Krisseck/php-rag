<?php

namespace Krisseck\PhpRag\Llm;

interface LlmInterface {

    /**
     * @param $prompt string
     * @param $documents array
     * @return string|false
     */
    public function query($prompt, $documents);
}