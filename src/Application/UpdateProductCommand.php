<?php

namespace Application;

class UpdateProductCommand {
    public function __construct(
        private Interfaces\ProductRepository $productRepository,
        private ProductSearchQuery $productSearchQuery,
        private SignedInUserQuery $signedInUserQuery
    )
    {
    }

    public function execute(string $id, string $productName, string $manufacturer, string $description): bool {
        $user = $this->signedInUserQuery->execute();
        $product = $this->productSearchQuery->executeIDFilter($id);
        if (!($user == null) && $user->getUserName() == $product->getCreator()) {
            $this->productRepository->updateProduct($id, $productName, $manufacturer, $description);
            return true;
        }
        return false;
    }
}
