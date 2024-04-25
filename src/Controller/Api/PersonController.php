<?php

namespace App\Controller\Api;

use App\Entity\Person;
use App\Repository\PersonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PersonController extends AbstractController
{

    public function __construct(
        private PersonRepository $personRepository,
        private EntityManagerInterface $em,
        private SerializerInterface $serializer
    ) {
        // ...
    }

    #[Route('/api/persons', name: 'app_api_person', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $persons = $this->personRepository->findAll();

        return $this->json([
            'persons' => $persons,
        ], 200, [], [
            'groups' => ['read']
        ]);
    }

    #[Route('/api/person/{id}', name: 'app_api_person_get',  methods: ['GET'])]
    public function get(?Person $person = null): JsonResponse
    {
        if (!$person) {
            return $this->json([
                'error' => 'Ressource does not exist',
            ], 404);
        }

        return $this->json($person, 200, [], [
            'groups' => ['read']
        ]);
    }

    #[Route('/api/persons', name: 'app_api_person_add',  methods: ['POST'])]
    public function add(
        #[MapRequestPayload('json', ['groups' => 'create'])] Person $person
    ): JsonResponse {
        $this->em->persist($person);
        $this->em->flush();

        return $this->json($person, 200, [], [
            'groups' => ['read']
        ]);
    }


    #[Route('/api/person/{id}', name: 'app_api_person_update',  methods: ['PUT'])]
    public function update(Person $person, Request $request): JsonResponse
    {

        $data = $request->getContent();
        $this->serializer->deserialize($data, Person::class, 'json', [
            AbstractNormalizer::OBJECT_TO_POPULATE => $person,
            'groups' => ['update']
        ]);

        $this->em->flush();

        return $this->json($person, 200, [], [
            'groups' => ['read'],
        ]);
    }

    #[Route('/api/person/{id}', name: 'app_api_person_delete',  methods: ['DELETE'])]
    public function delete(Person $person): JsonResponse
    {
        $this->em->remove($person);
        $this->em->flush();

        return $this->json([
            'message' => 'Person deleted successfully'
        ], 200);
    }
}
