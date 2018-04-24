<?php
namespace Controllers;

use Core\Controller;
use Core\Utils;
use Models\DepartmentsModel;
use Models\TicketsAttachmentsModel;
use Models\TicketsModel;
use Models\TicketsPostsModel;
use Models\UsersModel;

class TicketsController extends Controller
{
    public function actionCreate()
    {
        if ($this->request->isPost()) {
            $title = $this->request->getPostParam('title', true);
            $department = $this->request->getPostParam('department', true);
            $content = $this->request->getPostParam('content', true);
            $attachment = $this->request->getSentFile('attachment');
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
                $ticketPostId = $ticketPostModel->insert([
                    'ticketId' => $ticketId,
                    'userId' => $userId,
                    'content' => $content
                ]);
                if (!empty($attachment)) {
                    $file = $this->uploadFile($attachment);
                    $ticketAttachment = new TicketsAttachmentsModel();
                    $ticketAttachment->insert([
                        'postId' => $ticketPostId,
                        'filePath' => $file
                    ]);
                }
                $this->request->redirect(Utils::getURL('tickets', 'view', [$ticketId]));
            }
            $this->request->setViewParam('errors', $errors);
        }
        $departmentModel = new DepartmentsModel();
        $departments = $departmentModel->find();
        $this->request->setViewParam('departments', $departments);
        $this->renderView('create');
    }

    public function actionIndex()
    {
        $ticketsModel = new TicketsModel();
        $departmentsModel = new DepartmentsModel();
        $ticketsModel->join($departmentsModel, 'department', 'id', 'left');
        $tickets = $ticketsModel->find([], [
            "$ticketsModel->tableName.*",
            ["$departmentsModel->tableName.name", "departmentName"]
        ]);
        $this->request->setViewParam('tickets', $tickets);
        $this->renderView('index');
    }

    private function uploadFile($file)
    {
        $fileHash = hash_file('sha256', $file['tmp_name']);
        $folderName = date('Y-m');
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = $fileHash . (!empty($extension) ? '.' . $extension : '');
        $relativePath = $folderName . '/' . $fileName;
        $fullDir = __DIR__ . '/../site/uploads/' . $folderName . '/';
        if (!is_dir($fullDir)) {
            mkdir($fullDir, 0755, true);
        }
        $fullPath = $fullDir . $fileName;
        move_uploaded_file($file['tmp_name'], $fullPath);
        return $relativePath;
    }

    public function actionView($params = [])
    {
        if (isset($params[0])) {
            $ticketId = $params[0];
            $ticketsModel = new TicketsModel();
            $usersModel = new UsersModel();
            $departmentsModel = new DepartmentsModel();
            $ticketsModel->join($usersModel, 'createdBy', 'id', 'left');
            $ticketsModel->join($departmentsModel, 'department', 'id', 'left');
            $ticket = $ticketsModel->findOne($ticketId, "$ticketsModel->tableName.id", [
                "$ticketsModel->tableName.*",
                ["$usersModel->tableName.name", "createdName"],
                ["$usersModel->tableName.surname", "createdSurname"],
                ["$departmentsModel->tableName.name", "departmentName"]
            ]);
            if (!empty($ticket)) {
                $ticketPostsModel = new TicketsPostsModel();
                $ticketPostsModel->join($usersModel, 'userId', 'id', 'left');
                $ticketPosts = $ticketPostsModel->find(['ticketId' => $ticketId], [
                    "$ticketPostsModel->tableName.*",
                    ["$usersModel->tableName.name", "createdName"],
                    ["$usersModel->tableName.surname", "createdSurname"],
                    ["$usersModel->tableName.id", "createdId"]
                ]);
                $this->request->setViewParam('ticket', $ticket);
                $this->request->setViewParam('ticketPosts', $ticketPosts);
                $this->renderView('ticket');
            }
        }
        $this->renderView('invalidTicket');
    }
}