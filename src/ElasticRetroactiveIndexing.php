<?php

namespace ElasticAdapted;

use Elastic\Elasticsearch\ClientBuilder;

class ElasticRetroactiveIndexing{

    /**
     * @var \PDO
     */
    private $pdoInstance;

    /**
     * @var \Elastic\Elasticsearch\Client
     */
    private $client;

    /**
     * @var array
     */
    private $indexingConfig;

    public function __construct($retroactiveIndexingConfig)
    {
        $this->indexingConfig = $retroactiveIndexingConfig['indexingConfig'];
        $this->pdoInstance = $this->createPdoInstance($retroactiveIndexingConfig['dbConfig']);
        $this->client = ClientBuilder::create()->setHosts($retroactiveIndexingConfig['elasticHosts'])->build();
    }
    private function createPdoInstance($dbConfig){
        $dbName = $dbConfig['dbName'];
        $dbHost = $dbConfig['dbHost'];
        $dsn = 'mysql:dbname='.$dbName.';host='.$dbHost;
        $user = $dbConfig['dbUser'];
        $password = $dbConfig['dbPass'];

        return new \PDO($dsn, $user, $password);
    }

    private function buildQuery($queryConfig, $indexConfig){
        $fieldsAsString = "";
        foreach ($queryConfig['fields'] as $field) {
            $fieldsAsString .= $field.", ";
        }
        $fieldsAsString = rtrim($fieldsAsString, ", ");

        return "SELECT ".$indexConfig['id'].", ".$fieldsAsString." FROM ".$queryConfig['table'].";";
    }

    private function indexData($query, $indexConfig, $queryConfig){
        $dbDataCollection = $this->pdoInstance->query($query);
        $params = [];
        foreach ($dbDataCollection as $row) {
            $searchingContent = "";
            $params['index'] = $indexConfig['index'];
            $params['id'] = $this->transformTableName($queryConfig['table'])."_".$row[$indexConfig['id']];
            foreach ($queryConfig['fields'] as $field) {
                $searchingContent .= $row[$field]." | ";
                $params['body'][$this->transformBodyFieldName($field)] = $row[$field];
            }

            $params['body']['searching_content'] = $searchingContent;
            $this->client->index($params);
        }
    }

    private function transformBodyFieldName($field){
        $finalTerm = "";
        foreach (explode("_", $field) as $key=>$part) {
            if ($key === 0) {
                $finalTerm .= $part;
            } else {
                $finalTerm .= ucfirst($part);
            }
        }
        return $finalTerm;
    }

    private function transformTableName($tableName){
        $entityName = "";
        $holder = explode('_', $tableName);
        foreach ($holder as $item) {
            $entityName .= ucfirst($item);
        }
        return $entityName;
    }

    public function doIndexing()
    {
        foreach ($this->indexingConfig as $item) {
            $queryConfig = $item['queryConfig'];
            $indexConfig = $item['indexConfig'];

            $query = $this->buildQuery($queryConfig, $indexConfig);

            $this->indexData($query, $indexConfig, $queryConfig);
        }
    }
    public static function createRetroactiveIndexing($retroactiveIndexingConfig){

        return new self($retroactiveIndexingConfig);
    }
}

