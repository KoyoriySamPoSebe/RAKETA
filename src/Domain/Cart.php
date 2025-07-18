<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Domain;

final class Cart
{
    public function __construct(
        private readonly string   $uuid,
        private readonly Customer $customer,
        private readonly string   $paymentMethod,
        /** @var CartItem[] */
        private array            $items = []
    ) {
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function getPaymentMethod(): string
    {
        return $this->paymentMethod;
    }

    /** @return CartItem[] */
    public function getItems(): array
    {
        return $this->items;
    }

    public function addItem(CartItem $item): void
    {
        $this->items[] = $item;
    }

    public function toArray(): array
    {
        return [
            'uuid'          => $this->uuid,
            'customer'      => [
                'id'         => $this->customer->getId(),
                'uuid'       => $this->customer->getUuid(),
                'firstName'  => $this->customer->getFirstName(),
                'middleName' => $this->customer->getMiddleName(),
                'lastName'   => $this->customer->getLastName(),
                'email'      => $this->customer->getEmail(),
            ],
            'paymentMethod' => $this->paymentMethod,
            'items'         => array_map(fn (CartItem $i) => $i->toArray(), $this->items),
        ];
    }

    public static function fromArray(array $data): self
    {
        $customer = Customer::fromArray($data['customer'] ?? []);
        $cart     = new self(
            (string) $data['uuid'],
            $customer,
            (string) $data['paymentMethod'],
            []
        );

        foreach ($data['items'] as $itemData) {
            $cart->addItem(CartItem::fromArray($itemData));
        }

        return $cart;
    }
}
