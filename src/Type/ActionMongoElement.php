<?php

declare(strict_types=1);

namespace Syndesi\MongoEntityManager\Type;

use Syndesi\MongoDataStructures\Contract\DocumentInterface;
use Syndesi\MongoEntityManager\Contract\ActionMongoElementInterface;

class ActionMongoElement implements ActionMongoElementInterface
{
    public function __construct(
        private readonly ActionType $actionType,
        private readonly DocumentInterface $element,
    ) {
    }

    #[\Override]
    public function getAction(): ActionType
    {
        return $this->actionType;
    }

    #[\Override]
    public function getElement(): DocumentInterface
    {
        return $this->element;
    }
}
