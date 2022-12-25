<?php

declare(strict_types=1);

namespace Syndesi\MongoEntityManager\Event;

use Syndesi\MongoEntityManager\Contract\Event\PostFlushEventInterface;
use Syndesi\MongoEntityManager\Trait\StoppableEventTrait;

class PostFlushEvent implements PostFlushEventInterface
{
    use StoppableEventTrait;
}
