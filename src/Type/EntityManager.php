<?php

declare(strict_types=1);

namespace Syndesi\MongoEntityManager\Type;

use MongoDB\Client;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Syndesi\MongoDataStructures\Contract\DocumentInterface;
use Syndesi\MongoEntityManager\Contract\EntityManagerInterface;
use Syndesi\MongoEntityManager\Event\PostFlushEvent;
use Syndesi\MongoEntityManager\Event\PreFlushEvent;
use Syndesi\MongoEntityManager\Helper\LifecycleEventHelper;

class EntityManager implements EntityManagerInterface
{
    private Client $client;
    private ?LoggerInterface $logger;
    /**
     * @var ActionMongoElement[]
     */
    private array $queue = [];
    private EventDispatcherInterface $dispatcher;
    private ?string $database = null;

    public function __construct(string $database, Client $client, EventDispatcherInterface $dispatcher, ?LoggerInterface $logger = null)
    {
        $this->database = $database;
        $this->client = $client;
        $this->dispatcher = $dispatcher;
        $this->logger = $logger;
    }

    public function add(ActionType $actionType, DocumentInterface $element): self
    {
        $actionMongoElement = new ActionMongoElement($actionType, $element);
        $this->queue[] = $actionMongoElement;

        return $this;
    }

    public function create(DocumentInterface $element): self
    {
        $this->add(ActionType::CREATE, $element);

        return $this;
    }

    public function merge(DocumentInterface $element): self
    {
        $this->add(ActionType::MERGE, $element);

        return $this;
    }

    public function delete(DocumentInterface $element): self
    {
        $this->add(ActionType::DELETE, $element);

        return $this;
    }

    public function flush(): self
    {
        $this->logger?->debug("Dispatching PreFlushEvent");
        $this->dispatcher->dispatch(new PreFlushEvent());
        foreach ($this->queue as $actionMongoElement) {
            $events = LifecycleEventHelper::getLifecycleEventForMongoActionElement($actionMongoElement, true);
            foreach ($events as $event) {
                $this->logger?->debug(sprintf("Dispatching %s", (new \ReflectionClass($event))->getShortName()));
                $this->dispatcher->dispatch($event);
            }

            $element = $actionMongoElement->getElement();
            $collection = $this->client->selectDatabase($this->database)->selectCollection($element->getCollection());

            if (ActionType::CREATE === $actionMongoElement->getAction()) {
                $collection->insertOne($element->getProperties());
            }
            if (ActionType::MERGE === $actionMongoElement->getAction()) {
                $collection->updateOne(
                    [
                        '_id' => $element->getIdentifier(),
                    ],
                    [
                        '$set' => $element->getProperties(),
                    ]
                );
            }
            if (ActionType::DELETE === $actionMongoElement->getAction()) {
                $collection->deleteOne(['_id' => $element->getIdentifier()]);
            }

            $events = LifecycleEventHelper::getLifecycleEventForMongoActionElement($actionMongoElement, false);
            foreach ($events as $event) {
                $this->logger?->debug(sprintf("Dispatching %s", (new \ReflectionClass($event))->getShortName()), [
                    'element' => $event->getElement(),
                ]);
                $this->dispatcher->dispatch($event);
            }
        }

        $this->clear();

        $this->logger?->debug("Dispatching PostFlushEvent");
        $this->dispatcher->dispatch(new PostFlushEvent());

        return $this;
    }

    public function clear(): self
    {
        $this->queue = [];

        return $this;
    }

    public function getClient(): Client
    {
        return $this->client;
    }
}
