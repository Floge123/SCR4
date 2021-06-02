<?php

namespace Application\Interfaces;

interface ProductRepository{
    public function getProductsForName(string $name): array;
    public function getProductsForManufacturer(string $manufacturer): array;
    public function getProductsFromNameAndManufacturer(string $name, string $manufacturer): array;
    public function getAllProducts(): array;
    public function addProduct(string $name, string $manufacturer, string $creator): void;
}
