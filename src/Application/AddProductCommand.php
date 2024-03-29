<?php

namespace Application;

class AddProductCommand {
    public function __construct(
        private Interfaces\ProductRepository $productRepository,
        private SignedInUserQuery $signedInUserQuery
    )
    {
    }

    public function execute(string $productName, string $manufacturer, string $description): bool {
        $user = $this->signedInUserQuery->execute();
        if (!($user == null)) {
            $this->productRepository->addProduct($productName, $manufacturer, $user->getUserName(), $description);
            return true;
        }
        return false;
    }
}
