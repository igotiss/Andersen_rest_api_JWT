<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ApiController extends AbstractController
{
    protected $statusCode = 200;

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    protected function setStatusCode($statusCode): static
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    public function response($data, $headers = []): JsonResponse
    {
        return new JsonResponse($data, $this->getStatusCode(), $headers);
    }

    public function respondWithErrors($errors, $headers = []): JsonResponse
    {
        $data = [
            'status' => $this->getStatusCode(),
            'errors' => $errors,
        ];

        return new JsonResponse($data, $this->getStatusCode(), $headers);
    }

    public function respondWithSuccess($success, $headers = []): JsonResponse
    {
        $data = [
            'status' => $this->getStatusCode(),
            'success' => $success,
        ];

        return new JsonResponse($data, $this->getStatusCode(), $headers);
    }

    public function respondUnauthorized($message = 'Not authorized!'): JsonResponse
    {
        return $this->setStatusCode(401)->respondWithErrors($message);
    }

    public function respondValidationError($message = 'Validation errors'): JsonResponse
    {
        return $this->setStatusCode(422)->respondWithErrors($message);
    }

    public function respondNotFound($message = 'Not found!'): JsonResponse
    {
        return $this->setStatusCode(404)->respondWithErrors($message);
    }

    public function respondCreated($data = []): JsonResponse
    {
        return $this->setStatusCode(201)->response($data);
    }

    protected function transformJsonBody(Request $request): Request
    {
        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            return $request;
        }

        $request->request->replace($data);

        return $request;
    }
}
