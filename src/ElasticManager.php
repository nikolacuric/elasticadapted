<?php

namespace ElasticAdapted;

class ElasticManager
{
    /**
     * @var ElasticIndexing
     */
    private $elasticIndexing;
    /**
     * @var ElasticSearching
     */
    private $elasticSearching;
    /**
     * @var ElasticUpdating
     */
    private $elasticUpdating;
    /**
     * @var ElasticDeleting
     */
    private $elasticDeleting;

    /**
     * @return ElasticIndexing
     */
    public function getElasticIndexing(): ElasticIndexing
    {
        return $this->elasticIndexing;
    }

    /**
     * @return ElasticSearching
     */
    public function getElasticSearching(): ElasticSearching
    {
        return $this->elasticSearching;
    }

    /**
     * @return ElasticUpdating
     */
    public function getElasticUpdating(): ElasticUpdating
    {
        return $this->elasticUpdating;
    }

    /**
     * @return ElasticDeleting
     */
    public function getElasticDeleting(): ElasticDeleting
    {
        return $this->elasticDeleting;
    }

    /**
     * @param $config
     * @param $client
     * @param $index
     */
    public function __construct($config)
    {
        $this->elasticIndexing = new ElasticIndexing($config);
        $this->elasticSearching = new ElasticSearching($config);
        $this->elasticUpdating = new ElasticUpdating($config);
        $this->elasticDeleting = new ElasticDeleting($config);
    }
}