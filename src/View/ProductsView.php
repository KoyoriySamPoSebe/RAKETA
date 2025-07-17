<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\View;

use Raketa\BackendTestTask\Domain\Product;

final class ProductsView
{
    /** @param Product[] $products */
    public function toArray(array $products): array
    {
        return array_map(
            fn (Product $p): array => [
                'id'          => $p->getId(),
                'uuid'        => $p->getUuid(),
                'category'    => $p->getCategory(),
                'name'        => $p->getName(),
                'description' => $p->getDescription(),
                'thumbnail'   => $p->getThumbnail(),
                'price'       => $p->getPrice(),
            ],
            $products
        );
    }
}
