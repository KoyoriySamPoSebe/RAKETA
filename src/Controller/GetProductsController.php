<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Controller;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Raketa\BackendTestTask\Http\JsonResponse;
use Raketa\BackendTestTask\Repository\ProductRepository;
use Raketa\BackendTestTask\View\ProductsView;

readonly class GetProductsController
{
    public function __construct(
        private ProductRepository $productRepository,
        private ProductsView      $productsView
    ) {
    }

    public function getByCategory(RequestInterface $request): ResponseInterface
    {
        $category = $request->getQueryParams()['category'];
        $products = $this->productRepository->getByCategory($category);

        $body = json_encode(
            $this->productsView->toArray($products),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        );

        return (new JsonResponse($body, 200));
    }
}
