<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Domain;

readonly class Product
{
    public function __construct(
        private int    $id,
        private string $uuid,
        private bool   $isActive,
        private string $category,
        private string $name,
        private string $description,
        private string $thumbnail,
        private float  $price
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }
    public function getUuid(): string
    {
        return $this->uuid;
    }
    public function isActive(): bool
    {
        return $this->isActive;
    }
    public function getCategory(): string
    {
        return $this->category;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getDescription(): string
    {
        return $this->description;
    }
    public function getThumbnail(): string
    {
        return $this->thumbnail;
    }
    public function getPrice(): float
    {
        return $this->price;
    }

    public function toArray(): array
    {
        return [
            'id'          => $this->id,
            'uuid'        => $this->uuid,
            'isActive'    => $this->isActive,
            'category'    => $this->category,
            'name'        => $this->name,
            'description' => $this->description,
            'thumbnail'   => $this->thumbnail,
            'price'       => $this->price,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            (int) $data['id'],
            $data['uuid'],
            (bool) $data['isActive'],
            $data['category'],
            $data['name'],
            $data['description'],
            $data['thumbnail'],
            (float) $data['price']
        );
    }
}
