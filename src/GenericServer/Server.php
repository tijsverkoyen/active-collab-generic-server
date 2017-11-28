<?php

namespace App\GenericServer;

use ActiveCollab\SDK\Authenticator\Cloud;
use ActiveCollab\SDK\Client;
use ActiveCollab\SDK\Token;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Server
{
    /**
     * @var Token
     */
    private $token;

    public function __construct(Token $token)
    {
        $this->token = $token;
    }

    public function handleRequest(Request $request): Response
    {
        switch ($request->getPathInfo()) {
            case '/login':
                return $this->handleLoginRequest($request);
            case '/task-list':
                return $this->handleTaskListRequest($request);
        }

        // fallback
        return new JsonResponse(
            [
                'error' => 'ERROR',
                'code' => 404,
            ],
            404
        );
    }

    public function handleTaskListRequest(Request $request): Response
    {
        $client = new Client($this->token);
        $response = $client->get(
            sprintf(
                '/users/%1$s/tasks',
                $request->request->get('userId')
            )
        )->getJson();
        $tasks = [];

        foreach ($response['tasks'] as $activeCollabTask) {
            $activeCollabTask['instance_url'] = $this->token->getUrl();
            $activeCollabTask['project'] = $response['related']['Project'][$activeCollabTask['project_id']];

            $tasks[] = Task::fromActiveCollab($activeCollabTask)->toArray();
        }

        usort(
            $tasks,
            function ($e1, $e2) {
                return strcmp($e1['summary'], $e2['summary']);
            }
        );

        return new JsonResponse(
            [
                'tasks' => $tasks,
            ]
        );
    }

    public function handleLoginRequest(Request $request): Response
    {
        $username = $request->query->get('username');
        $password = $request->query->get('password');
        $accountId = (int) $request->query->get('account');

        $authenticator = new Cloud(
            'Tijs Verkoyen',
            'PHPStorm Generic Server',
            $username,
            $password
        );

        $token = $authenticator->issueToken($accountId);

        $client = new Client($token);
        $users = array_values(
            array_filter(
                $client->get('/users')->getJson(),
                function ($user) use ($username) {
                    return ($user['email'] == $username);
                }
            )
        );

        return new JsonResponse(
            [
                'token' => $token->getToken(),
                'acArl' => $token->getUrl(),
                'userId' => $users[0]['id'],
            ]
        );
    }
}
