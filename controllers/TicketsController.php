<?php

namespace Controllers;

use Core\Controller;
use Core\Utils;
use Models\DepartmentsModel;
use Models\TicketsModel;
use Models\TicketsPostsModel;

class TicketsController extends Controller
{
    public function actionIndex()
    {
        echo "Here should appear a list of tickets if there are any";
    }

    public function actionCreate()
    {
        if ($this->request->isPost()) {
            $title = $this->request->getPostParam('title', true);
            $department = $this->request->getPostParam('department', true);
            $content = $this->request->getPostParam('content', true);
            $errors = [];
            if (empty($title)) {
                $errors[] = "The title is empty";
            }
            if (empty($department)) {
                $errors[] = "You have not selected a department";
            }
            if (empty($content)) {
                $errors[] = "You have not wrote any content";
            }
            if (empty($errors)) {
                $userId = $this->request->getSessionParam('loggedUser');
                $ticketModel = new TicketsModel();
                $ticketId = $ticketModel->insert([
                    'title' => $title,
                    'createdBy' => $userId,
                    'department' => $department,
                ]);
                $ticketPostModel = new TicketsPostsModel();
                $ticketPostModel->insert([
                    'ticketId' => $ticketId,
                    'userId' => $userId,
                    'content' => $content
                ]);
                $this->request->redirect(Utils::getURL('tickets', 'view', [$ticketId]));
            }
            $this->request->setViewParam('errors', $errors);
        }
        $departmentModel = new DepartmentsModel();
        $departments = $departmentModel->find();
        $this->request->setViewParam('departments', $departments);
        $this->renderView('create');
    }

    public function actionView()
    {
        //TODO View the given ticket :D
    }
}