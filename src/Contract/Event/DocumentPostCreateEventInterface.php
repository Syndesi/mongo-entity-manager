<?php

declare(strict_types=1);

namespace Syndesi\MongoEntityManager\Contract\Event;

use Syndesi\MongoDataStructures\Contract\DocumentInterface;

interface DocumentPostCreateEventInterface extends PostCreateEventInterface
{
    #[\Override]
    public function getElement(): DocumentInterface;
}
