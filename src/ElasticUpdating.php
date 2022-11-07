<?php

namespace ElasticAdapted;

class ElasticUpdating extends ElasticBasicSetup
{
    public function updating(){
        $this->checkingRequiredFields();
        $this->checkingEntityInstance();
        $this->checkingID();

        $searchingContent = "";
        $body = [];

        foreach (($this->config)['indexing'] as $entityClass=>$fieldsArray) {
            if ($this->entityInstance instanceof $entityClass){
                foreach ($fieldsArray as $fieldName) {
                    if (!is_string((string)$this->entityInstance->{"get".$fieldName}())){
                        throw new \Exception("Entity method return type is not string;");
                    }
                    $searchingContent .= $this->entityInstance->{"get".$fieldName}() . "|";
                    $body[$fieldName] = $this->entityInstance->{"get".$fieldName}();
                }
                $body['searching_content'] = $searchingContent;
            }
        }

        $params = [
            'index'=>$this->index,
            'id' => $this->id,
            'body'=>$body
        ];

        $this->client->update($params);
    }
}