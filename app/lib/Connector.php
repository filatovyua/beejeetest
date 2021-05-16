<?php

namespace beejeetest\app\lib;

use beejeetest\app\lib\ConnectorConfig;

class Connector {

    private $config;
    private $debug = true;

    public function setDebug($debug) {
        $this->debug = $debug;
    }

    private $cn;
    private $statement;
    private $result;
    private $lastQuery;
    private $lastParams;
    private $fetchResult = true;

    public function setFetchResult(bool $state) {
        $this->fetchResult = $state;
    }

    public function __construct() {
        $this->config = new ConnectorConfig();
        $this->database = $this->config->database;
    }

    public function getConfig() {
        return $this->config;
    }

    public function connect() {
        if ($this->statement)
            return $this;
        $this->cn = new \mysqli($this->config->host, $this->config->user, $this->config->password, $this->config->database);
        if ($this->cn->connect_error)
            throw new \Exception($this->cn->connect_error);
        return $this;
    }

    private function setStatement($sql) {
        $this->lastQuery = $sql;
        $this->statement = $this->cn->prepare($sql);
        if (!$this->statement){
            $this->connect();
            $this->statement = $this->cn->prepare($sql);
        }
        return $this;
    }

    private function bindParams($types, $data) {
        $this->lastParams = $data;
        if (empty($data))
            return $this;
        $this->statement->bind_param($types, ...$data);
        return $this;
    }

    private function exec() {
        $this->statement->execute();
        $this->result = $this->statement->get_result();
        return $this;
    }

    private function getResult() {
        return $this->fetchResult ? $this->fetchAssocAll($this->result) : $this->result;
    }

    private function close() {
        $this->statement->close();
        return $this;
    }

    public function query($sql, $data = [], $types = "") {
        return $this->setStatement($sql)
                        ->bindParams(str_pad($types, count($data), "s"), array_values($data))
                        ->exec()
                        ->close()
                        ->getResult();
    }

    public function call($database, $procedure, $params = [], $types = "") {
        $r = "";
        $params = array_values($params);
        for ($i = 0; $i < count($params); $i += 1) {
            $r .= "?,";
        }
        return $this->setStatement("CALL {$database}.{$procedure}(" . trim($r, ",") . ")")
                        ->bindParams(str_pad($types, count($params), "s"), $params)
                        ->exec()
                        ->close()
                        ->getResult();
    }

    public function lastInsertId() {
        return $this->cn->insert_id ?? 0;
    }

    public function numRows() {
        return $this->result->num_rows ?? 0;
    }

    public function fetch($result) {
        return $result->fetch();
    }

    public function fetchAssoc($result) {
        return $result->fetch_assoc();
    }

    public function fetchAssocAll($result) {
        $out = [];
        while ($r = $this->fetchAssoc($result)) {
            $out[] = $r;
        }
        return $out;
    }

}
