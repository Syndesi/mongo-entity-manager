<?php

declare(strict_types=1);

namespace Syndesi\MongoEntityManager\Contract;

use Syndesi\MongoDataStructures\Contract\DocumentInterface;
use Syndesi\MongoEntityManager\Type\ActionType;

interface ActionMongoElementInterface
{
    public function getAction(): ActionType;

    public function getElement(): DocumentInterface;
}
