<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Repository;

use Raketa\BackendTestTask\Domain\Cart;
use Raketa\BackendTestTask\Infrastructure\Connector;

class CartRepository
{
    public function __construct(private Connector $connector)
    {
    }

    public function fetch(string $uuid): ?Cart
    {
        return $this->connector->get($uuid);
    }

    public function save(Cart $cart): void
    {
        $this->connector->set($cart->getUuid(), $cart);
    }
}
