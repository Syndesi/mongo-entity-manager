<?php

declare(strict_types=1);

namespace Syndesi\MongoEntityManager\Contract\Event;

use Syndesi\MongoDataStructures\Contract\DocumentInterface;

interface PostMergeEventInterface extends LifecycleEventInterface
{
    public function getElement(): DocumentInterface;
}
