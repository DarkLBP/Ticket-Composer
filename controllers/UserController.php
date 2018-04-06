<?php
namespace Controllers;

use Core\Controller;
use Models\UsersModel;

class UserController extends Controller
{
    public function actionLogin() {
        $error = '';
        if ($this->request->isPost()) {
            $email = $this->request->getPostParam('email', true);
            $password = $this->request->getPostParam('password');
            if (empty($email)) {
                $error = 'Email is empty';
            } else if (empty($password)) {
                $error = 'Password is empty';
            } else {
                $model = new UsersModel();
                $user = $model->findOne($email, "email");
                if (!empty($user)) {
                    if (password_verify($password, $user["password"])) {
                        die("Okey");
                    }
                }
                $error = "Invalid credentials";
            }
        }
        $this->renderView("login", ["error" => $error]);
    }

    public function actionRegister($params = []) {
        if (isset($params[0]) && $params[0] === 'complete') {
            if ($this->request->getSessionParam('registered')) {
                $this->request->setSessionParam('registered', false);
                die("Thanks for registering");
            } else {
                $this->request->redirect('/user/register');
            }
        }
        $error = '';
        if ($this->request->isPost()) {
            $name = $this->request->getPostParam("name", true);
            $surname = $this->request->getPostParam("surname", true);
            $email = $this->request->getPostParam("email", true);
            $password = $this->request->getPostParam("password");
            $confirm = $this->request->getPostParam("confirm");

            if (empty($name)) {
                $error = 'Name is empty';
            } else if (empty($surname)) {
                $error = 'Surname is empty';
            } else if (empty($email)) {
                $error = 'Email is empty';
            } else if (empty($password)) {
                $error = 'Password is empty';
            } else if (empty($confirm)) {
                $error = 'Password confirmation is empty';
            } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Invalid email';
            } else if ($password !== $confirm) {
                $error = 'Passwords do not match';
            } else {
                $model = new UsersModel();
                $model->insert([
                    "name" => $name,
                    "surname" => $surname,
                    "email" => $email,
                    "password" => password_hash($password, PASSWORD_DEFAULT)
                ]);
                $this->request->setSessionParam('registered', true);
                $this->request->redirect('/user/register/complete');
            }
        }
        $this->renderView("register", ["error" => $error]);
    }
}