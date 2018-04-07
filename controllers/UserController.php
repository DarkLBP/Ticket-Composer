<?php
namespace Controllers;

use Core\Controller;
use Models\UsersModel;
use Models\UsersValidationModel;

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
                $userModel = new UsersModel();
                $validationModel = new UsersValidationModel();
                $userModel->join($validationModel, 'id', 'userId', 'left');
                $user = $userModel->findOne($email, "email", [
                    "$userModel->tableName.*",
                    ["$validationModel->tableName.id", "validationId"]
                ]);
                if (!empty($user)) {
                    if (password_verify($password, $user["password"])) {
                        if (empty($user["validationId"])) {
                            die("Okey");
                        } else {
                            $error = 'Account pending for validation';
                        }
                    } else {
                        $error = "Invalid credentials";
                    }
                } else {
                    $error = "Invalid credentials";
                }
            }
        }
        $this->renderView("login", ["error" => $error]);
    }

    public function actionRegister($params = []) {
        if (isset($params[0]) && $params[0] === 'completed') {
            if ($this->request->getSessionParam('registered')) {
                $this->request->setSessionParam('registered', false);
                $this->renderView('registerCompleted');
                return;
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
                //Find if email is in use
                $existing = $model->findOne($email, 'email', ['email']);
                if (empty($existing)) {
                    try {
                        $validationCode = bin2hex(random_bytes(16));
                        $insertId = $model->insert([
                            "name" => $name,
                            "surname" => $surname,
                            "email" => $email,
                            "password" => password_hash($password, PASSWORD_DEFAULT)
                        ]);
                        $validation = new UsersValidationModel();
                        $validation->insert([
                            'id' =>  $validationCode,
                            'userId' => $insertId
                        ]);
                        $link = $this->request->getURL('user', 'validate', [$validationCode]);
                        mail($email, 'Account Validation', 'Please click this link to validate your account ' . $link);
                        $this->request->setSessionParam('registered', true);
                        $this->request->redirect('/user/register/completed');
                    } catch (\Exception $e) {
                        $error = 'Internal server error.';
                    }
                } else {
                    $error = 'Email is already in use';
                }
            }
        }
        $this->renderView("register", ["error" => $error]);
    }

    public function actionValidate($params = []) {
        if (!empty($params[0])) {
            $key = $params[0];
            $validation = new UsersValidationModel();
            $row = $validation->count(['id' => $key]);
            if ($row === 1) {
                $loginURL = $this->request->getURL('user', 'login');
                $validation->delete(['id' => $key]);
                $this->renderView('validateCompleted', ['loginURL' => $loginURL]);
                return;
            }
        }
        $this->renderView('validateInvalid');
    }
}