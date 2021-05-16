<?php

namespace beejeetest\app\core;

abstract class Controller {
    
    protected $model;
    protected $view;
    
    public function __construct() {
        $this->view = new View();
    }
    
}
