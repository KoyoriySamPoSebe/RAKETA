<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Infrastructure;

use Psr\Log\LoggerInterface;
use Redis;

abstract class ConnectorFacade
{
    protected Connector $connector;

    public function __construct(
        private readonly string         $host,
        private readonly int            $port     = 6379,
        private readonly ?string        $password = null,
        private readonly ?int           $dbindex  = null,
        private readonly LoggerInterface $logger
    ) {
        $this->buildConnector();
    }

    private function buildConnector(): void
    {
        $redis = new Redis();
        $redis->connect($this->host, $this->port);

        if ($this->password !== null) {
            $redis->auth($this->password);
        }

        if ($this->dbindex !== null) {
            $redis->select($this->dbindex);
        }

        $this->connector = new Connector($redis, $this->getLogger(), 86400);
    }

    abstract protected function getLogger(): LoggerInterface;
}
