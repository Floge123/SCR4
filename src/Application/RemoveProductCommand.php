<?php

namespace Application;

class RemoveProductCommand {
    public function __construct(
        private Interfaces\ProductRepository $productRepository,
        private SignedInUserQuery $signedInUserQuery
    )
    {
    }

    public function execute(string $id): bool {
        $user = $this->signedInUserQuery->execute();
        if (!($user == null)) {
            $this->productRepository->removeProduct($id);
            return true;
        }
        return false;
    }
}
