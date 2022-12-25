<?php

declare(strict_types=1);

namespace Syndesi\MongoEntityManager\Event;

use Syndesi\MongoDataStructures\Contract\DocumentInterface;
use Syndesi\MongoEntityManager\Contract\Event\DocumentPostDeleteEventInterface;
use Syndesi\MongoEntityManager\Trait\StoppableEventTrait;

class DocumentPostDeleteEvent implements DocumentPostDeleteEventInterface
{
    use StoppableEventTrait;

    public function __construct(private DocumentInterface $element)
    {
    }

    public function getElement(): DocumentInterface
    {
        return $this->element;
    }
}
