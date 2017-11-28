<?php

require __DIR__ . '/../vendor/autoload.php';

use App\GenericServer\Server;
use Symfony\Component\HttpFoundation\Request;

$request = Request::createFromGlobals();
$token = new \ActiveCollab\SDK\Token(
    urldecode($request->request->get('token')),
    urldecode($request->request->get('acUrl'))
);

$server = new Server($token);
$response = $server->handleRequest($request);
$response->send();
