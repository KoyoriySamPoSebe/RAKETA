<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Infrastructure;

use Psr\Log\LoggerInterface;
use Redis;
use RedisException;
use Raketa\BackendTestTask\Domain\Cart;

readonly class Connector
{
    public function __construct(
        private Redis          $redis,
        private LoggerInterface $logger,
        private int             $ttl = 86400
    ) {
        try {
            $pong = $this->redis->ping();
        } catch (RedisException $e) {
            $this->logger->error('Redis unavailable', ['exception' => $e]);
            throw new ConnectorException('Redis unavailable', 503, $e);
        }

        if ($pong !== '+PONG') {
            $this->logger->error('Redis ping failed', ['pong' => $pong]);
            throw new ConnectorException('Redis ping failed', 503);
        }
    }

    public function get(string $key): ?Cart
    {
        try {
            $raw = $this->redis->get($key);
        } catch (RedisException $e) {
            $this->logger->error('Error fetching from Redis', ['exception' => $e]);
            throw new ConnectorException('Redis unavailable', 503, $e);
        }

        if ($raw === false) {
            return null;
        }

        $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        return Cart::fromArray($data);
    }

    public function set(string $key, Cart $cart): void
    {
        $json = json_encode($cart->toArray(), JSON_THROW_ON_ERROR);
        try {
            $this->redis->setex($key, $this->ttl, $json);
        } catch (RedisException $e) {
            $this->logger->error('Error writing to Redis', ['exception' => $e]);
            throw new ConnectorException('Error writing cart', 503, $e);
        }
    }

    public function has(string $key): bool
    {
        try {
            return $this->redis->exists($key) > 0;
        } catch (RedisException $e) {
            $this->logger->error('Error checking key in Redis', ['exception' => $e]);
            throw new ConnectorException('Redis unavailable', 503, $e);
        }
    }
}
