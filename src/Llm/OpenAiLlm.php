<?php

namespace Krisseck\PhpRag\Llm;

use OpenAI;
use OpenAI\Client;

class OpenAiLlm extends Llm implements LlmInterface {

    /**
     * Name of the model that you want to run.
     *
     * @var $model string
     */
    private $model;

    /* @var $client Client */
    private $client;

    public function __construct($api_key, $model = 'gpt-3.5-turbo-instructs') {
        $this->client = OpenAI::Client($api_key);
        $this->model = $model;
    }

    public function query($prompt, $documents)
    {
        $input = $this->prepareInput($prompt, $documents);

        try {

            $response = $this->client->chat()->create([
                'model' => $this->model,
                'messages' => [
                    [
                        'content' => $input,
                        'role' => 'user'
                    ]
                ]
            ]);

            if (!empty($response->choices[0]->message->content)) return $response->choices[0]->message->content;

        } catch(\Exception $e) {
            // Something failed when querying OpenAI
        }

        return false;

    }

}