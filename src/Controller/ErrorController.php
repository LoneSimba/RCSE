<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;

class ErrorController extends AbstractController
{
    public function show(\Throwable $exception, ?DebugLoggerInterface $debugLogger): JsonResponse
    {
        $this->logger->error("Error occured: ({$exception->getCode()}) {$exception->getMessage()} in {$exception->getFile()} at {$exception->getLine()}, trace \n\n{$exception->getTraceAsString()}");

        $data = $this->getParameter('kernel.environment') !== 'prod' ? [
               'exception' => "{$exception->getCode()} - {$exception->getMessage()}" ,
               'file' => $exception->getFile(),
               'line' => $exception->getLine(),
               'trace' => array_map(function ($item) {
                   return [
                       'line' => $item['line'] ?? null,
                       'class' => $item['class'] ?? null,
                       'function' => $item['function'] ?? null,
                   ];
               }, $exception->getTrace())
           ] : [];

        return $this->apiAnswer($data, false, 500, 'Internal error');
    }
}