<?php

declare(strict_types=1);

namespace Syndesi\MongoEntityManager\Exception;

class InvalidArgumentException extends \Exception
{
    public static function createForIdentifierIsNull(): self
    {
        return new InvalidArgumentException("Identifier can not be null");
    }
}
