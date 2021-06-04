<?php

namespace Application;

class RemoveRatingCommand {
    public function __construct(
        private Interfaces\RatingRepository $ratingRepository,
        private SignedInUserQuery $signedInUserQuery
    )
    {
    }

    public function execute(string $ratingID, string $productID): bool {
        $user = $this->signedInUserQuery->execute();
        $intRID = (int) $ratingID;
        $intPID = (int) $productID;
        if (!($user == null)) {
            $this->ratingRepository->removeRating($intRID, $intPID);
            return true;
        }
        return false;
    }
}
