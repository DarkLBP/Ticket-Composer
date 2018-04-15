<?php
namespace Controllers;

use Core\Controller;
use Core\Utils;
use Models\UsersModel;
use Models\UsersSessionsModel;
use Models\UsersValidationModel;

class UserController extends Controller
{
    public function actionLogin() {
        $errors = [];
        if ($this->request->isPost()) {
            $email = $this->request->getPostParam('email', true);
            $password = $this->request->getPostParam('password');
            if (empty($email)) {
                $errors[] = 'Email is empty';
            } else if (empty($password)) {
                $errors[] = 'Password is empty';
            } else {
                $userModel = new UsersModel();
                $validationModel = new UsersValidationModel();
                $userModel->join($validationModel, 'id', 'userId', 'left');
                $user = $userModel->findOne($email, "email", [
                    ["$userModel->tableName.id", 'userId'],
                    ["$validationModel->tableName.id", "validationId"],
                    "password"
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
                                $this->request->redirect(Utils::getURL('tickets'));
                            } catch (\Exception $e) {
                                $errors[] = 'Internal server error';
                            }
                        } else {
                            $errors[] = 'Account pending for validation';
                        }
                    } else {
                        $errors[] = "Invalid credentials";
                    }
                } else {
                    $errors[] = "Invalid credentials";
                }
            }
        }
        $this->request->setViewParam('errors', $errors);
        $this->renderView("login");
    }

    public function actionRegister($params = []) {
        if (isset($params[0])) {
            if ($params[0] === 'completed' && $this->request->getSessionParam('registered')) {
                $this->request->setSessionParam('registered', false);
                $this->renderView('registerCompleted');
                return;
            }
            $this->request->redirect(Utils::getURL("user", "register"));
        }
        $errors = [];
        if ($this->request->isPost()) {
            $name = $this->request->getPostParam("name", true);
            $surname = $this->request->getPostParam("surname", true);
            $email = $this->request->getPostParam("email", true);
            $password = $this->request->getPostParam("password");
            $confirm = $this->request->getPostParam("confirm");

            if (empty($name)) {
                $errors[] = 'Name is empty';
            } else if (empty($surname)) {
                $errors[] = 'Surname is empty';
            } else if (empty($email)) {
                $errors[] = 'Email is empty';
            } else if (empty($password)) {
                $errors[] = 'Password is empty';
            } else if (empty($confirm)) {
                $errors[] = 'Password confirmation is empty';
            } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Invalid email';
            } else if ($password !== $confirm) {
                $errors[] = 'Passwords do not match';
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
                        $link = Utils::getURL('user', 'validate', [$validationCode]);
                        mail($email, 'Account Validation', 'Please click this link to validate your account ' . $link);
                        $this->request->setSessionParam('registered', true);
                        $this->request->redirect(Utils::getURL("user", "register", ["completed"]));
                    } catch (\Exception $e) {
                        $errors[] = 'Internal server error.';
                    }
                } else {
                    $errors[] = 'Email is already in use';
                }
            }
        }
        $this->request->setViewParam('errors', $errors);
        $this->renderView("register");
    }

    public function actionValidate($params = []) {
        if (!empty($params[0])) {
            $key = $params[0];
            $validation = new UsersValidationModel();
            $row = $validation->count(['id' => $key]);
            if ($row === 1) {
                $validation->delete(['id' => $key]);
                $this->renderView('validateCompleted');
                return;
            }
        }
        $this->renderView('validateInvalid');
    }
}