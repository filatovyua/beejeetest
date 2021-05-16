<?php

namespace beejeetest\app\models;

use beejeetest\app\core\Model;
use beejeetest\app\lib\Connector;
use Symfony\Component\Validator\Constraints\Email as EmailConstraints;
use Symfony\Component\Validator\Validation;

class ModelTask extends Model {

    private $statuses = [
        0 => 'Открыта',
        1 => 'Выполнена',
        2 => 'Отредактирована администратором',
        3 => 'Выполнена и отредактирована администратором'
    ];

    private function validateEmail($email) {
        $validator = Validation::createValidator();
        $emailConstraint = new EmailConstraints();
        $errors = $validator->validate(
                $email,
                $emailConstraint
        );
        return count($errors) == 0;
    }

    public function get() {
        $connector = new Connector();
        $connector->connect();
        $out = $connector->call($connector->getConfig()->database, "tasks_get", ['']);
        return [$out, $this->statuses];
    }

    public function add($name, $email, $content) {
        if (!$this->validateEmail($email)) {
            return [
                'error' => true,
                'message' => 'Email is not correct'
            ];
        }
        $connector = new Connector();
        $connector->connect();
        $id = $connector->call($connector->getConfig()->database, "tasks_add", [
                    'name' => $name,
                    'email' => $email,
                    'content' => $content
                ])[0]['id'] ?? 0;
        return [
            'id' => $id
        ];
    }

    private function getNewStatus($task) {
        switch ($task["status"]) {
            case 0:
                return 2;
            case 1:
                return 3;
            case 2:
                return 3;
            default:
                return $task["status"];
        }
    }

    public function edit($id, $key, $value) {

        if ($key == "status" && !isset($this->statuses[$value])) {
            return [
                'error' => true,
                'message' => 'Status is not correct'
            ];
        }
        if ($key == "email" && !$this->validateEmail($value)) {
            return [
                'error' => true,
                'message' => 'Email is not valid'
            ];
        }
        $connector = new Connector();
        $connector->connect();
        $out = $connector->call($connector->getConfig()->database, "tasks_edit", [
                    'id' => $id,
                    'key' => $key,
                    'value' => $value
                ])[0] ?? [];
        $task = $connector->call($connector->getConfig()->database, "tasks_get", [$id])[0];
        $newStatus = $this->getNewStatus($task);
        if ($newStatus != $task["status"]) {
            $out = $connector->call($connector->getConfig()->database, "tasks_edit", [
                        'id' => $id,
                        'key' => 'status',
                        'value' => $newStatus
                    ])[0] ?? [];
        }
        return $out;
    }

}
