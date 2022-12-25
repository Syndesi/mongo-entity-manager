<?php

declare(strict_types=1);

namespace Syndesi\MongoEntityManager\Event;

use Syndesi\MongoEntityManager\Contract\Event\PreFlushEventInterface;
use Syndesi\MongoEntityManager\Trait\StoppableEventTrait;

class PreFlushEvent implements PreFlushEventInterface
{
    use StoppableEventTrait;
}
