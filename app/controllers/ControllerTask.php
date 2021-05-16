<?php

namespace beejeetest\app\controllers;

use beejeetest\app\core\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use beejeetest\app\models\ModelTask;

class ControllerTask extends Controller {

    public function all(Request $request) {
        $modelTask = new ModelTask();
        [$tasks, $statuses] = $modelTask->get();
        return JsonResponse::create(['data' => $tasks, 'statuses' => $statuses], 200, []);
    }

    public function add(Request $request) {
        $data = json_decode($request->getContent(), true);
        $modelTask = new ModelTask();
        $out = $modelTask->add(
                htmlspecialchars($data['name']),
                htmlspecialchars($data['email']),
                htmlspecialchars($data['content'])
        );
        return JsonResponse::create($out, 200, []);
    }

    public function edit(Request $request) {
        
        $session = new Session();
        $session->start();
        if (!$session->get("isAuthed")){
            return JsonResponse::create([
                'error' => true,
                'message' => 'access'
            ]);
        }
        $data = json_decode($request->getContent(), true);
        $modelTask = new ModelTask();
        $out = $modelTask->edit(htmlspecialchars($data['id']), htmlspecialchars($data['key']), htmlspecialchars($data['value']));
        return JsonResponse::create($out, 200, []);
    }

}
