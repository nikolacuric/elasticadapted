<?php

namespace ElasticAdapted;

class ElasticDeleting extends ElasticBasicSetup
{
    public function deleting(){
        $this->checkingRequiredFields();
        $this->checkingID();

        $params = [
            'index' => $this->index,
            'id'    => $this->id,
        ];

        $this->client->delete($params);
    }
    public function deleteAllByIndex($index){

        foreach (($this->config)['hosts'] as $host) {
            $url = $host."/".$index;
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_exec($curl);
        }
    }
}