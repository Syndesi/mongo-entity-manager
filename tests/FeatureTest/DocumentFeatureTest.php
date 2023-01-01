<?php

declare(strict_types=1);

namespace Syndesi\MongoEntityManager\Tests\FeatureTest;

use Syndesi\MongoDataStructures\Type\Document;
use Syndesi\MongoEntityManager\Tests\FeatureTestCase;
use Syndesi\MongoEntityManager\Type\EntityManager;

class DocumentFeatureTest extends FeatureTestCase
{
    public function testDocument(): void
    {
        $document = new Document();
        $document
            ->setCollection('test')
            ->setIdentifier(1236)
            ->addProperty('someKey', 'some value')
            ->addProperty('otherPropertyName', 'some value');

        $em = $this->container->get(EntityManager::class);
        $this->assertCollectionDocumentCount('test', 0);
        $em->create($document);
        $em->flush();
        $this->assertCollectionDocumentCount('test', 1);

        $document->addProperty('changed', 'hello world update :D');
        $document->addProperty('array', [1, 2, 3, 4]);

        $em->merge($document);
        $em->flush();
        $this->assertCollectionDocumentCount('test', 1);

        $foundDocument = $em->getOneByIdentifier('test', 1236);
        $this->assertSame('test', $foundDocument->getCollection());
        $this->assertSame(1236, $foundDocument->getIdentifier());
        $this->assertSame('some value', $foundDocument->getProperty('someKey'));
        $this->assertSame('some value', $foundDocument->getProperty('otherPropertyName'));
        $this->assertSame('hello world update :D', $foundDocument->getProperty('changed'));
        $this->assertSame([1, 2, 3, 4], $foundDocument->getProperty('array'));

        $em->delete($document);
        $em->flush();
        $this->assertCollectionDocumentCount('test', 0);

        $unfoundDocument = $em->getOneByIdentifier('test', 1236);
        $this->assertNull($unfoundDocument);
    }
}
