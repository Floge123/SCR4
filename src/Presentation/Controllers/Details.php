<?php

namespace Presentation\Controllers;

class Details extends \Presentation\MVC\Controller {
    public function __construct(
        private \Application\SignedInUserQuery $signedInUserQuery,
        private \Application\ProductSearchQuery $productSearchQuery,
        private \Application\RatingQuery $ratingQuery,
        private \Application\ProductQuery $productQuery
    )
    {
    }

    public function GET_Index(): \Presentation\MVC\ActionResult {
        if (!$this->tryGetParam('product', $productID)) {
            return $this->view("productlist", [
                'products' => $this->productQuery->execute(),
                'context' => $this->getRequestUri(),
                'user' => $this->signedInUserQuery->execute(),
                'errors' => ['Invalid Product selected.']
            ]);
        }
        $product = $this->productSearchQuery->executeIDFilter($productID);
        if ($product == null) {
            return $this->view("productlist", [
                'products' => $this->productQuery->execute(),
                'context' => $this->getRequestUri(),
                'user' => $this->signedInUserQuery->execute(),
                'errors' => ['Invalid Product selected.']
            ]);
        }
        return $this->view("detailView", [
            'user' => $this->signedInUserQuery->execute(),
            'product' => $product,
            'ratings' => $this->ratingQuery->executeForProductID($productID),
            'rating' => null,
            'context' => $this->getRequestUri()
        ]);
    }
}