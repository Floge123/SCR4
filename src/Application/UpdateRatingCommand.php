<?php

namespace Application;

class UpdateRatingCommand {
    public function __construct(
        private Interfaces\RatingRepository $ratingRepository,
        private SignedInUserQuery $signedInUserQuery,
        private RatingQuery $ratingQuery,
        private ProductSearchQuery $productSearchQuery
    )
    {
    }

    public function execute(string $id, string $comment, string $grade): bool {
        $user = $this->signedInUserQuery->execute();
        $rating = $this->ratingQuery->executeForRatingID($id);
        $intID = (int) $id;
        $intGrade = (int) $grade;
        if (!($user == null) && $grade != null) {
            $this->ratingRepository->updateRating(
                $intID, $this->productSearchQuery->executeIDFilter($rating->getProductID())->getID(),
                $comment, $intGrade
            );
            return true;
        }
        return false;
    }
}
