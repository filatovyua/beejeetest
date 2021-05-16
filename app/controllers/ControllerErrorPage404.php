<?php

namespace beejeetest\app\controllers;

use beejeetest\app\core\Controller;

class ControllerErrorPage404 extends Controller{
    
    public function index(){        
        $this->view->generate("template_view.php", "template_404.html");
    }    
    
}
