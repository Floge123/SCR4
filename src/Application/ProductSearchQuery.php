<?php
namespace Application;

class ProductSearchQuery {
    public function __construct(
        private Interfaces\ProductRepository $productRepository
    ){
    }

    public function executeNameFilter(string $filter): array{
        $res = [];
        foreach($this->productRepository->getProductsForName($filter) as $p) {
            $res[] = new \Application\ProductData(
                $p->getID(), $p->getName(),
                $p->getManufacturer(), $p->getCreator(),
                $p->getRatingCount(), $p->getAverageRating()
            );
        }
        return $res;
    }

    public function executeManuFilter(string $filter): array{
        $res = [];
        foreach($this->productRepository->getProductsForManufacturer($filter) as $p) {
            $res[] = new \Application\ProductData(
                $p->getID(), $p->getName(),
                $p->getManufacturer(), $p->getCreator(),
                $p->getRatingCount(), $p->getAverageRating()
            );
        }
        return $res;
    }

    public function executeFullFilter(string $nameFilter, string $manuFilter): array{
        $res = [];
        foreach($this->productRepository->getProductsFromNameAndManufacturer($nameFilter, $manuFilter) as $p) {
            $res[] = new \Application\ProductData(
                $p->getID(), $p->getName(),
                $p->getManufacturer(), $p->getCreator(),
                $p->getRatingCount(), $p->getAverageRating()
            );
        }
        return $res;
    }

}