<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Controller;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Raketa\BackendTestTask\Http\JsonResponse;
use Raketa\BackendTestTask\Service\CartManager;
use Raketa\BackendTestTask\View\CartView;

readonly class GetCartController
{
    public function __construct(
        private CartManager $cartManager,
        private CartView    $cartView
    ) {
    }

    public function getCart(RequestInterface $request): ResponseInterface
    {
        $params   = $request->getQueryParams();
        $customer = $request->getAttribute('customer');
        $uuid     = $params['cartUuid'] ?? '';

        $cart = $this->cartManager->getCart($uuid, $customer);
        if ($cart === null) {
            $body = json_encode(
                ['message' => 'Cart not found'],
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
            );
            return new JsonResponse($body, 404);
        }

        $body = json_encode(
            $this->cartView->toArray($cart),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        );
        return new JsonResponse($body, 200);
    }
}
