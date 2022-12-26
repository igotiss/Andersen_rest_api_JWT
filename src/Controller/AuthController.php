<?php

namespace App\Controller;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthController extends ApiController
{
    public function register(Request $request, UserPasswordHasherInterface $hasher, ManagerRegistry $doctrine): JsonResponse
    {
        $em = $doctrine->getManager();
        $request = $this->transformJsonBody($request);
        $username = $request->get('username');
        $email = $request->get('email');
        $password = $request->get('password');

        if ( empty($password) || empty($email) || empty($username)){
            return $this->respondValidationError("Invalid Password or Email");
        }

        $user = new User($email);

        $user->setPassword($hasher->hashPassword($user, $password));
        $user->setEmail($email);
        $user->setUsername($username);
        $em->persist($user);
        $em->flush();

        return $this->respondWithSuccess(sprintf('User %s successfully created', $user->getUsername()));
    }

    public function getTokenUser(UserInterface $user, JWTTokenManagerInterface $tokenManager): JsonResponse
    {
        return new JsonResponse(['token' => $tokenManager->create($user)]);
    }
}
