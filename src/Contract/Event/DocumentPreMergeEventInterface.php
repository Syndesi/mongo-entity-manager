<?php

declare(strict_types=1);

namespace Syndesi\MongoEntityManager\Contract\Event;

use Syndesi\MongoDataStructures\Contract\DocumentInterface;

interface DocumentPreMergeEventInterface extends PreMergeEventInterface
{
    public function getElement(): DocumentInterface;
}
