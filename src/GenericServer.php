<?php

namespace App;

use Monolog\Logger;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GenericServer
{
    /**
     * @var Logger
     */
    private $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function handleRequest(Request $request): Response
    {
        return new JsonResponse(
            [
                'error' => 'ERROR',
                'code' => 404,
            ],
            404
        );
    }
}
