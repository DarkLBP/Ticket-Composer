<?php

namespace Controllers;

use Core\Controller;
use Core\Utils;

class PanelController extends Controller
{
    public function actionIndex()
    {
        $this->renderView('index');
    }

    public function actionDepartments()
    {
        $departmentsModel = $this->getModel('departments');
        $departments = $departmentsModel->find();
        $this->request->setViewParam('departments', $departments);
        $this->renderView('departments');
    }

    public function actionSite()
    {
        if ($this->request->isPost()) {
            $title = $this->request->getPostParam('title', true);
            $siteEmail = $this->request->getPostParam('siteemail', true);
            $errors = [];
            if (empty($title)) {
                $errors[] = 'Site title is empty';
            }
            if (empty($siteEmail)) {
                $errors[] = 'Site email is empty';
            } else if (!filter_var($siteEmail, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Site email is invalid';
            }
            if (empty($errors)) {
                $configString = "<?php
define(\"SITE_TITLE\", \"$title\");
define(\"SITE_EMAIL\", \"$siteEmail\");
define(\"DATABASE_HOST\", \"" . DATABASE_HOST . "\");
define(\"DATABASE_USER\", \"" . DATABASE_USER . "\");
define(\"DATABASE_PASSWORD\", \"" . DATABASE_PASSWORD . "\");
define(\"DATABASE_DB\", \"" . DATABASE_DB. "\");
define(\"DEFAULT_CONTROLLER\", \"main\");
define(\"DEFAULT_ACTION\", \"index\");

spl_autoload_register(function (\$class) {
    \$segments = explode(\"\\\\\", \$class);
    \$path = '';
    for (\$i = 0; \$i < count(\$segments) - 1; \$i++) {
        \$path .= strtolower(\$segments[\$i]) . '/';
    }
    \$finalPath = __DIR__ . '/../' . \$path . '/' . \$segments[\$i] . '.php';
    if (file_exists(\$finalPath)) {
        include_once \$finalPath;
    }
});
                    ";
                file_put_contents(__DIR__ . '/../core/Config.php', $configString);
                $this->request->setViewParam('message', "Site settings updated successfully");
                $this->request->setResponseHeader('Location', Utils::getURL('panel', 'site'));
            } else {
                $this->request->setViewParam('errors', $errors);
            }
        }
        $this->renderView('site');
    }

    public function actionTickets()
    {
        $ticketsModel = $this->getModel('tickets');
        $departmentsModel = $this->getModel('departments');
        $postModel = $this->getModel('posts');
        $ticketsModel->join($departmentsModel, 'department', 'id', 'left');
        $ticketsModel->join($postModel, 'id', 'ticketId', 'inner');
        $userId = $this->request->getSessionParam('loggedUser')['id'];
        $tickets = $ticketsModel->find([
            'createdBy' => $userId
        ], [
            "$ticketsModel.id",
            "$ticketsModel.title",
            "$ticketsModel.open",
            [
                "count(*)" => "totalPosts",
                "max($postModel.created)" => "lastReply",
                "$departmentsModel.name" => "departmentName"
            ]
        ],[
            "$ticketsModel.id"
        ], [
            'lastReply' => 'desc'
        ]);
        $this->request->setViewParam('myTickets', $tickets);
        $this->renderView('tickets');
    }

    public function actionUsers()
    {
        $usersModel = $this->getModel('users');
        $users = $usersModel->find();
        $this->request->setViewParam('users', $users);
        $this->renderView('users');
    }
}