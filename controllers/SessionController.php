<?php

namespace Controllers;

use Core\Controller;
use Core\Request;
use Core\Utils;

class SessionController extends Controller
{
    private $userToken = [];

    /**
     * SessionController constructor.
     * @param Request $request The incoming request
     */
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
    }

    /**
     * Destroys a session token
     */
    private function destroyToken()
    {
        if (empty($this->userToken)) {
            return;
        }
        $sessionModel = $this->getModel('sessions');
        $sessionModel->delete([
            ['id', '=', $this->userToken['sessionToken']],
            'AND',
            ['userId', '=', $this->userToken['userId']]
        ]);
        $this->userToken = [];
        $this->request->setCookieParam('userToken', null);
    }

    /**
     * Begins session controller checks
     */
    public function initialize()
    {
        //Destroy token if logout
        if ($this->request->getController() === "user" && $this->request->getAction() === "logout"
            && empty($this->request->getActionParameters())) {
            $this->destroyToken();
            return;
        }

        //Redeem user token from cookie to recover session
        $this->redeemToken();

        //Make proper redirects
        $this->makeRedirects();
    }

    /**
     * Redirects according to established rules
     */
    private function makeRedirects()
    {
        if (!$this->request->getSessionParam('loggedUser')) {
            //Redirect to login if user tries to enter to pages where login is required
            if ($this->request->getController() !== "main" && $this->request->getController() !== "user" &&
                $this->request->getController() !== "install" && $this->request->getController() !== "api") {
                $this->request->setSessionParam('targetURL', Utils::getURL($this->request->getController(), $this->request->getAction(), $this->request->getActionParameters()));
                $this->request->redirect(Utils::getURL("user", "login"));
            } else if (!empty($this->request->getSessionParam('targetURL')) && $this->request->getController() !== "user" && $this->request->getAction() !== "login") {
                $this->request->setSessionParam('targetURL');
            }
        } else {
            if ($this->request->getController() === "user") {
                //Redirect if the user is logged in and tries to access login or register pages
                if ($this->request->getAction() === "login" || $this->request->getAction() === "register") {
                    $this->request->redirect(Utils::getURL('panel'));
                }
            } else if ($this->request->getController() === 'main') {
                $this->request->redirect(Utils::getURL('panel'));
            }
        }
    }

    /**
     * Converts a user token to a session
     */
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
            ["$sessionModel.id", '=', $this->userToken['sessionToken']],
            "AND",
            ["$sessionModel.userId", '=', $this->userToken['userId']]
        ], [
            "$usersModel.*"
        ]);
        if (count($data) == 1) {
            $user = $data[0];
            $departmentsModel = $this->getModel('usersDepartments');
            $departments = $departmentsModel->find([
                ['userId', '=', $user['id']]
            ]);
            foreach ($departments as $department) {
                $user['departments'][] = $department['departmentId'];
            }
            $this->request->setSessionParam('loggedUser', $user);
        } else {
            $this->request->setSessionParam('loggedUser');
        }
    }
}