<?php

namespace Application;

class RatingQuery {
    public function __construct(
        private Interfaces\RatingRepository $ratingRepository
    ){
    }

    public function executeForProductID(string $productID): array{
        $res = [];
        $intID = (int) $productID;
        foreach($this->ratingRepository->getRatingsForProduct($intID) as $p) {
            $res[] = new \Application\RatingData(
                $p->getID(), $p->getCreator(), $p->getProductID(),
                $p->getCreateDate(), $p->getGrade(), $p->getComment()
            );
        }
        return $res;
    }

    public function executeForRatingID(string $ratingID): ?\Application\Entities\Rating {
        $intID = (int) $ratingID;
        return $this->ratingRepository->getRating($intID);
    }
}