<?php

declare(strict_types=1);

namespace Syndesi\MongoEntityManager\Helper;

use Syndesi\MongoDataStructures\Contract\DocumentInterface;
use Syndesi\MongoEntityManager\Type\ActionMongoElement;
use Syndesi\MongoEntityManager\Type\ActionMongoElementType;

class ActionMongoElementHelper
{
    /**
     * @psalm-suppress InvalidReturnType
     */
    public static function getTypeFromActionMongoElement(ActionMongoElement $actionMongoElement): ActionMongoElementType
    {
        $element = $actionMongoElement->getElement();
        if ($element instanceof DocumentInterface) {
            return ActionMongoElementType::DOCUMENT;
        }
    }
}
