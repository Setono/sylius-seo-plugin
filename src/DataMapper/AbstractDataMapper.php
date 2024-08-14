<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\DataMapper;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Setono\CompositeCompilerPass\CompositeService;

/**
 * @template T of object
 *
 * @extends CompositeService<T>
 */
abstract class AbstractDataMapper extends CompositeService implements LoggerAwareInterface
{
    protected LoggerInterface $logger;

    public function __construct()
    {
        $this->logger = new NullLogger();
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}
