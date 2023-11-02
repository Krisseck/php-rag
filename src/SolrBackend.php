<?php

namespace Krisseck\PhpRag;

use Solarium\Client;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Solarium\Core\Client\Adapter\Curl;

class SolrBackend {

    /* @var $client \Solarium\Client */
    private $client;

    public function __construct($solr_host, $solr_port, $solr_core) {

        $adapter = new Curl();
        $eventDispatcher = new EventDispatcher();

        $this->client = new Client($adapter, $eventDispatcher, [
            'endpoint' => [
                'host' => [
                    'host' => $solr_host,
                    'port' => $solr_port,
                    'path' => '/',
                    'core' => $solr_core
                ]
            ]
        ]);

    }

}