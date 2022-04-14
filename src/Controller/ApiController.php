<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ApiController extends AbstractController
{

    #[Route('/api/me', name: 'api_me')]
    public function me(#[CurrentUser] ?User $user, SerializerInterface $serializer): Response
    {
        if (null === $user) {
            return $this->json([
                'message' => 'missing credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }
        return $this->json([
            // https://symfonycasts.com/screencast/api-platform-security/data-page-load#serializing-data-directly-to-javascript
//            'user'  => $user->getUserIdentifier()
            'user' => $serializer->serialize($user, 'jsonld')
        ]);
    }
}
