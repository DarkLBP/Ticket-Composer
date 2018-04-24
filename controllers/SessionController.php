<?php

namespace Controllers;

use Core\Controller;
use Core\Request;
use Core\Utils;
use Models\UsersModel;
use Models\UsersSessionsModel;

class SessionController extends Controller
{
    private $user = [];
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
        $sessionModel = new UsersSessionsModel();
        $sessionModel->delete(['id' => $this->userToken['sessionToken'], 'userId' => $this->userToken['userId']]);
        $this->userToken = [];
        $this->request->setCookieParam('userToken', null);
    }

    public function getLoggedUser(): array
    {
        if ($this->isLoggedIn()) {
            return $this->user;
        }
        return [];
    }

    private function getUserData(): void
    {
        $userId = $this->request->getSessionParam('loggedUser');
        $usersModel = new UsersModel();
        $this->user = $usersModel->findOne($userId, 'id', ['id', 'name', 'surname', 'email']);
    }

    public function initialize()
    {
        //Destroy token if logout
        if ($this->controller === "user" && $this->action === "logout") {
            $this->destroyToken();
        }

        //Redeem user token from cookie to recover session
        if (!$this->isLoggedIn() && !empty($this->userToken)) {
            $this->redeemToken();
        }

        //Make proper redirects
        $this->makeRedirects();

        //Get user data
        $this->getUserData();
    }

    public function isLoggedIn()
    {
        return !empty($this->user);
    }

    private function makeRedirects()
    {
        if (!$this->isLoggedIn()) {
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
        $sessionModel = new UsersSessionsModel();
        $data = $sessionModel->count(['id' => $this->userToken['sessionToken'], 'userId' => $this->userToken['userId']]);
        if ($data === 1) {
            $this->request->setSessionParam('loggedUser', $this->userToken['userId']);
        } else {
            $this->destroyToken();
        }
    }
}