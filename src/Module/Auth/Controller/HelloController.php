<?php

namespace App\Module\Auth\Controller;

use App\Controller\AbstractController;
use App\Module\Auth\Entity\User;
use App\Module\Auth\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class HelloController extends AbstractController
{
    #[Route("/test/hello")]
    public function index(): Response
    {
        return new Response('<html><body><h1>Hello World!</h1></body></html>');
    }

    #[Route("/test/json")]
    public function apiTest(UserRepository $userRepository)
    {
        $user = new User();
        $user->setLogin('test')->setEmail('test')->setPassword('test');
        $userRepository->save($user, true);
        $user = $userRepository->findOneByLogin('test');
        $serialize = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);

        return $this->apiAnswer($serialize->serialize($user, 'json'));
    }
}