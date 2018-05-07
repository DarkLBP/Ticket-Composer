<?php

namespace Controllers;

use Core\Controller;
use Core\Utils;

class InstallController extends Controller {
    public function actionIndex()
    {
        if (file_exists(__DIR__ . "/../core/Config.php")) {
            $this->request->redirect(Utils::getURL());
        }
        if ($this->request->isPost()) {
            $title = $this->request->getPostParam('title', true);
            $mHost = $this->request->getPostParam('mhost', true);
            $mUser = $this->request->getPostParam('muser', true);
            $mPass = $this->request->getPostParam('mpass', false);
            $mDB = $this->request->getPostParam('mdb', true);
            $errors = [];
            if (empty($title)) {
                $errors[] = "Title is empty";
            }
            if (empty($mHost)) {
                $errors[] = "MySQL host is empty";
            }
            if (empty($mUser)) {
                $errors[] = "MySQL user is empty";
            }
            if (empty($mDB)) {
                $errors[] = "MySQL db is empty";
            }

            $name = $this->request->getPostParam("name", true);
            $surname = $this->request->getPostParam("surname", true);
            $email = $this->request->getPostParam("email", true);
            $password = $this->request->getPostParam("password");
            $confirm = $this->request->getPostParam("confirm");

            if (empty($name)) {
                $errors[] = 'Name is empty';
            }
            if (empty($surname)) {
                $errors[] = 'Surname is empty';
            }
            if (empty($email)) {
                $errors[] = 'Email is empty';
            }
            if (empty($password)) {
                $errors[] = 'Password is empty';
            }
            if (empty($confirm)) {
                $errors[] = 'Password confirmation is empty';
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Invalid email';
            }
            if ($password !== $confirm) {
                $errors[] = 'Passwords do not match';
            }

            if (empty($errors)) {
                $con = @new \mysqli($mHost, $mUser, $mPass, $mDB);
                if ($con->connect_errno) {
                    $errors[] = "Failed to connect to MySQL";
                } else {
                    $con->multi_query(file_get_contents(__DIR__ . '/../core/sql/Install.sql'));
                    copy(__DIR__ . '/../core/ConfigDefault.php', __DIR__ . '/../core/Config.php');
                    $configString = "
define(\"DATABASE_HOST\", \"$mHost\");
define(\"DATABASE_USER\", \"$mUser\");
define(\"DATABASE_PASSWORD\", \"$mPass\");
define(\"DATABASE_DB\", \"$mDB\");
                    ";
                    file_put_contents(__DIR__ . '/../core/Config.php', $configString, FILE_APPEND);
                    include_once __DIR__ . '/../core/Config.php';
                    $userModel = $this->getModel('users');
                    $userModel->insert([
                        'name' => $name,
                        'surname' => $surname,
                        'email' => $email,
                        'password' => password_hash($password, PASSWORD_DEFAULT),
                        'op' => 1
                    ]);
                    $this->request->setSessionParam("completed", true);
                    $this->request->redirect(Utils::getURL('install', 'completed'));
                }
            }
            $this->request->setViewParam('errors', $errors);
        }
        $this->renderView('install');
    }

    public function actionCompleted()
    {
        if ($this->request->getSessionParam("completed")) {
            $this->request->setSessionParam("completed");
            $this->renderView('completed');
        }
        $this->request->redirect(Utils::getURL());
    }
}