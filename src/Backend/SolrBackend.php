<?php

namespace Krisseck\PhpRag\Backend;

use Solarium\Client;
use Solarium\Core\Client\Adapter\Curl;
use Symfony\Component\EventDispatcher\EventDispatcher;

class SolrBackend implements BackendInterface
{

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

    /**
     * Indexes a piece of content.
     *
     * @param $id
     * @param $content
     * @return bool
     */
    public function indexContent($id, $content)
    {
        $update = $this->client->createUpdate();

        $doc = $update->createDocument();
        $doc->id = $id;
        $doc->ts_text = $content;

        $update->addDocuments([$doc]);
        $update->addCommit();

        return !!$this->client->update($update);
    }

    public function clearIndex()
    {
        $update = $this->client->createUpdate();

        $update->addDeleteQuery('*:*');

        $update->addCommit();

        return !!$this->client->update($update);
    }

    public function search($prompt, $amount = 5)
    {
        $query = $this->client->createSelect();

        $query->setQuery($prompt);

        $dismax = $query->getDisMax();

        $dismax->setQueryFields('ts_text');

        $dismax->setMinimumMatch('50%');

        $query->setStart(0)->setRows($amount);

        $results = $this->client->select($query);

        $return = [];

        foreach ($results as $doc) {
            $return[] = $doc->ts_text;
        }

        return $return;
    }

}