<?php

namespace Krisseck\PhpRag\Llm;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class ReplicateLlm extends Llm implements LlmInterface {

    /**
     * The ID of the model version that you want to run.
     *
     * @var $model_version string
     */
    private $model_version;

    /* @var $client \GuzzleHttp\Client */
    private $client;

    public function __construct($token, $model_version) {
        $this->client = new Client([
            'headers' => [
                'Authorization' => 'Token ' . $token
            ]
        ]);
        $this->model_version = $model_version;
    }

    public function query($prompt, $documents)
    {
        $input = $this->prepareInput($prompt, $documents);

        try {
            $request = $this->client->post("https://api.replicate.com/v1/predictions", [
                'json' => [
                    'version' => $this->model_version,
                    'input' => [
                        'prompt' => $input
                    ]
                ]
            ]);

            if($json = json_decode($request->getBody()->getContents())) {

                if(!empty($json->id)) {
                    $prediction_id = $json->id;

                    // Start polling the API for results

                    $got_response = false;

                    while(!$got_response) {

                        $request = $this->client->get("https://api.replicate.com/v1/predictions/" . $prediction_id);

                        if($json = json_decode($request->getBody()->getContents())) {

                            if(!empty($json->status)) {

                                if(in_array($json->status, ['failed', 'canceled'])) {
                                    // Failed on Replicate's end
                                    return false;
                                } else if($json->status == 'succeeded') {
                                    // Got the response from Replicate
                                    return implode("", $json->output);
                                }

                            }

                        }

                        // Wait some time before polling the API again
                        sleep(2);

                    }

                }

            }

        } catch (RequestException $e) {
            // Something failed when querying Replicate API
        }

        return false;

    }

}