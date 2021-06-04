<?php

namespace Application\Entities;

class Product {
    public function __construct(
        private int $id,
        private string $name,
        private string $manufacturer,
        private string $creator,
        private int $ratingCount,
        private float $averageRating,
        private string $description
    ){
    }

    public function getID(): int{
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCreator(): string
    {
        return $this->creator;
    }

    public function getManufacturer(): string
    {
        return $this->manufacturer;
    }

    public function getRatingCount(): int
    {
        return $this->ratingCount;
    }

    public function getAverageRating(): float
    {
        return $this->averageRating;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}
