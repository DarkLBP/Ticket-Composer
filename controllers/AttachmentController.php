<?php
namespace Controllers;

use Core\Controller;

class AttachmentController extends Controller
{
    public function actionDownload($params = [])
    {
        if (isset($params[0])) {
            $attachmentId = $params[0];
            $attachmentModel = $this->getModel('attachments');
            $postModel = $this->getModel('posts');
            $attachmentModel->join($postModel, 'postId', 'id');
            $attachment = $attachmentModel->findOne($attachmentId, "$attachmentModel.id", [
                ["$postModel.userId" => 'userId'],
                "$attachmentModel.*"
            ]);
            if (!empty($attachment)) {
                $userId = $attachment['userId'];
                if ($userId != $this->request->getSessionParam('loggedUser')['id']) {
                    $this->renderView('forbidden');
                }
                $filePath = '../uploads/'.$attachment['filePath'];
                $mimeType = mime_content_type($filePath);
                $this->request->setResponseHeader('Content-Type', $mimeType);
                $this->request->setResponseHeader("Content-Disposition", "attachment; filename=$attachment[fileName]");
                readfile($filePath);
                exit;
            }
        }
        $this->renderView('notFound');
    }
}