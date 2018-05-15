<?php

namespace Controllers;

use Core\Controller;
use Core\Utils;

class PanelController extends Controller
{
    /**
     * Show intro message and some statistics
     */
    public function actionIndex()
    {
        $loggedUser = $this->request->getSessionParam('loggedUser');
        $ticketsModel = $this->getModel('tickets');
        $totalTickets = $ticketsModel->count([
            ['createdBy', '=', $loggedUser['id']]
        ]);
        $postModel = $this->getModel('posts');
        $totalPosts = $postModel->count([
            ['userId', '=', $loggedUser['id']]
        ]);
        $recentPost = $postModel->findOne($loggedUser["id"], 'userId', [], ["created" => "desc"]);
        $this->request->setViewParam('totalTickets', $totalTickets);
        $this->request->setViewParam('totalPosts', $totalPosts);
        $this->request->setViewParam('recentPost', $recentPost);
        $this->renderView('index');
    }

    /**
     * Shows departments table
     */
    public function actionDepartments()
    {
        $searchTerm = $this->request->getGetParam('search', true);
        $page = $this->request->getGetParam('page', true);
        $departmentsModel = $this->getModel('departments');
        $sortBy = $this->request->getGetParam('sort', true);
        $sortOrder = $this->request->getGetParam('order', true);
        $matches = [];
        if (!empty($searchTerm)) {
            $matches[] = '(';
            $matches[] = ["id", "LIKE", "%$searchTerm%"];
            $matches[] = 'OR';
            $matches[] = ["name", "LIKE", "%$searchTerm%"];
            $matches[] = ')';
        }
        if (empty($page) || !is_numeric($page)) {
            $page = 0;
        } else {
            $page--;
        }
        if (empty($sortBy) || empty($sortOrder)) {
            $sort = [
                'id' => 'desc'
            ];
        } else {
            $sort = [
                $sortBy => $sortOrder
            ];
        }
        try {
            $departments = $departmentsModel->find($matches, [], [], $sort, 25, $page * 25);
            $departmentCount = $departmentsModel->count($matches);
        } catch (\PDOException $e) {
            $departments = [];
            $departmentCount = 0;
        }
        $this->request->setViewParam('departments', $departments, true);
        $this->request->setViewParam('sortOrder', $sortOrder, true);
        $this->request->setViewParam('sortBy', $sortBy, true);
        $this->request->setViewParam('searchTerm', $searchTerm, true);
        $this->request->setViewParam('page', ++$page, true);
        $this->request->setViewParam('morePages', $page * 25 < $departmentCount);
        $this->renderView('departments');
    }

    /**
     * Shows site options
     */
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
define(\"DATABASE_DB\", \"" . DATABASE_DB . "\");
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

    /**
     * Shows tickets table
     */
    public function actionTickets()
    {
        $searchTerm = $this->request->getGetParam('search', true);
        $page = $this->request->getGetParam('page', true);
        $sortBy = $this->request->getGetParam('sort', true);
        $sortOrder = $this->request->getGetParam('order', true);
        $ticketsModel = $this->getModel('tickets');
        $departmentsModel = $this->getModel('departments');
        $postModel = $this->getModel('posts');
        $ticketsModel->join($departmentsModel, 'department', 'id', 'left');
        $ticketsModel->join($postModel, 'id', 'ticketId', 'inner');
        $loggedUser = $this->request->getSessionParam('loggedUser');
        $matches = [];
        if (!empty($searchTerm)) {
            $matches[] = '(';
            $matches[] = ["$ticketsModel.id", "LIKE", "%$searchTerm%"];
            $matches[] = 'OR';
            $matches[] = ["$ticketsModel.title", "LIKE", "%$searchTerm%"];
            $matches[] = 'OR';
            $matches[] = ["$ticketsModel.open", "LIKE", "%$searchTerm%"];
            $matches[] = 'OR';
            $matches[] = ["$departmentsModel.name", "LIKE", "%$searchTerm%"];
            $matches[] = ')';
        }
        if (empty($page) || !is_numeric($page)) {
            $page = 0;
        } else {
            $page--;
        }
        if ($loggedUser['op'] == 0) {
            if (!empty($matches)) {
                $matches[] = 'AND';
            }
            $matches[] = ['createdBy', '=', $loggedUser["id"]];
            if (!empty($loggedUser["departments"])) {
                $matches[] = 'OR';
                $matches[] = ["$ticketsModel.department", 'IN', $loggedUser["departments"]];
            }
        }
        if (empty($sortBy) || empty($sortOrder)) {
            $sort = [
                'lastReply' => 'desc'
            ];
        } else {
            $sort = [
                $sortBy => $sortOrder
            ];
        }
        try {
            $tickets = $ticketsModel->find($matches, [
                "$ticketsModel.id",
                "$ticketsModel.title",
                "$ticketsModel.open",
                [
                    "count(*)" => "totalPosts",
                    "max($postModel.created)" => "lastReply",
                    "$departmentsModel.name" => "departmentName"
                ]
            ], [
                "$ticketsModel.id"
            ], $sort, 25, 25 * $page);
            $ticketCount = $ticketsModel->count($matches);
        } catch (\PDOException $e) {
            $tickets = [];
            $ticketCount = 0;
        }
        $this->request->setViewParam('tickets', $tickets, true);
        $this->request->setViewParam('sortOrder', $sortOrder, true);
        $this->request->setViewParam('sortBy', $sortBy, true);
        $this->request->setViewParam('searchTerm', $searchTerm, true);
        $this->request->setViewParam('page', ++$page, true);
        $this->request->setViewParam('morePages', $page * 25 < $ticketCount);
        $this->renderView('tickets');
    }

    /**
     * Shows users table
     */
    public function actionUsers()
    {
        $searchTerm = $this->request->getGetParam('search', true);
        $page = $this->request->getGetParam('page', true);
        $usersModel = $this->getModel('users');
        $sortBy = $this->request->getGetParam('sort', true);
        $sortOrder = $this->request->getGetParam('order', true);
        $matches = [];
        if (!empty($searchTerm)) {
            $matches[] = '(';
            $matches[] = ["id", "LIKE", "%$searchTerm%"];
            $matches[] = 'OR';
            $matches[] = ["name", "LIKE", "%$searchTerm%"];
            $matches[] = 'OR';
            $matches[] = ["surname", "LIKE", "%$searchTerm%"];
            $matches[] = 'OR';
            $matches[] = ["email", "LIKE", "%$searchTerm%"];
            $matches[] = 'OR';
            $matches[] = ["created", "LIKE", "%$searchTerm%"];
            $matches[] = ')';
        }
        if (empty($page) || !is_numeric($page)) {
            $page = 0;
        } else {
            $page--;
        }
        if (empty($sortBy) || empty($sortOrder)) {
            $sort = [
                'created' => 'desc'
            ];
        } else {
            $sort = [
                $sortBy => $sortOrder
            ];
        }
        try {
            $users = $usersModel->find($matches, [], [], $sort, 25, $page * 25);
            $userCount = $usersModel->count($matches);
        } catch (\PDOException $e) {
            $users = [];
            $userCount = 0;
        }
        $this->request->setViewParam('users', $users, true);
        $this->request->setViewParam('sortOrder', $sortOrder, true);
        $this->request->setViewParam('sortBy', $sortBy, true);
        $this->request->setViewParam('searchTerm', $searchTerm, true);
        $this->request->setViewParam('page', ++$page, true);
        $this->request->setViewParam('morePages', $page * 25 < $userCount);
        $this->renderView('users');
    }
}