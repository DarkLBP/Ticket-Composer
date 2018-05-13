<?php
namespace Controllers;

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
            if ($post['userId'] !== $this->request->getSessionParam('loggedUser')['id']) {
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
            $exists = $ticketsModel->findOne($ticketId, ["open"]);
            if (!empty($exists) && $exists['open'] == 1) {
                $content = $this->request->getPostParam('message', true);
                $attachments = $this->request->getSentFile('attachment');
                $close = $this->request->getPostParam('close');
                if (!empty($content)) {
                    $ticketPostsModel = $this->getModel('posts');
                    $postId = $ticketPostsModel->insert([
                        'ticketId' => $ticketId,
                        'userId' => $this->request->getSessionParam('loggedUser')['id'],
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