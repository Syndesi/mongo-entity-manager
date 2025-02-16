<?php

declare(strict_types=1);

namespace Syndesi\MongoEntityManager\Helper;

use Syndesi\MongoEntityManager\Contract\Event\LifecycleEventInterface;
use Syndesi\MongoEntityManager\Event\DocumentPostCreateEvent;
use Syndesi\MongoEntityManager\Event\DocumentPostDeleteEvent;
use Syndesi\MongoEntityManager\Event\DocumentPostMergeEvent;
use Syndesi\MongoEntityManager\Event\DocumentPreCreateEvent;
use Syndesi\MongoEntityManager\Event\DocumentPreDeleteEvent;
use Syndesi\MongoEntityManager\Event\DocumentPreMergeEvent;
use Syndesi\MongoEntityManager\Type\ActionMongoElement;
use Syndesi\MongoEntityManager\Type\ActionMongoElementType;
use Syndesi\MongoEntityManager\Type\ActionType;

class LifecycleEventHelper
{
    /**
     * @return LifecycleEventInterface[]
     */
    public static function getLifecycleEventForMongoActionElement(ActionMongoElement $actionMongoElement, bool $isPre): array
    {
        $eventClasses = [
            ActionMongoElementType::DOCUMENT->name => [
                'Pre' => [
                    ActionType::CREATE->name => DocumentPreCreateEvent::class,
                    ActionType::MERGE->name => DocumentPreMergeEvent::class,
                    ActionType::DELETE->name => DocumentPreDeleteEvent::class,
                ],
                'Post' => [
                    ActionType::CREATE->name => DocumentPostCreateEvent::class,
                    ActionType::MERGE->name => DocumentPostMergeEvent::class,
                    ActionType::DELETE->name => DocumentPostDeleteEvent::class,
                ],
            ],
        ];
        $elementType = ActionMongoElementHelper::getTypeFromActionMongoElement($actionMongoElement);
        if (array_key_exists($elementType->name, $eventClasses)) {
            $eventClass = $eventClasses[$elementType->name];
            if (array_key_exists($isPre ? 'Pre' : 'Post', $eventClass)) {
                $eventClass = $eventClass[$isPre ? 'Pre' : 'Post'];
                if (array_key_exists($actionMongoElement->getAction()->name, $eventClass)) {
                    $eventClass = $eventClass[$actionMongoElement->getAction()->name];

                    return [
                        new $eventClass($actionMongoElement->getElement()),
                    ];
                }
            }
        }
        throw new \LogicException("this line can not be reached");
    }
}
