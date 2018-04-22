<?php
namespace Controllers;

use Core\Controller;
use Core\Utils;
use Models\DepartmentsModel;
use Models\TicketsAttachmentsModel;
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

    public function actionView()
    {
        //TODO View the given ticket :D
    }
}