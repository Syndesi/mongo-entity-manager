<?php

declare(strict_types=1);

namespace Syndesi\MongoEntityManager\Type;

use MongoDB\Client;
use MongoDB\Model\BSONDocument;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Syndesi\MongoDataStructures\Contract\DocumentInterface;
use Syndesi\MongoDataStructures\Type\Document;
use Syndesi\MongoEntityManager\Contract\EntityManagerInterface;
use Syndesi\MongoEntityManager\Event\PostFlushEvent;
use Syndesi\MongoEntityManager\Event\PreFlushEvent;
use Syndesi\MongoEntityManager\Exception\MongoEntityManagerException;
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
    private string $database;

    public function __construct(string $database, Client $client, EventDispatcherInterface $dispatcher, ?LoggerInterface $logger = null)
    {
        $this->database = $database;
        $this->client = $client;
        $this->dispatcher = $dispatcher;
        $this->logger = $logger;
    }

    #[\Override]
    public function add(ActionType $actionType, DocumentInterface $element): static
    {
        $actionMongoElement = new ActionMongoElement($actionType, $element);
        $this->queue[] = $actionMongoElement;

        return $this;
    }

    #[\Override]
    public function create(DocumentInterface $element): static
    {
        $this->add(ActionType::CREATE, $element);

        return $this;
    }

    #[\Override]
    public function merge(DocumentInterface $element): static
    {
        $this->add(ActionType::MERGE, $element);

        return $this;
    }

    #[\Override]
    public function delete(DocumentInterface $element): static
    {
        $this->add(ActionType::DELETE, $element);

        return $this;
    }

    #[\Override]
    public function flush(): static
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
            $collectionIdentifier = $element->getCollection();
            if (null === $collectionIdentifier) {
                throw new MongoEntityManagerException("Mongo action element must have property 'collection' set to non null value.");
            }
            $collection = $this->client->selectDatabase($this->database)->selectCollection($collectionIdentifier);

            if (ActionType::CREATE === $actionMongoElement->getAction()) {
                $this->logger?->debug(sprintf(
                    "Creating element of type %s",
                    get_class($element)
                ), [
                    'collection' => $collectionIdentifier,
                    'identifier' => $element->getIdentifier(),
                    'properties' => $element->getProperties(),
                ]);
                $collection->insertOne($element->getProperties());
            }
            if (ActionType::MERGE === $actionMongoElement->getAction()) {
                $this->logger?->debug(sprintf(
                    "Updating element of type %s",
                    get_class($element)
                ), [
                    'collection' => $collectionIdentifier,
                    'identifier' => $element->getIdentifier(),
                    'properties' => $element->getProperties(),
                ]);
                $collection->updateOne(
                    [
                        '_id' => $element->getIdentifier(),
                    ],
                    [
                        '$set' => $element->getProperties(),
                    ],
                    [
                        'upsert' => true,
                    ]
                );
            }
            if (ActionType::DELETE === $actionMongoElement->getAction()) {
                $this->logger?->debug(sprintf(
                    "Deleting element of type %s",
                    get_class($element)
                ), [
                    'collection' => $collectionIdentifier,
                    'identifier' => $element->getIdentifier(),
                ]);
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

    public function getOneByIdentifier(string $collection, int|string $identifier): ?DocumentInterface
    {
        $res = $this->client->selectDatabase($this->database)->selectCollection($collection)
            ->findOne(
                [
                    '_id' => $identifier,
                ]
            );
        if (null === $res) {
            return null;
        }
        if (!($res instanceof BSONDocument)) {
            throw new MongoEntityManagerException(sprintf("Mongo returned non BSON document of the type '%s'.", is_object($res) ? get_class($res) : gettype($res)));
        }
        $properties = $res->getArrayCopy();
        unset($properties['_id']);

        $documentIdentifier = $res->offsetGet('_id');
        if (null === $documentIdentifier) {
            throw new MongoEntityManagerException("Mongo document does not contain '_id' property.");
        }

        return (new Document())
            ->addProperties($properties)
            ->setIdentifier($documentIdentifier)
            ->setCollection($collection);
    }

    #[\Override]
    public function clear(): static
    {
        $this->queue = [];

        return $this;
    }

    #[\Override]
    public function getClient(): Client
    {
        return $this->client;
    }

    public function getDatabase(): ?string
    {
        return $this->database;
    }
}
