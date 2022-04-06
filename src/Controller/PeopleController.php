<?php

namespace App\Controller;

use App\Repository\PeopleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PeopleController extends AbstractController
{
    #[Route('/people', name: 'people_index')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome!',
        ]);
    }

    #[Route('/people/last', name: 'people_last')]
    public function last(Request $request, PeopleRepository $peopleRepository): JsonResponse
    {
        $offset = $request->query->get('offset', 0);

        $paginator = $peopleRepository->findStatePublished($offset);

        $response = $this->json([
            'data' => $paginator
        ]);
        $response->setSharedMaxAge(300);
        return $response;
    }
}
