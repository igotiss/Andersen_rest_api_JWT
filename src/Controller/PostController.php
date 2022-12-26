<?php

namespace App\Controller;

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PostRepository;

#[Route("/api", name: "post_api")]
class PostController extends AbstractController
{
    #[Route('/posts', name: 'posts', methods: ["GET"])]
    public function getPosts(PostRepository $postRepository): JsonResponse
    {
       $data = $postRepository->findAll();
       return $this->response($data);
    }

    private function response(array $data, $status = 200, $headers = [])
    {
        return new JsonResponse($data, $status, $headers);
    }

    #[Route('/posts', name: 'posts_add', methods: ["POST"])]
    public function addPost(Request $request, EntityManagerInterface $entityManager, PostRepository $postRepository){

        try{
            $request = $this->transformJsonBody($request);

            if ( !$request->get('name') || !$request->request->get('description')){
                throw new \Exception();
            }

            $post = new Post();
            $post->setName($request->get('name'));
            $post->setDescription($request->get('description'));
            $entityManager->persist($post);
            $entityManager->flush();

            $data = [
                'status' => 200,
                'success' => "Post added successfully",
            ];
            return $this->response($data);

        }catch (\Exception $e){
            $data = [
                'status' => 422,
                'errors' => "Data no valid",
            ];
            return $this->response($data, 422);
        }

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
