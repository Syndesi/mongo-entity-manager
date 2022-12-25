<?php

declare(strict_types=1);

namespace Syndesi\MongoEntityManager\Tests;

use Dotenv\Dotenv;
use MongoDB\Client;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Selective\Container\Container;
use Syndesi\MongoEntityManager\Type\EntityManager;

class FeatureTestCase extends ContainerTestCase
{
    protected Container $container;

    protected function setUp(): void
    {
        parent::setUp();

        $dotenv = Dotenv::createImmutable(__DIR__."/../");
        $dotenv->safeLoad();

        if (!array_key_exists('ENABLE_FEATURE_TEST', $_ENV)) {
            $this->markTestSkipped();
        }
        if (array_key_exists('LEAK', $_ENV)) {
            $this->markTestSkipped();
        }
        $client = new Client($_ENV['MONGODB_AUTH']);
        $client->dropDatabase('test');
        $this->container->set(Client::class, $client);
        $entityManager = new EntityManager(
            'test',
            $client,
            $this->container->get(EventDispatcherInterface::class),
            $this->container->get(LoggerInterface::class)
        );
        $this->container->set(EntityManager::class, $entityManager);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function assertCollectionDocumentCount(string $collection, int $expectedCount): void
    {
        $em = $this->container->get(EntityManager::class);
        $client = $em->getClient();
        $count = $client->selectDatabase('test')->selectCollection($collection)->countDocuments([]);
        $this->assertSame($expectedCount, $count, "Collection document count does not match.");
    }
}
