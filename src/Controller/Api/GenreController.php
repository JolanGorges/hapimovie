<?php

namespace App\Controller\Api;

use App\Entity\Genre;
use App\Repository\GenreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GenreController extends AbstractController
{
    public function __construct(
        private GenreRepository $genreRepository,
        private EntityManagerInterface $em,
        private SerializerInterface $serializer
    ) {
        // ...
    }


    #[Route('/api/genres', name: 'app_api_genre', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $genres = $this->genreRepository->findAll();

        return $this->json([
            'genres' => $genres,
        ], 200, [], [
            'groups' => ['read']
        ]);
    }

    #[Route('/api/genre/{id}', name: 'app_api_genre_get',  methods: ['GET'])]
    public function get(?Genre $genre = null): JsonResponse
    {
        if (!$genre) {
            return $this->json([
                'error' => 'Ressource does not exist',
            ], 404);
        }

        return $this->json($genre, 200, [], [
            'groups' => ['read']
        ]);
    }

    #[Route('/api/genres', name: 'app_api_genre_add',  methods: ['POST'])]
    public function add(
        #[MapRequestPayload('json', ['groups' => 'create'])] Genre $genre
    ): JsonResponse {
        $this->em->persist($genre);
        $this->em->flush();

        return $this->json($genre, 200, [], [
            'groups' => ['read']
        ]);
    }


    #[Route('/api/genre/{id}', name: 'app_api_genre_update',  methods: ['PUT'])]
    public function update(Genre $genre, Request $request): JsonResponse
    {

        $data = $request->getContent();
        $this->serializer->deserialize($data, Genre::class, 'json', [
            AbstractNormalizer::OBJECT_TO_POPULATE => $genre,
            'groups' => ['update']
        ]);

        $this->em->flush();

        return $this->json($genre, 200, [], [
            'groups' => ['read'],
        ]);
    }

    #[Route('/api/genre/{id}', name: 'app_api_genre_delete',  methods: ['DELETE'])]
    public function delete(Genre $genre): JsonResponse
    {
        $this->em->remove($genre);
        $this->em->flush();

        return $this->json([
            'message' => 'Genre deleted successfully'
        ], 200);
    }
}
