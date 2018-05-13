<?php
namespace Controllers;

require_once __DIR__ . '/../vendor/SimpleMailer.php';

use Core\Controller;
use Core\Utils;

class PostController extends Controller
{
    public function actionDelete($params = []) {
        if (isset($params[0])) {
            $postId = $params[0];
            $postsModel = $this->getModel('posts');
            $post = $postsModel->findOne($postId, 'id');
            if (!empty($post)) {
                if ($this->request->getSessionParam('loggedUser')['op'] != 1) {
                    $this->renderView('forbidden');
                }
                if ($this->request->isGet()) {
                    $this->request->setViewParam('post', $post);
                    $this->renderView('delete');
                } else if ($this->request->isPost()) {
                    $attachmentsModel = $this->getModel('attachments');
                    $postAttachments = $attachmentsModel->find([
                        ['postId', '=', $postId]
                    ]);
                    foreach ($postAttachments as $attachment) {
                        $path = __DIR__ . '/../uploads/' . $attachment['filePath'];
                        unset($path);
                    }
                    $attachmentsModel->delete([
                        ['postId', '=', $postId]
                    ]);
                    $postsModel->delete([
                        ['id', '=', $postId]
                    ]);
                    $this->request->redirect(Utils::getURL('ticket', 'view', [$post['ticketId']]));
                } else {
                    $this->renderView('error');
                }
            }
        }
        $this->renderView('invalid');
    }

    public function actionEdit($params = [])
    {
        if (isset($params[0])) {
            $postId = $params[0];
            $postsModel = $this->getModel('posts');
            $post = $postsModel->findOne($postId, 'id');
            $loggedUser = $this->request->getSessionParam('loggedUser');
            if ($post['userId'] !== $loggedUser['id'] && $loggedUser['op'] == 0) {
                $this->renderView('forbidden');
            }
            if (!empty($post)) {
                if ($this->request->isGet()) {
                    $this->request->setViewParam('post', $post);
                    $this->renderView('edit');
                } else if ($this->request->isPost()) {
                    $message = $this->request->getPostParam('message', true);
                    if (!empty($message)) {
                        $postsModel->update([
                            'content' => $message
                        ], [
                            ['id', '=', $postId]
                        ]);
                        $this->request->redirect(Utils::getURL('ticket', 'view', [$post['ticketId']]));
                    }
                    $this->request->setViewParam('post', $post);
                    $this->request->setViewParam('error', 'Message is empty');
                    $this->renderView('edit');
                } else {
                    $this->renderView('error');
                }
            }
        }
        $this->renderView('invalid');
    }

    public function actionCreate($params = [])
    {
        if (isset($params[0])) {
            $ticketId = $params[0];
            $ticketsModel = $this->getModel('tickets');
            $exists = $ticketsModel->findOne($ticketId);
            if (!empty($exists) && $exists['open'] == 1) {
                $content = $this->request->getPostParam('message', true);
                $attachments = $this->request->getSentFile('attachment');
                $close = $this->request->getPostParam('close');
                if (!empty($content)) {
                    $postsModel = $this->getModel('posts');
                    $loggedUser = $this->request->getSessionParam('loggedUser');
                    $postId = $postsModel->insert([
                        'ticketId' => $ticketId,
                        'userId' => $loggedUser['id'],
                        'content' => $content
                    ]);
                    if (!empty($attachments)) {
                        $ticketAttachment = $this->getModel('attachments');
                        $count = count($attachments['error']);
                        for ($i = 0; $i < $count; $i++) {
                            if ($attachments["error"][$i] === 0) {
                                $file = $this->uploadFile($attachments['tmp_name'][$i], $attachments["name"][$i]);
                                $ticketAttachment->insert([
                                    'postId' => $postId,
                                    'fileName' => $attachments["name"][$i],
                                    'filePath' => $file
                                ]);
                            }
                        }
                    }
                    if (!empty($close)) {
                        $ticketsModel->update([
                            "open" => 0,
                        ], [
                            ["id", '=', $ticketId]
                        ]);
                    }
                    //Send email to the assigned person
                    if (!empty($exists['assignedTo'])) {
                        if ($loggedUser['id'] != $exists['assignedTo']) {
                            $usersModel = $this->getModel('users');
                            $user = $usersModel->findOne($exists['assignedTo']);
                            if (!empty($user)) {
                                $mailer = new \SimpleMailer();
                                $mailer->addTo($user['email'], $user['name'] . ' ' . $user['surname']);
                                $mailer->addReplyTo(SITE_EMAIL);
                                $mailer->setFrom(SITE_EMAIL, SITE_TITLE);
                                $mailer->setSubject('[Ticket #' . $ticketId . '] ' . $exists['title']);
                                $mailer->setMessage('New answer by ' . $loggedUser["name"] . " " . $loggedUser["surname"] .
                                ' on ticket ' . Utils::getURL('ticket', 'view', [$ticketId]));
                                $mailer->send();
                            }
                        }
                    }
                } else {
                    $this->request->setSessionParam('postError', 'Message is empty');
                }
                $this->request->redirect(Utils::getURL('ticket', 'view', [$ticketId]));
            }
        }
        $this->renderView('forbidden');
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