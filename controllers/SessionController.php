<?php

namespace Controllers;

use Core\Controller;
use Core\Request;
use Core\Utils;

class SessionController extends Controller
{
    private $userToken = [];
    private $controller = '';
    private $action = '';

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $userToken = $this->request->getCookieParam('userToken', true);
        if (!empty($userToken)) {
            $tokenSplit = explode("-", $userToken);
            if (count($tokenSplit) === 2) {
                $this->userToken['userId'] = $tokenSplit[0];
                $this->userToken['sessionToken'] = $tokenSplit[1];
            }
        }
        $this->controller = $this->request->getController();
        $this->action = $this->request->getAction();
    }

    private function destroyToken()
    {
        if (empty($this->userToken)) {
            return;
        }
        $sessionModel = $this->getModel('sessions');
        $sessionModel->delete(['id' => $this->userToken['sessionToken'], 'userId' => $this->userToken['userId']]);
        $this->userToken = [];
        $this->request->setCookieParam('userToken', null);
    }

    public function initialize()
    {
        //Destroy token if logout
        if ($this->controller === "user" && $this->action === "logout") {
            $this->destroyToken();
            return;
        }

        //Redeem user token from cookie to recover session
        $this->redeemToken();

        //Make proper redirects
        $this->makeRedirects();
    }

    private function makeRedirects()
    {
        if (!$this->request->getSessionParam('loggedUser')) {
            //Redirect to login if user tries to enter to pages where login is required
            if ($this->controller !== "main" && $this->controller !== "user") {
                $this->request->redirect(Utils::getURL("user", "login"));
            }
        } else {
            if ($this->controller === "user") {
                //Redirect if the user is logged in and tries to access login or register pages
                if ($this->action === "login" || $this->action === "register") {
                    $this->request->redirect(Utils::getURL('tickets'));
                }
            }
        }
    }

    private function redeemToken()
    {
        if (empty($this->userToken)) {
            $this->request->setSessionParam('loggedUser');
            return;
        }
        $sessionModel = $this->getModel('sessions');
        $usersModel = $this->getModel('users');
        $sessionModel->join($usersModel, 'userId', 'id', 'inner');
        $data = $sessionModel->find([
            "$sessionModel.id" => $this->userToken['sessionToken'],
            "$sessionModel.userId" => $this->userToken['userId']
        ], [
            "$usersModel.name", "$usersModel.surname", "$usersModel.email", "$usersModel.id"
        ]);
        if (count($data) == 1) {
            $this->request->setSessionParam('loggedUser', $data[0]);
        } else {
            $this->request->setSessionParam('loggedUser');
        }
    }
}