<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Controller;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Raketa\BackendTestTask\Http\JsonResponse;
use Raketa\BackendTestTask\Service\CartManager;
use Raketa\BackendTestTask\View\CartView;

readonly class AddToCartController
{
    public function __construct(
        private CartManager $cartManager,
        private CartView    $cartView
    ) {
    }

    public function addToCart(RequestInterface $request): ResponseInterface
    {
        $data     = $request->getParsedBody();
        $customer = $request->getAttribute('customer');

        $cart = $this->cartManager->addToCart(
            $data['productUuid'],
            (int) $data['quantity'],
            $customer,
            $data['cartUuid'] ?? null
        );

        $payload = [
            'status' => 'success',
            'cart'   => $this->cartView->toArray($cart),
        ];

        return new JsonResponse(
            json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            200
        );
    }
}
