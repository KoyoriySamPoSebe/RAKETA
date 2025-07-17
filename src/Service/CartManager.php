<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Service;

use Psr\Log\LoggerInterface;
use Raketa\BackendTestTask\Domain\Cart;
use Raketa\BackendTestTask\Domain\CartItem;
use Raketa\BackendTestTask\Domain\Customer;
use Raketa\BackendTestTask\Repository\CartRepository;
use Raketa\BackendTestTask\Repository\ProductRepository;
use Raketa\BackendTestTask\Infrastructure\ConnectorException;
use Ramsey\Uuid\Guid\Guid;

readonly class CartManager
{
    public function __construct(
        private CartRepository    $cartRepo,
        private ProductRepository $productRepo,
        private LoggerInterface   $logger,
        private string            $defaultPaymentMethod,
    ) {
    }

    public function generateCartUuid(): string
    {
        return Guid::uuid4()->toString();
    }

    /**
     * @throws ConnectorException
     */
    public function getCart(string $uuid, Customer $customer): Cart
    {
        try {
            $cart = $this->cartRepo->fetch($uuid);
        } catch (ConnectorException $e) {
            $this->logger->error(
                'Error fetching cart from cache',
                ['exception' => $e, 'cartUuid' => $uuid]
            );
            throw $e;
        }

        if ($cart === null) {
            $this->logger->info("Cart {$uuid} not found, creating new one");
            return new Cart(
                $uuid,
                $customer,
                $this->defaultPaymentMethod,
                []
            );
        }

        return $cart;
    }

    public function saveCart(Cart $cart): void
    {
        try {
            $this->cartRepo->save($cart);
        } catch (ConnectorException $e) {
            $this->logger->error(
                'Error writing cart to cache',
                ['exception' => $e, 'cartUuid' => $cart->getUuid()]
            );
        }
    }

    /**
     * @throws ConnectorException
     */
    public function addToCart(
        string   $productUuid,
        int      $quantity,
        Customer $customer,
        ?string  $cartUuid = null
    ): Cart {
        $product = $this->productRepo->getByUuid($productUuid);
        $uuid    = $cartUuid ?? $this->generateCartUuid();
        $cart    = $this->getCart($uuid, $customer);

        $cart->addItem(new CartItem(
            Guid::uuid4()->toString(),
            $product,
            $product->getPrice(),
            $quantity
        ));

        $this->saveCart($cart);

        return $cart;
    }
}
