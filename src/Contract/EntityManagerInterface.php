<?php

declare(strict_types=1);

namespace Syndesi\MongoEntityManager\Contract;

use MongoDB\Client;
use Syndesi\MongoDataStructures\Contract\DocumentInterface;
use Syndesi\MongoEntityManager\Type\ActionType;

interface EntityManagerInterface
{
    public function add(ActionType $actionType, DocumentInterface $element): static;

    public function create(DocumentInterface $element): static;

    public function merge(DocumentInterface $element): static;

    public function delete(DocumentInterface $element): static;

    public function flush(): static;

    public function clear(): static;

    public function getClient(): Client;
}
