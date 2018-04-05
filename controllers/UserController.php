<?php
namespace Controllers;

use Core\Controller;
use Models\UsersModel;

class UserController extends Controller
{
    public function actionLogin() {
        if ($this->request->isPost()) {
            if (!$this->request->hasPostParam([
                "email", "password"
            ])) {
               $error = "Missing data";
            } else {
                $email = $this->request->getPostParam("email");
                $password = $this->request->getPostParam("password");
                $model = new UsersModel();
                $user = $model->findOne($email, "email");
                if (!empty($user)) {
                    if (password_verify($password, $user["password"])) {
                        die("Okey");
                    }
                }
                $error = "Invalid credentials";
            }
            $this->renderView("login", [
                "error" => $error
            ]);
        } else {
            $this->renderView("login");
        }
    }

    public function actionRegister() {
        if ($this->request->isPost()) {
            $error = "";
            if (!$this->request->hasPostParam([
                "name", "surname", "email", "password", "confirm"
            ])) {
                $error = "Missing data";
            } else {
                $name = $this->request->getPostParam("name");
                $surname = $this->request->getPostParam("surname");
                $email = $this->request->getPostParam("email");
                $password = $this->request->getPostParam("password");
                $confirm = $this->request->getPostParam("confirm");

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error = "Invalid email";
                } else if ($password !== $confirm) {
                    $error = "Passwords do not match";
                }

                if (empty($error)) {
                    $model = new UsersModel();
                    $model->insert([
                        "name" => $name,
                        "surname" => $surname,
                        "email" => $email,
                        "password" => password_hash($password, PASSWORD_DEFAULT)
                    ]);
                }
            }
            $this->renderView("register", [
                "error" => $error
            ]);
        } else {
            $this->renderView("register");
        }
    }
}