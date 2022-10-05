<?php

namespace App\Modules\Auth\Controller;

use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelloController extends AbstractController
{
    #[Route("/test/hello")]
    public function index(): Response
    {
        return new Response('<html><body><h1>Hello World!</h1></body></html>');
    }

    #[Route("/test/json")]
    public function apiTest()
    {
        return $this->apiAnswer('hello');
    }
}