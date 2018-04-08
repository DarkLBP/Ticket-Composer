<?php

namespace Controllers;

use Core\Controller;
use Models\UsersSessionsModel;

class SessionController extends Controller
{
    public function initialize()
    {
        //Redeem user token from cookie to recover session
        $user = $this->request->getSessionParam('user');
        if (empty($user)) {
            $userToken = $this->request->getCookieParam('userToken', true);
            if (!empty($userToken)) {
                $tokenSplit = explode("-", $userToken);
                if (count($tokenSplit) === 2) {
                    $userId = $tokenSplit[0];
                    $sessionToken = $tokenSplit[1];
                    $sessionModel = new UsersSessionsModel();
                    $data = $sessionModel->find(['id' => $sessionToken, 'userId' => $userId]);
                    if (!empty($data)) {
                        $this->request->setSessionParam('user', $userId);
                    }
                }
            }
        }
    }
}