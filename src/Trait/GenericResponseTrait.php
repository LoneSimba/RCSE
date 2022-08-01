<?php
namespace App\Trait;

use Symfony\Component\HttpFoundation\JsonResponse;

trait GenericResponseTrait
{
    protected bool $success = true;
    protected int $code = 200;

    protected function responseJson(array $contents = []): JsonResponse
    {
        return new JsonResponse([
            'data' => $contents,
            'code' => $this->code,
            'success' => $this->success
        ], $this->code);
    }
}