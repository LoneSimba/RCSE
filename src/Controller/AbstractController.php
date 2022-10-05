<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as SymfonyController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class AbstractController extends SymfonyController
{
    protected LoggerInterface $logger;

    public function __construct(#[Autowire(service: 'monolog.logger.request')] LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    protected function apiAnswer(mixed $data, bool $success = true, int $status = 200, string $message = ''): JsonResponse
    {
        return $this->json(
            [
                'data' => $data,
                'status' => $status,
                'message' => $message,
                'success' => $success,
            ],
            $status
        );
    }
}