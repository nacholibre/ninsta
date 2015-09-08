<?php

class NInsta_Client {
    function __construct($apiURL) {
        $clientDefaultParams = Array();

        $this->_restClient = new GuzzleHttp\Client([
        'base_url' => $apiURL,
        'defaults' => [
            'query' => $clientDefaultParams],
            'headers' => [
                'User-Agent' => 'host:'.$_SERVER['HTTP_HOST']
            ],
        ]);
    }

    private function _queryAPI($url, $options = [], $type='get') {
        $header = array(
            "Keep-Alive: 300"           ,
            "Connection: keep-alive"
        );

        $options["config"]["curl"]["CURLOPT_HTTPHEADER"]  = $header;
        $options["config"]["curl"]["CURLOPT_TCP_NODELAY"] = true;
        $options["config"]["curl"]["CURLOPT_ENCODING"]    = ""; // enable gzip / zlib negotiations.


        if ($type == 'get') {
            $response = $this->_restClient->get($url, $options);
        } elseif($type == 'post') {
            $response = $this->_restClient->post($url, $options);
        } else {
            exit('No such _queryAPI type');
        }

        $body = $response->getBody();
        $data = array();

        while (!$body->eof()) {
            array_push($data, $body->read(1024));
        }

        $json = implode('', $data);

        return json_decode($json);
    }

    public function getUserInfo($username) {
        try{
            return $this->_queryAPI(['userInfo/{name}', ['name' => $username]]);
        }catch(Exception $e){
            //return $this->_exceptionHandle(__METHOD__, func_get_args(), $e);
        }
    }

    public function getRecentUserMedia($username, $maxID=null, $perPage=33) {
        $options = [];

        $options['query'] = ['maxID' => $maxID, 'perPage' => $perPage];

        try{
            return $this->_queryAPI(['userMedia/{username}', ['username' => $username]], $options);
        }catch(Exception $e){
            //var_dump($e);
            //return $this->_exceptionHandle(__METHOD__, func_get_args(), $e);
        }
    }

    public function getRecentTags($tag, $maxID=null, $perPage=30) {
        $options = [];

        $options['query'] = ['maxID' => $maxID, 'perPage' => $perPage];

        try{
            return $this->_queryAPI(['tag/{tag}', ['tag' => $tag]], $options);
        }catch(Exception $e){
            //var_dump($e);
            //return $this->_exceptionHandle(__METHOD__, func_get_args(), $e);
        }
    }

    public function search($query) {
        try{
            return $this->_queryAPI(['search/{query}', ['query' => $query]]);
        } catch(Exception $e){
            //var_dump($e);
            //return $this->_exceptionHandle(__METHOD__, func_get_args(), $e);
        }
    }

    public function getMedia($mediaID) {
        try{
            return $this->_queryAPI(['media/{id}', ['id' => $mediaID]]);
        } catch(Exception $e){
            //var_dump($e);
            //return $this->_exceptionHandle(__METHOD__, func_get_args(), $e);
        }
    }

    public function getTopUsers() {
        try{
            return $this->_queryAPI('popularUsers');
        } catch(Exception $e){
            //var_dump($e);
            //return $this->_exceptionHandle(__METHOD__, func_get_args(), $e);
        }
    }
}
