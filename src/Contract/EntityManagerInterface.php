<?php

declare(strict_types=1);

namespace Syndesi\MongoEntityManager\Contract;

use MongoDB\Client;
use Syndesi\MongoDataStructures\Contract\DocumentInterface;
use Syndesi\MongoEntityManager\Type\ActionType;

interface EntityManagerInterface
{
    public function add(ActionType $actionType, DocumentInterface $element): self;

    public function create(DocumentInterface $element): self;

    public function merge(DocumentInterface $element): self;

    public function delete(DocumentInterface $element): self;

    public function flush(): self;

    public function clear(): self;

    public function getClient(): Client;
}
