<?php

declare(strict_types=1);

namespace Syndesi\MongoEntityManager\Tests\Trait;

use PHPUnit\Framework\TestCase;
use Syndesi\MongoEntityManager\Contract\Event\EventInterface;
use Syndesi\MongoEntityManager\Trait\StoppableEventTrait;

class StoppableEventTraitTest extends TestCase
{
    private function getTrait(): EventInterface
    {
        return new class implements EventInterface {
            use StoppableEventTrait;
        };
    }

    public function testStoppableEventTrait(): void
    {
        $trait = $this->getTrait();
        $this->assertFalse($trait->isPropagationStopped());
        $trait->stopPropagation();
        $this->assertTrue($trait->isPropagationStopped());
    }
}
