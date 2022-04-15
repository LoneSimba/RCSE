<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelloController
{
    /**
     * @Route("/hello")
     */
    public function index(): Response
    {
        return new Response('<html><body><h1>Hello, World!</h1></body></html>');
    }
}