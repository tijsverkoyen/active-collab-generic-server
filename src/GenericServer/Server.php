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
            case substr($request->getPathInfo(), 0, 11) == '/task-list/':
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
        $projectId = (int) str_replace('/task-list/', '', $request->getPathInfo());
        $userId = $request->request->get('userId');

        return new JsonResponse(
            [
                'tasks' => $this->getTasksForProject($projectId, $userId),
            ]
        );
    }

    private function getTasksForProject(int $id, ?int $userId): array
    {
        $client = new Client($this->token);

        // get all tasks for a project
        $tasks = $client->get(
            sprintf(
                '/projects/%1$s/tasks',
                $id
            )
        )->getJson()['tasks'];

        // remove tasks that are not assigned on the given user
        if ($userId !== null) {
            $tasks = array_filter(
                $tasks,
                function ($task) use ($userId) {
                    return ($task['assignee_id'] === $userId);
                }
            );
        }

        // convert into usable objects
        $tasks = array_map(
            function ($task) {
                $task['url_path'] = $this->token->getUrl() . $task['url_path'];

                return Task::fromActiveCollab($task)->toArray();
            },
            $tasks
        );

        // sort the tasks
        usort(
            $tasks,
            function ($e1, $e2) {
                return strcmp($e1['id'], $e2['id']);
            }
        );

        return $tasks;
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
