<?php

declare(strict_types=1);

namespace Syndesi\MongoEntityManager\Tests\Helper;

use PHPUnit\Framework\TestCase;
use Syndesi\MongoDataStructures\Type\Document;
use Syndesi\MongoEntityManager\Helper\ActionMongoElementHelper;
use Syndesi\MongoEntityManager\Type\ActionMongoElement;
use Syndesi\MongoEntityManager\Type\ActionMongoElementType;
use Syndesi\MongoEntityManager\Type\ActionType;

class ActionMongoElementHelperTest extends TestCase
{
    public function provideActionMongoElementWithType()
    {
        return [
            [
                new ActionMongoElement(ActionType::CREATE, new Document()),
                ActionMongoElementType::DOCUMENT,
            ],
        ];
    }

    /**
     * @dataProvider provideActionMongoElementWithType
     */
    public function testGetTypeFromActionMongoElement(ActionMongoElement $object, ActionMongoElementType $expectedType): void
    {
        $foundType = ActionMongoElementHelper::getTypeFromActionMongoElement($object);
        $this->assertSame($expectedType, $foundType);
    }
}
