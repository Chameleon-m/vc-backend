<?php

namespace App\Controller;

use App\Entity\People;
use App\Message\PeopleStateMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpCache\StoreInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Workflow\Registry;

//#[Route('/admin')]
class AdminController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private MessageBusInterface $bus;

    public function __construct(EntityManagerInterface $entityManager, MessageBusInterface $bus)
    {
        $this->entityManager = $entityManager;
        $this->bus = $bus;
    }

    // todo: in api controller for test
    #[Route('/admin/people/review/{id}', name: 'review_people')]
    public function reviewPeople(Request $request, People $people, Registry $registry): Response
    {
        $accepted = !$request->query->get('reject');

        $machine = $registry->get($people);
        if ($machine->can($people, 'publish')) {
            $transition = $accepted ? 'publish' : 'reject';
        } elseif ($machine->can($people, 'publish_ham')) {
            $transition = $accepted ? 'publish_ham' : 'reject_ham';
        } else {
            return $this->json([
                'status' => 'error',
                'message' => 'People already reviewed or not in the right state.'
            ], 400);
        }

        $machine->apply($people, $transition);
        $this->entityManager->flush();

        if ($accepted) {
            $reviewUrl = $this->generateUrl('review_people', ['id' => $people->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
            $this->bus->dispatch(new PeopleStateMessage($people->getId(), $reviewUrl));
        }

        return $this->json([
            'status' => 'ok',
            'transition' => $transition,
            'people' => $people->getId(),
        ]);
    }

    #[Route('/admin/http-cache/{uri<.*>}', methods: ['PURGE'])]
    public function purgeHttpCache(KernelInterface $kernel, Request $request, string $uri, StoreInterface $store): Response
    {
        if ('prod' === $kernel->getEnvironment()) {
            return $this->json(['status' => 'error', 'message' => 'KO', 400]);
        }

        $store->purge($request->getSchemeAndHttpHost().'/'.$uri);

        return $this->json(['status' => 'ok']);
    }
}