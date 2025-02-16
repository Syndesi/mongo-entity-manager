<?php

declare(strict_types=1);

namespace Syndesi\MongoEntityManager\Helper;

use Syndesi\MongoEntityManager\Type\ActionMongoElement;
use Syndesi\MongoEntityManager\Type\ActionMongoElementType;

class ActionMongoElementHelper
{
    public static function getTypeFromActionMongoElement(ActionMongoElement $_): ActionMongoElementType
    {
        return ActionMongoElementType::DOCUMENT;
    }
}
