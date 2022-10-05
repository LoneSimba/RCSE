<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;

class ErrorController extends AbstractController
{
    public function show(\Throwable $exception, ?DebugLoggerInterface $debugLogger): JsonResponse
    {

    }
}