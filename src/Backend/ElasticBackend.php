<?php

namespace Krisseck\PhpRag\Backend;

use Elastic\Elasticsearch\ClientBuilder;

class ElasticBackend implements BackendInterface
{

    /* @var $client \Elastic\Elasticsearch\Client */
    private $client;

    /* @var $index string */
    private $index;

    public function __construct($elastic_host, $elastic_api_key, $elastic_index) {

        $this->client = ClientBuilder::create()
            ->setHosts([$elastic_host])
            ->setApiKey($elastic_api_key)
            ->build();

        $this->index = $elastic_index;
    }

    /**
     * Indexes a piece of content.
     *
     * @param $id
     * @param $content
     * @return bool
     */
    public function indexContent($id, $content)
    {
        try {
            $this->client->index([
                'index' => $this->index,
                'id' => $id,
                'body' => [
                    'content' => $content
                ]
            ]);
            return true;
        } catch(\Exception $e) {
            // Something failed when indexing
            return false;
        }
    }

    public function clearIndex()
    {
        try {
            $this->client->deleteByQuery([
                "index" => $this->index,
                "body" => [
                    "query" => [
                        "match_all" => (object)[]
                    ]
                ]
            ]);
            return true;
        } catch(\Exception $e) {
            // Something failed when deleting
            return false;
        }
    }

    public function search($prompt, $amount = 5)
    {
        try {

            $response = $this->client->search([
                'index' => $this->index,
                'size' => $amount,
                'body'  => [
                    'query' => [
                        'match' => [
                            'content' => [
                                'query' => $prompt,
                                'fuzziness' => 'AUTO',
                                'minimum_should_match' => '50%'
                            ],
                        ]
                    ]
                ]
            ]);

            return array_map(function($result) { return $result['_source']['content']; }, $response->asArray()['hits']['hits']);

        } catch(\Exception $e) {
            // Something failed when deleting
            return [];
        }
    }

}