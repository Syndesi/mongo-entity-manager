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

        $em->merge($document);
        $em->flush();
        $this->assertCollectionDocumentCount('test', 1);

        $em->delete($document);
        $em->flush();
        $this->assertCollectionDocumentCount('test', 0);
    }
}
