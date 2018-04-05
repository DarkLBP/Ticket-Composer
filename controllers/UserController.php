<?php
namespace Controllers;

use Core\Controller;

class UserController extends Controller
{
    public function actionLogin() {
        if ($this->request->isPost()) {
            var_dump($_POST);
        } else {
            $this->renderView("login");
        }
    }

    public function actionRegister() {
        if ($this->request->isPost()) {
            var_dump($_POST);
        } else {
            $this->renderView("register");
        }
    }
}