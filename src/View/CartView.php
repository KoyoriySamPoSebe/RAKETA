<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\View;

use Raketa\BackendTestTask\Domain\Cart;
use Raketa\BackendTestTask\Domain\CartItem;

final class CartView
{
    public function toArray(Cart $cart): array
    {
        $items = array_map(
            fn (CartItem $item): array => [
                'item_uuid'  => $item->getUuid(),
                'price'      => $item->getPrice(),
                'quantity'   => $item->getQuantity(),
                'total'      => $item->getPrice() * $item->getQuantity(),
                'product'    => [
                    'id'        => $item->getProduct()->getId(),
                    'uuid'      => $item->getProduct()->getUuid(),
                    'name'      => $item->getProduct()->getName(),
                    'thumbnail' => $item->getProduct()->getThumbnail(),
                    'price'     => $item->getProduct()->getPrice(),
                ],
            ],
            $cart->getItems()
        );

        return [
            'uuid'           => $cart->getUuid(),
            'customer_uuid'  => $cart->getCustomer()->getUuid(),
            'payment_method' => $cart->getPaymentMethod(),
            'items'          => $items,
            'total'          => array_sum(array_column($items, 'total')),
        ];
    }
}
