<?php
declare(strict_types=1);

namespace Raketa\BackendTestTask\Repository;

use Doctrine\DBAL\Connection;
use Raketa\BackendTestTask\Domain\Product;

class ProductRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getByUuid(string $uuid): Product
    {
        $row = $this->connection->fetchAssociative(
            'SELECT id, uuid, is_active, category, name, description, thumbnail, price
             FROM products
             WHERE uuid = ?',
            [$uuid]
        );

        if ($row === false) {
            throw new \Exception('Product not found');
        }

        return $this->make($row);
    }

    public function getByCategory(string $category): array
    {
        $rows = $this->connection->fetchAllAssociative(
            'SELECT id, uuid, is_active, category, name, description, thumbnail, price
             FROM products
             WHERE is_active = 1
               AND category = ?',
            [$category]
        );

        return array_map(
            fn(array $row): Product => $this->make($row),
            $rows
        );
    }

    public function make(array $row): Product
    {
        return new Product(
            $row['id'],
            $row['uuid'],
            $row['is_active'],
            $row['category'],
            $row['name'],
            $row['description'],
            $row['thumbnail'],
            $row['price'],
        );
    }
}
