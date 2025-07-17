<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Domain;

readonly class CartItem
{
    public function __construct(
        private string  $uuid,
        private Product $product,
        private float   $price,
        private int     $quantity
    ) {
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }
    public function getProduct(): Product
    {
        return $this->product;
    }
    public function getPrice(): float
    {
        return $this->price;
    }
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function toArray(): array
    {
        return [
            'uuid'     => $this->uuid,
            'product'  => $this->product->toArray(),
            'price'    => $this->price,
            'quantity' => $this->quantity,
        ];
    }

    public static function fromArray(array $data): self
    {
        $product = Product::fromArray($data['product']);
        return new self($data['uuid'], $product, $data['price'], $data['quantity']);
    }
}
