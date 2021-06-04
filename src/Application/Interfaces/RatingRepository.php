<?php

namespace Application\Interfaces;

interface RatingRepository {
    public function addRating(string $userName, int $productID, int $grade, string $comment): void;
    public function getRatingsForProduct(int $productID): array;
    public function getRating(int $ratingID): ?\Application\Entities\Rating;
    public function updateRating(int $ratingID, int $productID, string $comment, int $grade): void;
    public function removeRating(int $ratingID, int $productID): void;
}
