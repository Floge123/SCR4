<?php
namespace Application;

class ProductSearchQuery {
    public function __construct(
        private Interfaces\ProductRepository $productRepository
    ){
    }

    public function executeIDFilter(string $id): ?\Application\ProductData {
        $p = $this->productRepository->getProductFromID($id);
        if ($p == null) {
            return null;
        }
        return new \Application\ProductData(
            $p->getID(), $p->getName(),
            $p->getManufacturer(), $p->getCreator(),
            $p->getRatingCount(), $p->getAverageRating(),
            $p->getDescription()
        );
    }

    public function executeNameFilter(string $filter): array{
        $res = [];
        foreach($this->productRepository->getProductsForName($filter) as $p) {
            $res[] = new \Application\ProductData(
                $p->getID(), $p->getName(),
                $p->getManufacturer(), $p->getCreator(),
                $p->getRatingCount(), $p->getAverageRating(),
                $p->getDescription()
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
                $p->getRatingCount(), $p->getAverageRating(),
                $p->getDescription()
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
                $p->getRatingCount(), $p->getAverageRating(),
                $p->getDescription()
            );
        }
        return $res;
    }

}