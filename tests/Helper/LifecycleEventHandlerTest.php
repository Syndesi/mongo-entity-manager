<?php

declare(strict_types=1);

namespace Syndesi\MongoEntityManager\Tests\Helper;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Syndesi\MongoDataStructures\Type\Document;
use Syndesi\MongoEntityManager\Event\DocumentPostCreateEvent;
use Syndesi\MongoEntityManager\Event\DocumentPostDeleteEvent;
use Syndesi\MongoEntityManager\Event\DocumentPostMergeEvent;
use Syndesi\MongoEntityManager\Event\DocumentPreCreateEvent;
use Syndesi\MongoEntityManager\Event\DocumentPreDeleteEvent;
use Syndesi\MongoEntityManager\Event\DocumentPreMergeEvent;
use Syndesi\MongoEntityManager\Helper\LifecycleEventHelper;
use Syndesi\MongoEntityManager\Type\ActionMongoElement;
use Syndesi\MongoEntityManager\Type\ActionType;

class LifecycleEventHandlerTest extends TestCase
{
    public static function provideTestCases()
    {
        return [
            [
                new ActionMongoElement(ActionType::CREATE, new Document()),
                true,
                [
                    DocumentPreCreateEvent::class,
                ],
            ],
            [
                new ActionMongoElement(ActionType::CREATE, new Document()),
                false,
                [
                    DocumentPostCreateEvent::class,
                ],
            ],
            [
                new ActionMongoElement(ActionType::MERGE, new Document()),
                true,
                [
                    DocumentPreMergeEvent::class,
                ],
            ],
            [
                new ActionMongoElement(ActionType::MERGE, new Document()),
                false,
                [
                    DocumentPostMergeEvent::class,
                ],
            ],
            [
                new ActionMongoElement(ActionType::DELETE, new Document()),
                true,
                [
                    DocumentPreDeleteEvent::class,
                ],
            ],
            [
                new ActionMongoElement(ActionType::DELETE, new Document()),
                false,
                [
                    DocumentPostDeleteEvent::class,
                ],
            ],
        ];
    }

    #[DataProvider("provideTestCases")]
    public function testCases(ActionMongoElement $actionMongoElement, bool $isPre, array $expectedEvents): void
    {
        $actualEvents = LifecycleEventHelper::getLifecycleEventForMongoActionElement($actionMongoElement, $isPre);
        $this->assertSame(count($expectedEvents), count($actualEvents));
        foreach ($expectedEvents as $i => $expectedEvent) {
            $this->assertInstanceOf($expectedEvent, $actualEvents[$i]);
        }
    }
}
