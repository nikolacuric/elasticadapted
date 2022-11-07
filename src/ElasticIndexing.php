<?php

namespace ElasticAdapted;

class ElasticIndexing extends ElasticBasicSetup
{
    public function indexing(){
        $this->checkingRequiredFields();
        $this->checkingEntityInstance();

        $searchingContent = "";
        $body = [];
        $class = "";

        foreach (($this->config)['indexing'] as $entityClass=>$fieldsArray) {
            if ($this->entityInstance instanceof $entityClass){
                foreach ($fieldsArray as $fieldName) {
                    if (!is_string((string)$this->entityInstance->{"get".$fieldName}())){
                        throw new \Exception("Entity method return type is not string;");
                    }
                    $searchingContent .= $this->entityInstance->{"get".$fieldName}() . " | ";
                    $body[$fieldName] = $this->entityInstance->{"get".$fieldName}();
                }
                $body['searching_content'] = $searchingContent;
                $class = $this->getClassFromFullyQualifiedCassName($entityClass);
                break;
            }
        }

        $params = [
            'index'=>$this->index,
            'body'=>$body
        ];

        if ($this->id !== null){
            $params = [
                'index'=>$this->index,
                'id' => $class."_".$this->id,
                'body'=>$body
            ];
        }

        $this->client->index($params);
    }
}