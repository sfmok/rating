<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\DataFixtures\AppFixtures;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RateControllerTest extends WebTestCase
{
    protected function setUp(): void
    {
        $container = self::getContainer();
        $entityManager = $container->get('doctrine')->getManager();

        // Drop and create schema
        $metadatas = $entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($entityManager);
        $schemaTool->dropDatabase();
        $schemaTool->createSchema($metadatas);

        // Load fixtures
        $loader = new Loader();
        /** @psalm-suppress ArgumentTypeCoercion */
        $loader->addFixture(new AppFixtures($container->get(UserPasswordHasherInterface::class)));

        $purger = new ORMPurger($entityManager);
        $executor = new ORMExecutor($entityManager, $purger);
        $executor->execute($loader->getFixtures());
    }

    public function testAddRateWithInvalidData(): void
    {
        $client = $this->getAuthenticatedClient();
        $client->request(
            method: Request::METHOD_POST,
            uri: '/api/rate',
            server: ['CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/json'],
            content: json_encode([
                'satisfaction' => 6,
                'feedback' => 'G',
                'communication' => 0,
                'quality_of_work' => 6,
                'value_for_money' => -1,
                'project_id' => 1,
            ]),
        );

        $response = $client->getResponse();
        self::assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
        $violations = json_decode($response->getContent(), true)['violations'];
        self::assertSame('satisfaction', $violations[0]['propertyPath']);
        self::assertSame('less_or_equal 5', $violations[0]['title']);

        self::assertSame('feedback', $violations[1]['propertyPath']);
        self::assertSame('min_length 2', $violations[1]['title']);

        self::assertSame('communication', $violations[2]['propertyPath']);
        self::assertSame('great_or_equal 1', $violations[2]['title']);

        self::assertSame('qualityOfWork', $violations[3]['propertyPath']);
        self::assertSame('less_or_equal 5', $violations[3]['title']);

        self::assertSame('valueForMoney', $violations[4]['propertyPath']);
        self::assertSame('great_or_equal 1', $violations[4]['title']);
    }

    public function testAddRateWithValidData(): void
    {
        $client = $this->getAuthenticatedClient();
        $client->request(
            method: Request::METHOD_POST,
            uri: '/api/rate',
            server: ['CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/json'],
            content: json_encode([
                'satisfaction' => 4,
                'feedback' => 'Good Job',
                'communication' => 5,
                'quality_of_work' => 5,
                'value_for_money' => 4,
                'project_id' => 1,
            ]),
        );

        $response = $client->getResponse();
        self::assertSame(Response::HTTP_CREATED, $response->getStatusCode());
        $result = json_decode($response->getContent(), true);

        self::assertArrayHasKey('id', $result);
        self::assertNotEmpty($result['id']);
        self::assertSame(4, $result['satisfaction']);
        self::assertSame(5, $result['communication']);
        self::assertSame(5, $result['quality_of_work']);
        self::assertSame(4, $result['value_for_money']);
        self::assertSame('Good Job', $result['feedback']);
        self::assertArrayHasKey('created_at', $result);
        self::assertNotEmpty($result['created_at']);
        self::assertArrayHasKey('updated_at', $result);
        self::assertNotEmpty($result['updated_at']);
    }

    public function testEditExistingRate(): void
    {
        $client = $this->getAuthenticatedClient();
        $client->request(
            method: Request::METHOD_PUT,
            uri: '/api/rate/1',
            server: ['CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/json'],
            content: json_encode([
                'satisfaction' => 1,
                'feedback' => 'Bad Job',
            ]),
        );

        $response = $client->getResponse();
        self::assertSame(Response::HTTP_OK, $response->getStatusCode());
        $result = json_decode($response->getContent(), true);
        self::assertSame(1, $result['satisfaction']);
        self::assertSame('Bad Job', $result['feedback']);
    }

    private function getAuthenticatedClient(): KernelBrowser
    {
        static::ensureKernelShutdown();
        $client = static::createClient();
        $client->request(
            Request::METHOD_POST,
            '/api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'username' => 'johndoe@foobar.com', // See AppFixtures
                'password' => 'admin',
            ]),
        );

        $data = json_decode($client->getResponse()->getContent(), true);
        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));

        return $client;
    }
}
