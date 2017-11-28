<?php

require __DIR__ . '/../vendor/autoload.php';

use App\GenericServer;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Symfony\Component\HttpFoundation\Request;

$requestLogger = new Logger('requests');
$requestLogger->pushHandler(
    new StreamHandler(
        __DIR__ . '/../var/log/requests.log',
        Logger::INFO
    )
);

$server = new GenericServer($requestLogger);
$request = Request::createFromGlobals();
$response = $server->handleRequest($request);
$response->send();
