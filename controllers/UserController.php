<?php

namespace Controllers;

require_once __DIR__ . '/../vendor/SimpleMailer.php';

use Core\Controller;
use Core\Utils;

class UserController extends Controller
{
    public function actionCreate()
    {
        $loggedUser = $this->request->getSessionParam('loggedUser');;
        if ($loggedUser['op'] != 1) {
            $this->renderView('forbidden');
        }
        if ($this->request->isPost()) {
            $errors = [];
            $name = $this->request->getPostParam("name", true);
            $surname = $this->request->getPostParam("surname", true);
            $email = $this->request->getPostParam("email", true);
            $password = $this->request->getPostParam("password");
            $confirm = $this->request->getPostParam("confirm");
            $op = $this->request->getPostParam('op', false);
            $departments = $this->request->getPostParam('departments');
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
                $model = $this->getModel('users');
                $userDepartmentsModel = $this->getModel('usersDepartments');

                //Find if email is in use
                $existing = $model->count([
                    ["email", '=', $email]
                ]);
                if ($existing === 0) {
                    $userId = $model->insert([
                        "name" => $name,
                        "surname" => $surname,
                        "email" => $email,
                        "password" => password_hash($password, PASSWORD_DEFAULT),
                        "op" => intval(!empty($op))
                    ]);
                    if (!empty($departments)) {
                        foreach ($departments as $department) {
                            $userDepartmentsModel->insert([
                                'userId' => $userId,
                                'departmentId' => $department
                            ]);
                        }
                    }
                    $this->request->redirect(Utils::getURL("panel", "users"));
                } else {
                    $errors[] = 'Email is already in use';
                }
            }
            $this->request->setViewParam('errors', $errors);
        }
        $departmentsModel = $this->getModel('departments');
        $departments = $departmentsModel->find();
        $this->request->setViewParam('departments', $departments);
        $this->renderView("create");
    }

    public function actionDelete($params = [])
    {
        $loggedUser = $this->request->getSessionParam('loggedUser');
        if ($loggedUser['op'] != 1) {
            $this->renderView('forbidden');
        }
        if (!empty($params)) {

            $userId = $params[0];
            $userModel = $this->getModel('users');
            $user = $userModel->findOne($userId);
            if (!empty($user)) {
                $this->request->setViewParam('user', $user, true);
                if ($this->request->isPost()) {
                    $userModel->delete([
                        ['id', '=', $userId]
                    ]);
                    $this->request->redirect(Utils::getURL('panel', 'users'));
                } else if ($this->request->isGet()) {
                    $this->renderView('delete');
                }
            }
        }
        $this->renderView('invalid');
    }

    public function actionEdit($params = [])
    {
        $userModel = $this->getModel('users');
        $userDepartmentsModel = $this->getModel('usersDepartments');
        $departmentsModel = $this->getModel('departments');
        $loggedUser = $this->request->getSessionParam('loggedUser');;
        $departmentList = $departmentsModel->find();
        if (isset($params[0])) {
            if ($loggedUser['op'] != 1) {
                $this->renderView('forbidden');
            }
            $user = $userModel->findOne($params[0]);
            if (empty($user)) {
                $this->renderView('invalid');
            }
            $userDepartments = $userDepartmentsModel->find([
                ["userId", '=', $user['id']]
            ], [
                "departmentId"
            ]);
            $user["departments"] = [];
            foreach ($userDepartments as $department) {
                $user["departments"][] = $department['departmentId'];
            }
        } else {
            $user = $loggedUser;
        }
        if ($this->request->isPost()) {
            $name = $this->request->getPostParam('name', true);
            $surname = $this->request->getPostParam('surname', true);
            $email = $this->request->getPostParam('email', true);
            $newPassword = $this->request->getPostParam('new-password', false);
            $newPasswordConfirm = $this->request->getPostParam('confirm-password', false);
            $currentPassword = $this->request->getPostParam('current-password', false);
            $op = $this->request->getPostParam('op', false);
            $departments = $this->request->getPostParam('departments');
            $errors = [];
            if (empty($name)) {
                $errors[] = 'The name is empty';
            }
            if (empty($surname)) {
                $errors[] = 'The surname is empty';
            }
            if (empty($email)) {
                $errors[] = 'The email is empty';
            } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'The email is invalid';
            }
            if (!empty($newPassword)) {
                if (empty($currentPassword) && $user['id'] == $loggedUser['id']) {
                    $errors[] = 'Current password is empty';
                }
                if (empty($newPasswordConfirm)) {
                    $errors[] = 'Password confirmation is empty';
                } else if ($newPassword != $newPasswordConfirm) {
                    $errors[] = 'Password do not match';
                }
                if (!password_verify($currentPassword, $user['password']) && $user['id'] == $loggedUser['id']) {
                    $errors[] = 'Invalid password';
                }
            }
            if (empty($errors)) {
                if ($user["email"] != $email) {
                    //Find if email is in use
                    $existing = $userModel->count([
                        ["email", '=', $email]
                    ]);
                    if ($existing === 1) {
                        $errors[] = "Email already in use";
                    }
                }
                if (empty($errors)) {
                    $data['name'] = $name;
                    $data['surname'] = $surname;
                    $data['email'] = $email;
                    if (!empty($newPassword)) {
                        $data['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
                    }
                    if ($loggedUser['op'] == 1 && $loggedUser['id'] != $user['id']) {
                        $data['op'] = intval(!empty($op));
                    }
                    $userModel->update($data, [
                        ['id', '=', $user['id']]
                    ]);
                    $userDepartmentsModel->delete([
                        ["userId", '=', $user['id']]
                    ]);
                    if (!empty($departments)) {
                        foreach ($departments as $department) {
                            $userDepartmentsModel->insert([
                                'userId' => $user['id'],
                                'departmentId' => $department
                            ]);
                        }
                    }
                    $this->request->redirect(Utils::getURL('user', 'edit', $params));
                }
            }
            $this->request->setViewParam('errors', $errors);
        }
        $this->request->setViewParam('user', $user, true);
        $this->request->setViewParam('departments', $departmentList, true);
        $this->renderView('edit');
    }

    public function actionForgot($params = [])
    {
        if (isset($params[0])) {
            if ($params[0] === 'completed' && $this->request->getSessionParam('forgot')) {
                $this->request->setSessionParam('forgot', false);
                $this->renderView('forgotCompleted');
            }
            $this->request->redirect(Utils::getURL("user", "login"));
        }
        if ($this->request->isPost()) {
            $email = $this->request->getPostParam('email', true);
            $errors = [];
            if (empty($email)) {
                $errors[] = 'Email is empty';
            } else {
                $userModel = $this->getModel('users');
                $exists = $userModel->findOne($email, 'email', ['id', 'name', 'surname']);
                if (!empty($exists)) {
                    try {
                        $recoverToken = bin2hex(random_bytes(32));
                        $recoverModel = $this->getModel('recovers');
                        $recoverModel->insert([
                            'id' => $recoverToken,
                            'userId' => $exists['id']
                        ]);
                        $link = Utils::getURL('user', 'recover', [$exists['id'], $recoverToken]);
                        $mailer = new \SimpleMailer();
                        $mailer->addTo($email, $exists['name'] . ' ' . $exists['surname']);
                        $mailer->addReplyTo(SITE_EMAIL);
                        $mailer->setFrom(SITE_EMAIL, SITE_TITLE);
                        $mailer->setSubject('Account Recovery');
                        $mailer->setMessage('Please click this link to recover your account ' . $link);
                        $mailer->send();
                        $this->request->setSessionParam('forgot', true);
                        $this->request->redirect(Utils::getURL("user", "forgot", ["completed"]));
                    } catch (\Exception $e) {
                        $errors[] = 'Internal server error';
                    }
                } else {
                    $errors[] = 'No user found';
                }
                $this->request->setViewParam('errors', $errors);
            }
        }
        $this->renderView('forgot');
    }

    public function actionLogin()
    {
        if ($this->request->isPost()) {
            $errors = [];
            $email = $this->request->getPostParam('email', true);
            $password = $this->request->getPostParam('password');
            if (empty($email)) {
                $errors[] = 'Email is empty';
            }
            if (empty($password)) {
                $errors[] = 'Password is empty';
            }
            if (empty($errors)) {
                $userModel = $this->getModel('users');
                $validationModel = $this->getModel('validations');
                $userModel->join($validationModel, 'id', 'userId', 'left');
                $user = $userModel->findOne($email, "email", [
                    [
                        "$userModel.id" => 'userId',
                        "$validationModel.id" => "validationId"
                    ],
                    "password"
                ]);
                if (!empty($user)) {
                    if (password_verify($password, $user["password"])) {
                        if (empty($user["validationId"])) {
                            try {
                                $sessionToken = bin2hex(random_bytes(32));
                                $sessionModel = $this->getModel('sessions');
                                $sessionModel->insert([
                                    'id' => $sessionToken,
                                    'userId' => $user['userId']
                                ]);
                                $this->request->setCookieParam('userToken', "$user[userId]-$sessionToken", time() + (3600 * 24 * 30));
                                $this->request->setSessionParam('loggedUser', $user['userId']);
                                $targetURL = $this->request->getSessionParam('targetURL');
                                if (!empty($targetURL)) {
                                    $this->request->redirect($targetURL);
                                } else {
                                    $this->request->redirect(Utils::getURL('panel'));
                                }
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
            $this->request->setViewParam('errors', $errors);
        }
        $this->renderView("login");
    }

    public function actionLogout($params = [])
    {
        $loggedUser = $this->request->getSessionParam('loggedUser');
        if (!empty($params)) {
            $userModel = $this->getModel('users');
            $user = $userModel->findOne($params[0]);
            if (empty($user)) {
                $this->renderView('invalid');
            } else if ($loggedUser["op"] == 0 && $loggedUser['id'] != $user['id']) {
                $this->renderView('forbidden');
            }
            if ($this->request->isGet()) {
                $this->request->setViewParam("user", $user);
                $this->renderView('logout');
            } else if ($this->request->isPost()) {
                $sessionModel = $this->getModel('sessions');
                $sessionModel->delete([
                    ['userId', '=', $user['id']]
                ]);
                if ($loggedUser['id'] != $user['id']) {
                    $this->request->redirect(Utils::getURL('user', 'edit', $params));
                }
            }
        }
        $this->request->redirect(Utils::getURL());
    }

    public function actionRecover($params = [])
    {
        if (isset($params[0])) {
            if ($params[0] === 'completed') {
                if ($this->request->getSessionParam('recovered')) {
                    $this->request->setSessionParam('recovered', false);
                    $this->renderView('recoverCompleted');
                }
                $this->request->redirect(Utils::getURL("user", "login"));
            }
        }
        if (count($params) === 2) {
            $user = $params[0];
            $key = $params[1];
            $recover = $this->getModel('recovers');
            $data = $recover->count([
                ['id', '=', $key],
                'AND',
                ['userId', '=', $user]
            ]);
            if ($data === 1) {
                $this->request->setViewParam('params', $params);
                if ($this->request->isGet()) {
                    $this->renderView('recover');
                }
                $errors = [];
                $password = $this->request->getPostParam("password");
                $confirm = $this->request->getPostParam("confirm");
                if (empty($password)) {
                    $errors[] = 'Password is empty';
                }
                if (empty($confirm)) {
                    $errors[] = 'Password confirmation is empty';
                }
                if ($password != $confirm) {
                    $errors[] = 'Passwords do not match';
                }
                if (empty($errors)) {
                    $userModel = $this->getModel('users');
                    $userModel->update(['password' => password_hash($password, PASSWORD_DEFAULT)], [
                        ['id', '=', $user]
                    ]);
                    $recover->delete([
                        ['id', '=', $key],
                        'AND',
                        ['userId', '=', $user]
                    ]);
                    $this->request->setSessionParam('recovered', true);
                    $this->request->redirect(Utils::getURL("user", "recover", ["completed"]));
                }
                $this->request->setViewParam('errors', $errors);
                $this->renderView('recover');
            }
        }
        $this->renderView('recoverInvalid');
    }

    public function actionRegister($params = [])
    {
        if (isset($params[0])) {
            if ($params[0] === 'completed' && $this->request->getSessionParam('registered')) {
                $this->request->setSessionParam('registered', false);
                $this->renderView('registerCompleted');
            }
            $this->request->redirect(Utils::getURL("user", "register"));
        }
        if ($this->request->isPost()) {
            $errors = [];
            $name = $this->request->getPostParam("name", true);
            $surname = $this->request->getPostParam("surname", true);
            $email = $this->request->getPostParam("email", true);
            $password = $this->request->getPostParam("password");
            $confirm = $this->request->getPostParam("confirm");
            $accept = $this->request->getPostParam('accept');

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
            if (!empty($accept)) {
                $errors[] = 'You need to accept the cookie policy';
            }
            if (empty($errors)) {
                $model = $this->getModel('users');
                //Find if email is in use
                $existing = $model->count([
                    ["email", '=', $email]
                ]);
                if ($existing === 0) {
                    try {
                        $validationCode = bin2hex(random_bytes(32));
                        $insertId = $model->insert([
                            "name" => $name,
                            "surname" => $surname,
                            "email" => $email,
                            "password" => password_hash($password, PASSWORD_DEFAULT)
                        ]);
                        $validation = $this->getModel('validations');
                        $validation->insert([
                            'id' => $validationCode,
                            'userId' => $insertId
                        ]);
                        $link = Utils::getURL('user', 'validate', [$insertId, $validationCode]);
                        $mailer = new \SimpleMailer();
                        $mailer->addTo($email, $name . ' ' . $surname);
                        $mailer->addReplyTo(SITE_EMAIL);
                        $mailer->setFrom(SITE_EMAIL, SITE_TITLE);
                        $mailer->setSubject('Account Validation');
                        $mailer->setMessage('Please click this link to validate your account ' . $link);
                        $mailer->send();
                        $this->request->setSessionParam('registered', true);
                        $this->request->redirect(Utils::getURL("user", "register", ["completed"]));
                    } catch (\Exception $e) {
                        $errors[] = 'Internal server error.';
                    }
                } else {
                    $errors[] = 'Email is already in use';
                }
            }
            $this->request->setViewParam('errors', $errors);
        }
        $this->renderView("register");
    }

    public function actionValidate($params = [])
    {
        if (count($params) === 2) {
            $user = $params[0];
            $key = $params[1];
            $validation = $this->getModel('validations');
            $data = $validation->count([
                ['id', '=', $key],
                'AND',
                ['userId', '=', $user]
            ]);
            if ($data === 1) {
                $validation->delete([
                    ['id', '=', $key],
                    'AND',
                    ['userId', '=', $user]
                ]);
                $this->renderView('validateCompleted');
            }
        }
        $this->renderView('validateInvalid');
    }
}