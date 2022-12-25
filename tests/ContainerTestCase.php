<?php

declare(strict_types=1);

namespace Syndesi\MongoEntityManager\Tests;

use Crell\Tukio\Dispatcher;
use Crell\Tukio\OrderedListenerProvider;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\Log\LoggerInterface;
use Selective\Container\Container;
use Selective\Container\Resolver\ConstructorResolver;

class ContainerTestCase extends ProphesizeTestCase
{
    protected Container $container;

    protected function setUp(): void
    {
        parent::setUp();
        $this->container = new Container();
        $this->container->addResolver(new ConstructorResolver($this->container));

        // add logging
        $loggerTestHandler = new TestHandler();
        $logger = (new Logger('logger'))
            ->pushHandler($loggerTestHandler)
            ->pushHandler(new StreamHandler(__DIR__.'/test.log', Logger::DEBUG));

        $this->container->set(TestHandler::class, $loggerTestHandler);
        $this->container->set(LoggerInterface::class, $logger);

        // register events
        $listenerProvider = new OrderedListenerProvider($this->container);
        $this->container->set(ListenerProviderInterface::class, $listenerProvider);
        $this->container->set(EventDispatcherInterface::class, new Dispatcher(
            $this->container->get(ListenerProviderInterface::class),
            $this->container->get(LoggerInterface::class)
        ));
    }

    protected function tearDown(): void
    {
        unset($this->container);
        parent::tearDown();
    }
}
