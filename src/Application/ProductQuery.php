<?php 

namespace Application;

use Application\Entities\ProductData;

class ProductQuery {
    public function __construct(
        private Interfaces\ProductRepository $productRepository
        ){ 
    }

    public function execute(): array{
        $res = [];
        foreach($this->productRepository->getAllProducts() as $p) {
            $res[] = new \Application\ProductData(
                $p->getID(), $p->getName(),
                $p->getManufacturer(), $p->getCreator(),
                $p->getRatingCount(), $p->getAverageRating()
            );
        }
        return $res;
    }
}