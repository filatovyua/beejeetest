<?php

namespace beejeetest\app\controllers;

use beejeetest\app\core\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class ControllerIndex extends Controller{

    public function index(Request $request){
        
        $session = new Session();
        $session->start();
        $isAuthed = $session->get("isAuthed");        
        $this->view->generate("template_view.php", "template_index.html", [
            'isAuthed' => $isAuthed
        ]);
        return Response::create();
    }    
    
    public function auth(Request $request){
        $this->view->generat("template_view.php", "template_auth.html");
        return Response::create();
    }
    
}
