<?php
namespace Controllers;

use Core\Controller;
use Models\AttachmentsModel;
use Models\PostsModel;

class AttachmentController extends Controller
{
    public function actionDownload($params = [])
    {
        if (isset($params[0])) {
            $attachmentId = $params[0];
            $attachmentModel = new AttachmentsModel();
            $postModel = new PostsModel();
            $attachmentModel->join($postModel, 'postId', 'id');
            $attachment = $attachmentModel->findOne($attachmentId, "$attachmentModel.id", [
                ["$postModel.userId", 'userId'],
                "$attachmentModel.*"
            ]);
            if (!empty($attachment)) {
                $userId = $attachment['userId'];
                if ($userId != $this->request->getSessionParam('loggedUser')['id']) {
                    $this->renderView('notAuthorised');
                }
                header("Content-Disposition: attachment; filename=$attachment[fileName]");
                readfile('../uploads/'.$attachment['filePath']);
                exit;
            }
        }
        $this->renderView('notFound');
    }
}