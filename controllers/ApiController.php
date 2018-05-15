<?php

namespace Controllers;

use Core\Controller;
use Core\Request;

class ApiController extends Controller
{
    private $response = [];
    private $authExclusions = [
        'email'
    ];

    /**
     * ApiController constructor.
     * @param Request $request Incoming request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        if (!$this->request->isPost()) {
            $this->response['error'] = 'Invalid request';
            $this->sendResponse();
        }

        if (!in_array($this->request->getAction(), $this->authExclusions)) {
            $validToken = false;
            if (isset($_SERVER["HTTP_SESSION_TOKEN"])) {
                $validToken = $this->validateSession($_SERVER["HTTP_SESSION_TOKEN"]);
            }

            if (!$validToken && $this->request->getAction() !== 'login') {
                $this->response['error'] = 'Not authorised';
                $this->sendResponse();
            }
        }
    }

    /**
     *  Validates a user token
     * @param string $token The user token
     * @return bool Returns whether the token is valid or not
     */
    private function validateSession(string $token): bool
    {
        $chunks = explode('-', $token);
        if (count($chunks) === 2) {
            $sessionModel = $this->getModel('sessions');
            $usersModel = $this->getModel('users');
            $sessionModel->join($usersModel, 'userId', 'id', 'inner');
            $userId = $chunks[0];
            $session = $chunks[1];
            $count = $sessionModel->count([
                ["$sessionModel.id", '=', $session],
                'AND',
                ["$usersModel.id", '=', $userId],
                'AND',
                ["$usersModel.op", '=', 1]
            ]);
            return $count === 1;
        }
        return false;
    }

    /**
     * Replies with a list of tickets
     */
    public function actionTickets()
    {
        $ticketsModel = $this->getModel('tickets');
        $tickets = $ticketsModel->find();
        $this->response['tickets'] = $tickets;
        $this->sendResponse();
    }

    /**
     * Replies with a list of departments
     */
    public function actionDepartments()
    {
        $departmentsModel = $this->getModel('departments');
        $departments = $departmentsModel->find();
        $this->response['departments'] = $departments;
        $this->sendResponse();
    }

    /**
     * Replies with a list of posts from a ticket
     */
    public function actionPosts()
    {
        $ticket = $this->request->getPostParam('ticket', true);
        if (empty($ticket)) {
            $this->response['error'] = 'Missing parameters';
        } else {
            $ticketsModel = $this->getModel('tickets');
            $count = $ticketsModel->count([
                ['id', '=', $ticket]
            ]);
            if ($count === 1) {
                $postsModel = $this->getModel('posts');
                $posts = $postsModel->find([
                    ['ticketId', '=', $ticket]
                ]);
                $this->response['posts'] = $posts;
            } else {
                $this->response['error'] = 'Requested ticket does not exist';
            }
        }
        $this->sendResponse();
    }

    /**
     * Replies with a list of attachments from a post
     */
    public function actionAttachments()
    {
        $post = $this->request->getPostParam('post', true);
        if (empty($post)) {
            $this->response['error'] = 'Missing parameters';
        } else {
            $postsModel = $this->getModel('posts');
            $count = $postsModel->count([
                ['id', '=', $post]
            ]);
            if ($count === 1) {
                $attachmentsModel = $this->getModel('attachments');
                $attachments = $attachmentsModel->find([
                    ['postId', '=', $post]
                ]);
                $this->response['attachments'] = $attachments;
            } else {
                $this->response['error'] = 'Requested post does not exist';
            }
        }
        $this->sendResponse();
    }

    /**
     * Checks if the given email is valid
     */
    public function actionEmail()
    {
        $email = $this->request->getPostParam('email', true);
        if (empty($email)) {
            $this->response['error'] = 'Missing parameters';
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->response['error'] = 'Invalid email';
        } else {
            $usersModel = $this->getModel('users');
            $exists = $usersModel->count([
                ['email', '=', $email]
            ]);
            if ($exists == 1) {
                $this->response['error'] = 'Email already exists';
            }
        }
        $this->sendResponse();
    }

    /**
     * Logs in a user and replies with the user token
     */
    public function actionLogin()
    {
        $email = $this->request->getPostParam('email', true);
        $password = $this->request->getPostParam('password');
        if (empty($email) || empty($password)) {
            $this->response['error'] = 'Missing parameters';
            $this->sendResponse();
        }

        $usersModel = $this->getModel('users');
        $sessionModel = $this->getModel('sessions');
        $validationModel = $this->getModel('validations');
        $usersModel->join($validationModel, 'id', 'userId', 'left');
        $user = $usersModel->findOne($email, "email", [
            "$usersModel.*",
            ["$validationModel.id" => "validationId"]
        ]);
        if (!empty($user)) {
            if (password_verify($password, $user["password"])) {
                if (empty($user["validationId"])) {
                    if ($user["op"] != 1) {
                        $this->response['error'] = 'This user is not OP';
                    } else {
                        try {
                            $sessionToken = bin2hex(random_bytes(32));
                            $sessionModel->insert([
                                'id' => $sessionToken,
                                'userId' => $user['id']
                            ]);
                            $this->response['sessionToken'] = $user['id'] . '-' . $sessionToken;
                        } catch (\Exception $e) {
                            $this->response['error'] = 'Invalid credentials';
                        }
                    }
                } else {
                    $this->response['error'] = 'Account pending for validation';
                }
            } else {
                $this->response['error'] = 'Invalid credentials';
            }
        } else {
            $this->response['error'] = 'Invalid credentials';
        }
        $this->sendResponse();
    }

    /**
     * Replies with a group of calculated statistics
     */
    public function actionStatistics()
    {
        $ticketsModel = $this->getModel('tickets');
        $departmentsModel = $this->getModel('departments');
        $departmentsModel->join($ticketsModel, 'id', 'department', 'left');
        $countPerDepartment = $departmentsModel->find([], [
            'name',
            [
                "COUNT($ticketsModel.id)" => "count"
            ],
        ], ["$departmentsModel.id"]);
        $countPerStatus = $ticketsModel->find([], [
            'open',
            ['COUNT(*)' => 'count']
        ], ["open"]);
        $countPerStatusDepartment = $departmentsModel->find([], [
            'name',
            'open',
            ['COUNT(*)' => 'count']
        ], ["$departmentsModel.id", "open"]);

        $this->response['results'][] = $countPerDepartment;
        $this->response['results'][] = $countPerStatus;
        $this->response['results'][] = $countPerStatusDepartment;
        $this->response['resultTitles'][] = "Tickets Per Department";
        $this->response['resultTitles'][] = "Tickets Per Status";
        $this->response['resultTitles'][] = "Open And Closed Tickets Per Department";
        $this->sendResponse();
    }

    /**
     * Send the response to the client
     */
    private function sendResponse()
    {
        $this->request->setResponseHeader('Content-Type', 'application/json');
        echo json_encode($this->response);
        exit;
    }
}