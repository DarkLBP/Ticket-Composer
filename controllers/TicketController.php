<?php
namespace Controllers;

use Core\Controller;
use Core\Utils;

class TicketController extends Controller
{
    public function actionAssign($params = [])
    {
        $loggedUser = $this->request->getSessionParam('loggedUser');
        $ticketsModel = $this->getModel('tickets');
        if (!empty($params)) {
            $ticketId = $params[0];
            $ticket = $ticketsModel->findOne($ticketId, 'id', ['department']);
            if (!empty($ticket)) {
                if (in_array($ticket['department'], $loggedUser['departments'])) {
                    $ticketsModel->update([
                        'assignedTo' => $loggedUser['id']
                    ], [
                        ['id', '=', $ticketId]
                    ]);
                    $this->request->redirect(Utils::getURL('ticket', 'view', $params));
                } else {
                    $this->renderView('forbidden');
                }
            }
        }
        $this->renderView('invalid');
    }

    public function actionCreate()
    {
        if ($this->request->isPost()) {
            $title = $this->request->getPostParam('title', true);
            $department = $this->request->getPostParam('department', true);
            $content = $this->request->getPostParam('content', true);
            $attachments = $this->request->getSentFile('attachment');
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
                if (!empty($attachments)) {
                    $ticketAttachment = $this->getModel('attachments');
                    $count = count($attachments['error']);
                    for ($i = 0; $i < $count; $i++) {
                        if ($attachments["error"][$i] === 0) {
                            $file = $this->uploadFile($attachments['tmp_name'][$i], $attachments["name"][$i]);
                            $ticketAttachment->insert([
                                'postId' => $ticketPostId,
                                'fileName' => $attachments["name"][$i],
                                'filePath' => $file
                            ]);
                        }
                    }
                }
                $this->request->redirect(Utils::getURL('ticket', 'view', [$ticketId]));
            }
            $this->request->setViewParam('errors', $errors);
        }
        $departmentModel = $this->getModel('departments');
        $departments = $departmentModel->find();
        $this->request->setViewParam('departments', $departments);
        $this->renderView('create');
    }

    public function actionClose($params = []) {
        if (isset($params[0])) {
            $ticketId = $params[0];
            $ticketsModel = $this->getModel('tickets');
            $ticket = $ticketsModel->count([
                ['id', '=', $ticketId]
            ]);
            if ($ticket === 1) {
                $ticketsModel->update([
                    "open" => 0
                ], [
                    ['id', '=', $ticketId]
                ]);
                $this->request->redirect(Utils::getURL('ticket', 'view', $params));
            }
        }
        $this->renderView('invalid');
    }

    public function actionOpen($params = []) {
        if (isset($params[0])) {
            $ticketId = $params[0];
            $ticketsModel = $this->getModel('tickets');
            $ticket = $ticketsModel->count([
                ['id', '=', $ticketId]
            ]);
            if ($ticket === 1) {
                $ticketsModel->update([
                    "open" => 1
                ], [
                    ['id', '=', $ticketId]
                ]);
                $this->request->redirect(Utils::getURL('ticket', 'view', $params));
            }
        }
        $this->renderView('invalid');
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
            $ticketsModel->join($usersModel, 'assignedTo', 'id', 'left', 'u2');
            $ticket = $ticketsModel->findOne($ticketId, "$ticketsModel.id", [
                "$ticketsModel.*",
                [
                    "$usersModel.name" => "createdName",
                    "$usersModel.surname" => "createdSurname",
                    "$departmentsModel.name" => "departmentName",
                    "u2.name" => "assignedName",
                    "u2.surname" => "assignedSurname",
                ]
            ]);
            if (!empty($ticket)) {
                //Retrieve ticket's posts
                $ticketPostsModel = $this->getModel('posts');
                $ticketPostsModel->join($usersModel, 'userId', 'id', 'left');
                $ticketPosts = $ticketPostsModel->find([
                    ['ticketId', '=', $ticketId]
                ], [
                    "$ticketPostsModel.*",
                    [
                        "$usersModel.name" => "createdName",
                        "$usersModel.surname" => "createdSurname",
                        "$usersModel.id" => "createdId"
                    ]
                ], [], [
                    "$ticketPostsModel.created" => 'asc'
                ]);

                //Retrieve attachments
                $attachmentsModel = $this->getModel('attachments');
                foreach ($ticketPosts as &$post) {
                    $post['attachments'] = $attachmentsModel->find([
                        ['postId', '=', $post['id']]
                    ]);
                }
                $this->request->setViewParam('ticket', $ticket);
                $this->request->setViewParam('ticketPosts', $ticketPosts);
                $this->renderView('ticket');
            }
        }
        $this->renderView('invalid');
    }

    private function uploadFile($tmp_name, $name)
    {
        $fileHash = hash_file('sha256', $tmp_name);
        $folderName = date('Y-m');
        $extension = pathinfo($name, PATHINFO_EXTENSION);
        $fileName = time() . "-" . $fileHash . (!empty($extension) ? '.' . $extension : '');
        $relativePath = $folderName . '/' . $fileName;
        $fullDir = __DIR__ . '/../uploads/' . $folderName . '/';
        if (!is_dir($fullDir)) {
            mkdir($fullDir, 0755, true);
        }
        $fullPath = $fullDir . $fileName;
        move_uploaded_file($tmp_name, $fullPath);
        return $relativePath;
    }
}