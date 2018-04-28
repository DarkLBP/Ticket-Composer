<?php
namespace Controllers;

use Core\Controller;
use Core\Utils;

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
                $errors[] = "You have not written any content";
            }
            if (empty($errors)) {
                $user = $this->request->getSessionParam('loggedUser');
                $ticketModel = $this->getModel('tickets');
                $ticketId = $ticketModel->insert([
                    'title' => $title,
                    'createdBy' => $user['id'],
                    'department' => $department,
                ]);
                $ticketPostModel = $this->getModel('posts');
                $ticketPostId = $ticketPostModel->insert([
                    'ticketId' => $ticketId,
                    'userId' => $user['id'],
                    'content' => $content
                ]);
                if (!empty($attachment)) {
                    $file = $this->uploadFile($attachment);
                    $ticketAttachment = $this->getModel('attachments');
                    $ticketAttachment->insert([
                        'postId' => $ticketPostId,
                        'fileName' => $attachment['name'],
                        'filePath' => $file
                    ]);
                }
                $this->request->redirect(Utils::getURL('tickets', 'view', [$ticketId]));
            }
            $this->request->setViewParam('errors', $errors);
        }
        $departmentModel = $this->getModel('departments');
        $departments = $departmentModel->find();
        $this->request->setViewParam('departments', $departments);
        $this->renderView('create');
    }

    public function actionDeletePost($params = []) {
        if (isset($params[0])) {
            $postId = $params[0];
            $postsModel = $this->getModel('posts');
            $post = $postsModel->findOne($postId, 'id');
            if ($post['userId'] !== $this->request->getSessionParam('loggedUser')) {
                $this->renderView('notYours');
            }
            if (!empty($post)) {
                if ($this->request->isGet()) {
                    $this->request->setViewParam('post', $post);
                    $this->renderView('postDelete');
                } else if ($this->request->isPost()) {
                    $postsModel->delete([
                        'id' => $postId
                    ]);
                    $this->request->redirect(Utils::getURL('tickets', 'view', [$post['ticketId']]));
                } else {
                    $this->renderView('error');
                }
            }
        }
        $this->renderView('invalidPost');
    }

    public function actionEditPost($params = [])
    {
        if (isset($params[0])) {
            $postId = $params[0];
            $postsModel = $this->getModel('posts');
            $post = $postsModel->findOne($postId, 'id');
            if ($post['userId'] !== $this->request->getSessionParam('loggedUser')) {
                $this->renderView('notYours');
            }
            if (!empty($post)) {
                if ($this->request->isGet()) {
                    $this->request->setViewParam('post', $post);
                    $this->renderView('postEdit');
                } else if ($this->request->isPost()) {
                    $message = $this->request->getPostParam('message', true);
                    if (!empty($message)) {
                        $postsModel->update([
                            'content' => $message
                        ], [
                            'id' => $postId
                        ]);
                        $this->request->redirect(Utils::getURL('tickets', 'view', [$post['ticketId']]));
                    }
                    $this->request->setViewParam('error', 'Message is empty');
                    $this->renderView('postEdit');
                } else {
                    $this->renderView('error');
                }
            }
        }
        $this->renderView('invalidPost');
    }

    public function actionIndex()
    {
        $ticketsModel = $this->getModel('tickets');
        $departmentsModel = $this->getModel('departments');
        $ticketsModel->join($departmentsModel, 'department', 'id', 'left');
        $tickets = $ticketsModel->find([], [
            "$ticketsModel.*",
            ["$departmentsModel.name", "departmentName"]
        ], [
            'created' => 'desc'
        ]);
        $this->request->setViewParam('tickets', $tickets);
        $this->renderView('index');
    }

    public function actionPost($params = [])
    {
        if (isset($params[0])) {
            $ticketId = $params[0];
            $ticketsModel = $this->getModel('tickets');
            $exists = $ticketsModel->count(['id' => $ticketId]);
            if ($exists === 1) {
                $content = $this->request->getPostParam('message', true);
                if (!empty($content)) {
                    $ticketPostsModel = $this->getModel('posts');
                    $ticketPostsModel->insert([
                        'ticketId' => $ticketId,
                        'userId' => $this->request->getSessionParam('loggedUser'),
                        'content' => $content
                    ]);
                } else {
                    $this->request->setSessionParam('postError', 'Message is empty');
                }
                $this->request->redirect(Utils::getURL('tickets', 'view', [$ticketId]));
            }
        }
        $this->renderView('invalidTicket');
    }

    public function actionView($params = [])
    {
        $error = $this->request->getSessionParam('postError');
        if (!empty($error)) {
            $this->request->setViewParam('error', $error);
            $this->request->setSessionParam('postError');
        }
        if (isset($params[0])) {
            $ticketId = $params[0];
            $ticketsModel = $this->getModel('tickets');
            $usersModel = $this->getModel('users');
            $departmentsModel = $this->getModel('departments');
            $ticketsModel->join($usersModel, 'createdBy', 'id', 'left');
            $ticketsModel->join($departmentsModel, 'department', 'id', 'left');
            $ticket = $ticketsModel->findOne($ticketId, "$ticketsModel.id", [
                "$ticketsModel.*",
                ["$usersModel.name", "createdName"],
                ["$usersModel.surname", "createdSurname"],
                ["$departmentsModel.name", "departmentName"]
            ]);
            if (!empty($ticket)) {
                $ticketPostsModel = $this->getModel('posts');
                $ticketPostsModel->join($usersModel, 'userId', 'id', 'left');
                $ticketPosts = $ticketPostsModel->find(['ticketId' => $ticketId], [
                    "$ticketPostsModel.*",
                    ["$usersModel.name", "createdName"],
                    ["$usersModel.surname", "createdSurname"],
                    ["$usersModel.id", "createdId"]
                ], [
                    "$ticketPostsModel.created" => 'asc'
                ]);
                $this->request->setViewParam('ticket', $ticket);
                $this->request->setViewParam('ticketPosts', $ticketPosts);
                $this->renderView('ticket');
            }
        }
        $this->renderView('invalidTicket');
    }

    private function uploadFile($file)
    {
        $fileHash = hash_file('sha256', $file['tmp_name']);
        $folderName = date('Y-m');
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = $fileHash . (!empty($extension) ? '.' . $extension : '');
        $relativePath = $folderName . '/' . $fileName;
        $fullDir = __DIR__ . '/../uploads/' . $folderName . '/';
        if (!is_dir($fullDir)) {
            mkdir($fullDir, 0755, true);
        }
        $fullPath = $fullDir . $fileName;
        move_uploaded_file($file['tmp_name'], $fullPath);
        return $relativePath;
    }
}