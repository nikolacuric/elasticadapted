<?php

namespace ElasticAdapted;

class ElasticBasicSetup
{
    protected $config;

    protected $client;

    protected $index;

    protected $id;

    protected $entityInstance;

    /**
     * @param mixed $index
     */
    public function setIndex($index): void
    {
        $this->index = $index;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @param mixed $entityInstance
     */
    public function setEntityInstance($entityInstance): void
    {
        $this->entityInstance = $entityInstance;
    }

    protected function checkingRequiredFields(){
        if ($this->client === null){
            throw new \Exception('You have to provide elasticsearch PHP client');
        }
        if ($this->config === null){
            throw new \Exception('You have to provide configuration array');
        }
        if ($this->index === null){
            throw new \Exception('You have to provide elasticsearch index');
        }
    }

    protected function checkingID(){
        if ($this->id === null){
            throw new \Exception('You have to provide ID');
        }
    }
    protected function checkingEntityInstance(){
        if ($this->entityInstance === null){
            throw new \Exception('You have to provide entity instance');
        }
    }

    public function __construct($config, $client)
    {
        $this->config = $config;
        $this->client = $client;
    }

    protected function getClassFromFullyQualifiedCassName($fullyQualifiedClassName){
        $holder = explode('\\', $fullyQualifiedClassName);
        return end($holder);
    }
}