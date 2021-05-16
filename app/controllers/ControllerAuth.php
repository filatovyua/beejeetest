<?php

namespace beejeetest\app\controllers;

use beejeetest\app\core\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\Session;

class ControllerAuth extends Controller {

    const ADMIN_LOGIN = 'admin';
    const ADMIN_PASSWORD = '123';

    public function index(Request $request) {
        $this->view->generate("template_view.php", "template_auth.html");
        return Response::create();
    }

    public function auth(Request $request) {
        $data = json_decode($request->getContent(), true);
        
        if ($data['login'] != self::ADMIN_LOGIN 
                || $data['password'] != self::ADMIN_PASSWORD) {
            return JsonResponse::create([
                        'error' => true,
                        'message' => 'incorrect login/password'
            ]);
        }

        $session = new Session();
        $session->start();
        $session->set("isAuthed", self::ADMIN_LOGIN);
        return JsonResponse::create(['status' => 'ok']);
    }

    public function logout(Request $request) {
        $session = new Session();
        $session->start();
        $session->remove("isAuthed");
        return JsonResponse::create(['status' => 'ok']);
    }

}
