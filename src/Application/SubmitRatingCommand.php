<?php

namespace Application;

class SubmitRatingCommand {
    public function __construct(
        private Interfaces\RatingRepository $ratingRepository,
        private SignedInUserQuery $signedInUserQuery
    )
    {
    }

    public function execute(string $productID, string $comment, string $grade): bool {
        $user = $this->signedInUserQuery->execute();
        $intID = (int) $productID;
        $intGrade = (int) $grade;
        if (!($user == null) && $grade != null) {
            $this->ratingRepository->addRating($user->getUserName(), $intID, $intGrade, $comment);
            return true;
        }
        return false;
    }
}
