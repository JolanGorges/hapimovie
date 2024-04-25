<?php

namespace App\Controller\Api;

use App\Entity\Movie;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use OpenApi\Attributes as OA;

#[OA\Tag(name: "Movie")]
class MovieController extends AbstractController
{

    public function __construct(
        private MovieRepository $movieRepository,
        private EntityManagerInterface $em,
        private SerializerInterface $serializer
    ) {
        // ...
    }


    #[Route('/api/movies', name: 'app_api_movie', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $movies = $this->movieRepository->findAll();

        return $this->json([
            'movies' => $movies,
        ], 200, [], [
            'groups' => ['read']
        ]);
    }

    #[Route('/api/movie/{id}', name: 'app_api_movie_get',  methods: ['GET'])]
    public function get(?Movie $movie = null): JsonResponse
    {
        if (!$movie) {
            return $this->json([
                'error' => 'Ressource does not exist',
            ], 404);
        }

        return $this->json($movie, 200, [], [
            'groups' => ['read']
        ]);
    }

    #[Route('/api/movies', name: 'app_api_movie_add',  methods: ['POST'])]
    public function add(
        #[MapRequestPayload('json', ['groups' => ['create']])] Movie $movie
    ): JsonResponse {
        $this->em->persist($movie);
        $this->em->flush();

        return $this->json($movie, 200, [], [
            'groups' => ['read']
        ]);
    }


    #[Route('/api/movie/{id}', name: 'app_api_movie_update',  methods: ['PUT'])]
    public function update(Movie $movie, Request $request): JsonResponse
    {

        $data = $request->getContent();
        $this->serializer->deserialize($data, Movie::class, 'json', [
            AbstractNormalizer::OBJECT_TO_POPULATE => $movie,
            'groups' => ['update']
        ]);

        $this->em->flush();

        return $this->json($movie, 200, [], [
            'groups' => ['read'],
        ]);
    }

    #[Route('/api/movie/{id}', name: 'app_api_movie_delete',  methods: ['DELETE'])]
    public function delete(Movie $movie): JsonResponse
    {
        $this->em->remove($movie);
        $this->em->flush();

        return $this->json([
            'message' => 'Movie deleted successfully'
        ], 200);
    }
}
