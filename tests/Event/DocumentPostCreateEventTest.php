<?php

declare(strict_types=1);

namespace Syndesi\MongoEntityManager\Tests\Event;

use PHPUnit\Framework\TestCase;
use Syndesi\MongoDataStructures\Type\Document;
use Syndesi\MongoEntityManager\Event\DocumentPostCreateEvent;

class DocumentPostCreateEventTest extends TestCase
{
    public function testDocumentPostCreateEvent(): void
    {
        $element = new Document();
        $event = new DocumentPostCreateEvent($element);
        $this->assertSame($element, $event->getElement());
    }

    public function testElementManipulation(): void
    {
        $element = new Document();
        $element->addProperty('some', 'value');
        $event = new DocumentPostCreateEvent($element);
        $this->assertTrue($event->getElement()->hasProperty('some'));
        $event->getElement()->removeProperty('some');
        $this->assertFalse($event->getElement()->hasProperty('some'));
    }
}
