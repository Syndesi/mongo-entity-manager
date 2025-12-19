<?php

declare(strict_types=1);

namespace Syndesi\MongoEntityManager\Event;

use Syndesi\MongoDataStructures\Contract\DocumentInterface;
use Syndesi\MongoEntityManager\Contract\Event\DocumentPreCreateEventInterface;
use Syndesi\MongoEntityManager\Trait\StoppableEventTrait;

class DocumentPreCreateEvent implements DocumentPreCreateEventInterface
{
    use StoppableEventTrait;

    public function __construct(private DocumentInterface $element)
    {
    }

    #[\Override]
    public function getElement(): DocumentInterface
    {
        return $this->element;
    }
}
