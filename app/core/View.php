<?php

namespace beejeetest\app\core;

class View{
    
    private $engine;
    
    public function __construct() {
        $this->engine = new \Mustache_Engine(array('entity_flags' => ENT_QUOTES));
    }
    
    private function getTemplate($templateName){
        return file_get_contents(dirname(__DIR__)."/views/".$templateName);
    }
    
    public function generate($mainTemplate, $templateName, array $context = []){
        $template = $this->getTemplate($templateName);    
        require_once dirname(__DIR__)."/views/".$mainTemplate;
    }
    
}

