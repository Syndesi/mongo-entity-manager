<?php

declare(strict_types=1);

namespace Syndesi\MongoEntityManager\Tests;

use PHPUnit\Framework\TestCase;
use Prophecy\Prophet;

class ProphesizeTestCase extends TestCase
{
    protected Prophet $prophet;

    protected function setUp(): void
    {
        $this->prophet = new Prophet();
    }

    protected function tearDown(): void
    {
        $this->prophet->checkPredictions();
    }
}
