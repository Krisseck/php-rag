<?php

namespace Krisseck\PhpRag\Llm;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class KoboldAiHordeLlm extends Llm implements LlmInterface {

    /**
     * @var $models array List of models to be used.
     */
    private $models;

    /**
     * @param $api_key string
     * @param $models array
     */
    public function __construct($api_key, $models = []) {
        $this->client = new Client([
            'headers' => [
                'apikey' => $api_key,
                'Client-Agent' => 'Krisseck-PHP-RAG 1.0',
            ]
        ]);
        $this->models = $models;
    }

    public function query($prompt, $documents)
    {
        $input = $this->prepareInput($prompt, $documents);

        var_dump($input);

        try {
            $request = $this->client->post("https://stablehorde.net/api/v2/generate/text/async", [
                'json' => [
                    'prompt' => $input,
                    'models' => $this->models
                ]
            ]);

            if($json = json_decode($request->getBody()->getContents())) {

                if(!empty($json->id)) {
                    $prediction_id = $json->id;

                    // Start polling the API for results

                    $got_response = false;

                    while(!$got_response) {

                        $request = $this->client->get("https://stablehorde.net/api/v2/generate/text/status/" . $prediction_id);

                        if($json = json_decode($request->getBody()->getContents())) {

                            if(!empty($json->done)) {

                                if(empty($json->generations[0]->text)) {
                                    // Failed on KoboldAI's end
                                    return false;
                                } else {
                                    // Got the response from KoboldAI
                                    return $json->generations[0]->text;
                                }

                            }

                        }

                        // Wait some time before polling the API again
                        sleep(2);

                    }

                }

            }

        } catch (RequestException $e) {
            // Something failed when querying KoboldAI Horde API
        }

        return false;

    }

}