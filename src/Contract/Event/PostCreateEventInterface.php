<?php

declare(strict_types=1);

namespace Syndesi\MongoEntityManager\Contract\Event;

use Syndesi\MongoDataStructures\Contract\DocumentInterface;

interface PostCreateEventInterface extends LifecycleEventInterface
{
    #[\Override]
    public function getElement(): DocumentInterface;
}
