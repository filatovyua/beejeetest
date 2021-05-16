<?php

namespace beejeetest\app\lib;

class ConnectorConfig{
    
    public $host;
    public $user;
    public $password;
    public $port;
    public $database;

    public function __construct() {
        $dbConf = json_decode(file_get_contents(dirname(__DIR__)."/../config/db.json"),true);
        $this->host = $dbConf['host'];
        $this->user = $dbConf['user'];
        $this->password = $dbConf['password'];
        $this->database = $dbConf['database'];
        $this->port = $dbConf['port'];
    }
    
    
}
