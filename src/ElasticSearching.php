<?php

namespace ElasticAdapted;

class ElasticSearching extends ElasticBasicSetup
{
    public function getById(){
        $this->checkingRequiredFields();
        $this->checkingID();
        $params = [
            'index' => $this->index,
            'id'    => $this->id,
        ];

        $result = $this->client->get($params)->asArray();

        return $result['_source'];
    }

    public function matchSearching($arrayDefinition){
        $this->checkingRequiredFields();

        $params = [
            'index'=>$this->index,
            'body' => [
                'query'=>[
                    'match'=>$arrayDefinition
                ]
            ]
        ];

        $result = $this->client->search($params);

        return $result->asArray()['hits']['hits'];
    }
    public function shouldMatchSearching($arrayDefinition){
        $this->checkingRequiredFields();

        $array = [];

        foreach ($arrayDefinition as $field=>$value) {
            $array[] = ['match'=>[$field=>$value]];
        }

        $params = [
            'index'=>$this->index,
            'body' => [
                'query'=>[
                    'bool'=>[
                        'should'=>$array
                    ]
                ]
            ]
        ];

        $result = $this->client->search($params);
        return $result->asArray()['hits']['hits'];

    }
    public function mustMatchSearching($arrayDefinition){
        $this->checkingRequiredFields();

        $array = [];

        foreach ($arrayDefinition as $field=>$value) {
            $array[] = ['match'=>[$field=>$value]];
        }

        $params = [
            'index'=>$this->index,
            'body' => [
                'query'=>[
                    'bool'=>[
                        'must'=>$array
                    ]
                ]
            ]
        ];

        $result = $this->client->search($params);
        return $result->asArray()['hits']['hits'];
    }
    public function shouldWildcardsSearching($arrayDefinition){
        $this->checkingRequiredFields();

        $array = [];

        foreach ($arrayDefinition as $field=>$value) {
            $array[] = ['wildcard'=>[$field=>$value]];
        }

        $params = [
            'index'=>$this->index,
            'body' => [
                'query'=>[
                    'bool'=>[
                        'should'=>$array
                    ]
                ]
            ]
        ];

        $result = $this->client->search($params);

        return $result->asArray()['hits']['hits'];
    }
    public function mustWildcardsSearching($arrayDefinition){
        $this->checkingRequiredFields();

        $array = [];

        foreach ($arrayDefinition as $field=>$value) {
            $array[] = ['wildcard'=>[$field=>$value]];
        }

        $params = [
            'index'=>$this->index,
            'body' => [
                'query'=>[
                    'bool'=>[
                        'must'=>$array
                    ]
                ]
            ]
        ];

        $result = $this->client->search($params);
        return $result->asArray()['hits']['hits'];
    }

}