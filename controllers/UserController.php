<?php
namespace Controllers;

use Core\Controller;
use Models\UsersModel;
use Models\UsersSessionsModel;
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
                    ["$userModel->tableName.id", 'userId'],
                    ["$validationModel->tableName.id", "validationId"]
                ]);
                if (!empty($user)) {
                    if (password_verify($password, $user["password"])) {
                        if (empty($user["validationId"])) {
                            try {
                                $sessionToken = bin2hex(random_bytes(32));
                                $sessionModel = new UsersSessionsModel();
                                $sessionModel->insert([
                                    'id' => $sessionToken,
                                    'userId' => $user['userId']
                                ]);
                                setcookie('userToken', "$user[id]-$sessionToken", time() + (3600 * 24 * 30), '/');
                                $this->request->setSessionParam('user', $user['userId']);
                                $this->request->redirect($this->request->getURL('ticket'));
                            } catch (\Exception $e) {
                                $error = 'Internal server error';
                            }
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
        $this->request->setViewParam('error', $error);
        $this->renderView("login");
    }

    public function actionRegister($params = []) {
        if (isset($params[0])) {
            if ($params[0] === 'completed' && $this->request->getSessionParam('registered')) {
                $this->request->setSessionParam('registered', false);
                $this->renderView('registerCompleted');
                return;
            }
            $this->request->redirect('/user/register');
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
                        $validationCode = bin2hex(random_bytes(32));
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
        $this->request->setViewParam('error', $error);
        $this->renderView("register");
    }

    public function actionValidate($params = []) {
        if (!empty($params[0])) {
            $key = $params[0];
            $validation = new UsersValidationModel();
            $row = $validation->count(['id' => $key]);
            if ($row === 1) {
                $loginURL = $this->request->getURL('user', 'login');
                $validation->delete(['id' => $key]);
                $this->request->setViewParam('loginURL', $loginURL);
                $this->renderView('validateCompleted');
                return;
            }
        }
        $this->renderView('validateInvalid');
    }
}