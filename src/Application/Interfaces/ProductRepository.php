<?php

namespace Application\Interfaces;

interface ProductRepository{
    public function getProductsForName(string $name): array;
    public function getProductsForManufacturer(string $manufacturer): array;
    public function getProductsFromNameAndManufacturer(string $name, string $manufacturer): array;
    public function getAllProducts(): array;
    public function getProductFromID(string $id): ?\Application\Entities\Product;
    public function addProduct(string $name, string $manufacturer, string $creator, string $description): void;
    public function updateProduct(string $id, string $name, string $manufacturer, string $description): void;
    public function removeProduct(string $id): void;
}
