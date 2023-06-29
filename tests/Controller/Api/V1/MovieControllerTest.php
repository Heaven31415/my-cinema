<?php
/** @noinspection PhpUnhandledExceptionInspection */

namespace App\Tests\Controller\Api\V1;

use App\Factory\MovieFactory;
use App\Repository\MovieRepository;
use FOS\RestBundle\Exception\InvalidParameterException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Uid\Uuid;

class MovieControllerTest extends WebTestCase
{
    protected KernelBrowser $client;
    protected MovieFactory $movieFactory;
    protected MovieRepository $movieRepository;
    protected final const DATE_FORMAT = 'Y-m-d';

    /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->catchExceptions(false);

        $container = $this->client->getContainer();

        $this->movieFactory = $container->get(MovieFactory::class);
        $this->movieRepository = $container->get(MovieRepository::class);
    }

    public function testIndex_ReturnsAllMovies(): void // done!
    {
        $movies = $this->movieFactory->createMany(2);

        $this->client->jsonRequest('GET', 'api/v1/movies');
        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertCount(2, $response);

        $movie = $movies[0];
        $this->assertEquals($movie->getId(), $response[0]['id']);
        $this->assertEquals($movie->getTitle(), $response[0]['title']);
        $this->assertEquals($movie->getDescription(), $response[0]['description']);
        $this->assertEquals($movie->getDurationInMinutes(), $response[0]['durationInMinutes']);
        $this->assertEquals(
            $movie->getReleaseDate()->format(self::DATE_FORMAT),
            $response[0]['releaseDate']
        );
        $this->assertEquals($movie->getGenre()->getId(), $response[0]['genre']['id']);

        $movie = $movies[1];
        $this->assertEquals($movie->getId(), $response[1]['id']);
        $this->assertEquals($movie->getTitle(), $response[1]['title']);
        $this->assertEquals($movie->getDescription(), $response[1]['description']);
        $this->assertEquals($movie->getDurationInMinutes(), $response[1]['durationInMinutes']);
        $this->assertEquals(
            $movie->getReleaseDate()->format(self::DATE_FORMAT),
            $response[1]['releaseDate']
        );
        $this->assertEquals($movie->getGenre()->getId(), $response[1]['genre']['id']);
    }

    public function testShow_ReturnsMovie(): void // done!
    {
        $movie = $this->movieFactory->createOne();

        $this->client->jsonRequest('GET', 'api/v1/movies/'.$movie->getId());
        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertEquals($movie->getId(), $response['id']);
        $this->assertEquals($movie->getTitle(), $response['title']);
        $this->assertEquals($movie->getDescription(), $response['description']);
        $this->assertEquals($movie->getDurationInMinutes(), $response['durationInMinutes']);
        $this->assertEquals(
            $movie->getReleaseDate()->format(self::DATE_FORMAT),
            $response['releaseDate']
        );
        $this->assertEquals($movie->getGenre()->getId(), $response['genre']['id']);
    }

    public function testShow_ThrowsResourceNotFoundException_IfMovieDoesntExist(): void // done!
    {
        $this->expectException(ResourceNotFoundException::class);

        $this->client->jsonRequest(
            'GET',
            'api/v1/movies/'.new Uuid('8a47fd24-34d3-4ed0-b69c-4d151bf277c6')
        );
    }

    public function testCreate_CreatesMovie(): void // done!
    {
        $title = 'Avatar';
        $description = 'Avatar is a 2009 science fiction film...';
        $durationInMinutes = 162;
        $releaseDate = '2009-12-25';
        $genre = 'Science Fiction';

        $this->client->jsonRequest('POST', 'api/v1/movies', [
            'title' => $title,
            'description' => $description,
            'durationInMinutes' => $durationInMinutes,
            'releaseDate' => $releaseDate,
            'genre' => $genre,
        ]);
        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $this->assertTrue(Uuid::isValid($response['id']));
        $this->assertEquals($title, $response['title']);
        $this->assertEquals($description, $response['description']);
        $this->assertEquals($durationInMinutes, $response['durationInMinutes']);
        $this->assertEquals($releaseDate, $response['releaseDate']);
        $this->assertEquals($genre, $response['genre']['name']);

        $this->assertCount(1, $this->movieRepository->findAll());
    }

    public function testCreate_ThrowsInvalidParameterException_IfRequestBodyIsMissingValue(): void
    { // done!
        $this->expectException(InvalidParameterException::class);

        $this->client->jsonRequest('POST', 'api/v1/movies', [
            'description' => 'Avatar is a 2009 science fiction film...',
            'durationInMinutes' => 162,
            'releaseDate' => '2009-12-25',
            'genre' => 'Science Fiction',
        ]);
    }

    public function testCreate_ThrowsInvalidParameterException_IfRequestBodyHasInvalidValue(): void
    { // done!
        $this->expectException(InvalidParameterException::class);

        $this->client->jsonRequest('POST', 'api/v1/movies', [
            'title' => 0,
            'description' => 'Avatar is a 2009 science fiction film...',
            'durationInMinutes' => 162,
            'releaseDate' => '2009-12-25',
            'genre' => 'Science Fiction',
        ]);
    }

    public function testUpdate_UpdatesMovie(): void // done!
    {
        $movie = $this->movieFactory->createOne();

        $title = 'Avatar';
        $description = 'Avatar is a 2009 science fiction film...';
        $durationInMinutes = 162;
        $releaseDate = '2009-12-25';
        $genre = 'Science Fiction';

        $this->client->jsonRequest('PUT', 'api/v1/movies/'.$movie->getId(), [
            'title' => $title,
            'description' => $description,
            'durationInMinutes' => $durationInMinutes,
            'releaseDate' => $releaseDate,
            'genre' => $genre,
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
        $this->assertTrue($this->client->getResponse()->isEmpty());

        $this->assertEquals($title, $movie->getTitle());
        $this->assertEquals($description, $movie->getDescription());
        $this->assertEquals($durationInMinutes, $movie->getDurationInMinutes());
        $this->assertEquals($releaseDate, $movie->getReleaseDate()->format(self::DATE_FORMAT));
        $this->assertEquals($genre, $movie->getGenre()->getName());
    }

    public function testUpdate_ThrowsInvalidParameterException_IfRequestBodyIsMissingValue(): void
    { // done!
        $movie = $this->movieFactory->createOne();

        $this->expectException(InvalidParameterException::class);

        $this->client->jsonRequest('PUT', 'api/v1/movies/'.$movie->getId(), [
            'description' => 'Avatar is a 2009 science fiction film...',
            'durationInMinutes' => 162,
            'releaseDate' => '2009-12-25',
            'genre' => 'Science Fiction',
        ]);
    }

    public function testUpdate_ThrowsInvalidParameterException_IfRequestBodyHasInvalidValue(): void
    { // done!
        $movie = $this->movieFactory->createOne();

        $this->expectException(InvalidParameterException::class);

        $this->client->jsonRequest('PUT', 'api/v1/movies/'.$movie->getId(), [
            'title' => 0,
            'description' => 'Avatar is a 2009 science fiction film...',
            'durationInMinutes' => 162,
            'releaseDate' => '2009-12-25',
            'genre' => 'Science Fiction',
        ]);
    }

    public function testDelete_DeletesShow(): void // done!
    {
        $movie = $this->movieFactory->createOne();

        $this->client->jsonRequest('DELETE', 'api/v1/movies/'.$movie->getId());

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
        $this->assertTrue($this->client->getResponse()->isEmpty());

        $this->assertCount(0, $this->movieRepository->findAll());
    }

    public function testDelete_ThrowsResourceNotFoundException_IfMovieDoesntExist(): void
    {
        $this->expectException(ResourceNotFoundException::class);

        $this->client->jsonRequest(
            'DELETE',
            'api/v1/movies/'.new Uuid('8a47fd24-34d3-4ed0-b69c-4d151bf277c6')
        );
    }
}
