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