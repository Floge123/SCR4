<?php

namespace Presentation\Controllers;

class Rating extends \Presentation\MVC\Controller{
    const PARAM_CONTEXT = 'ctx';
    const PARAM_PRODUCTID = 'productID';
    const PARAM_COMMENT = 'rc';
    const PARAM_GRADE = 'grade';
    const PARAM_RATINGID = 'ratingID';

    public function __construct(
        private \Application\SignedInUserQuery $signedInUserQuery,
        private \Application\ProductSearchQuery $productSearchQuery,
        private \Application\RatingQuery $ratingQuery,
        private \Application\SubmitRatingCommand $submitRatingCommand,
        private \Application\UpdateRatingCommand $updateRatingCommand,
        private \Application\RemoveRatingCommand $removeRatingCommand
    )
    {
    }

    public function POST_Submit(): \Presentation\MVC\ActionResult {
        if (!$this->tryGetParam(self::PARAM_PRODUCTID, $productID)) {
            return $this->redirectToUri($this->getParam(self::PARAM_CONTEXT));
        }
        $product = $this->productSearchQuery->executeIDFilter($productID);
        if ($product == null) {
            return $this->view("productlist", [
                'products' => $this->productQuery->execute(),
                'context' => $this->getRequestUri(),
                'user' => $this->signedInUserQuery->execute()
            ]);
        }
        $user = $this->signedInUserQuery->execute();
        if ($user === null) {
            return $this->view('login', [
                'user' => $this->signedInUserQuery->execute(),
                'userName' => '',
                'context' => $this->getRequestUri(),
                'errors' => ['You have to log in to review a product.']
            ]);
        }
        $comment = $this->getParam(self::PARAM_COMMENT);
        if ($comment == null) {
            $comment = '';
        }
        if (!$this->tryGetParam(self::PARAM_GRADE, $grade) || $grade == null) {
            return $this->view('detailView', [
                'context' => $this->getParam(self::PARAM_CONTEXT),
                'user' => $this->signedInUserQuery->execute(),
                'product' => $product,
                'ratings' => $this->ratingQuery->executeForProductID($productID),
                'rating' => null,
                'errors' => ['Enter valid Grade.']
            ]);
        }
        if (!$this->submitRatingCommand->execute($productID, $comment, $grade)) {
            return $this->view('detailView', [
                'context' => $this->getParam(self::PARAM_CONTEXT),
                'user' => $this->signedInUserQuery->execute(),
                'product' => $product,
                'ratings' => $this->ratingQuery->executeForProductID($productID),
                'rating' => null,
                'errors' => ['Rating couldn`t be added.']
            ]);
        }

        return $this->redirectToUri($this->getParam(self::PARAM_CONTEXT));
    }

    public function GET_Edit(): \Presentation\MVC\ActionResult {
        if (!$this->tryGetParam(self::PARAM_RATINGID, $ratingID)) {
            return $this->redirectToUri($this->getParam(self::PARAM_CONTEXT));
        }
        if (!$this->tryGetParam(self::PARAM_PRODUCTID, $productID)) {
            return $this->redirectToUri($this->getParam(self::PARAM_CONTEXT));
        }
        $rating = $this->ratingQuery->executeForRatingID($ratingID);
        $user = $this->signedInUserQuery->execute();
        $product = $this->productSearchQuery->executeIDFilter($rating->getProductID());
        if ($user === null) {
            return $this->view('login', [
                'user' => $this->signedInUserQuery->execute(),
                'userName' => '',
                'context' => $this->getRequestUri(),
                'errors' => ['You have to log in to review a product.']
            ]);
        }
        if ($user->getUserName() != $rating->getCreator()) {
            return $this->view('detailView', [
                'context' => $this->getParam(self::PARAM_CONTEXT),
                'user' => $this->signedInUserQuery->execute(),
                'ratings' => $this->ratingQuery->executeForProductID($productID),
                'rating' => null,
                'errors' => ['Insufficient Permission.']
            ]);
        }

        return $this->view('editRating', [
            'context' => $this->getParam(self::PARAM_CONTEXT),
            'user' => $user,
            'product' => $product,
            'ratings' => $this->ratingQuery->executeForProductID($productID),
            'rating' => null,
            'rating' => $rating
        ]);
    }

    public function POST_Update(): \Presentation\MVC\ActionResult {
        if (!$this->tryGetParam(self::PARAM_RATINGID, $ratingID)) {
            return $this->redirectToUri($this->getParam(self::PARAM_CONTEXT));
        }
        if (!$this->tryGetParam(self::PARAM_PRODUCTID, $productID)) {
            return $this->redirectToUri($this->getParam(self::PARAM_CONTEXT));
        }
        $rating = $this->ratingQuery->executeForRatingID($ratingID);
        $user = $this->signedInUserQuery->execute();
        $product = $this->productSearchQuery->executeIDFilter($rating->getProductID());
        if ($user === null) {
            return $this->view('login', [
                'user' => $this->signedInUserQuery->execute(),
                'userName' => '',
                'context' => $this->getRequestUri(),
                'errors' => ['You have to log in to review a product.']
            ]);
        }
        if ($user->getUserName() != $rating->getCreator()) {
            return $this->view('detailView', [
                'context' => $this->getParam(self::PARAM_CONTEXT),
                'user' => $this->signedInUserQuery->execute(),
                'product' => $product,
                'ratings' => $this->ratingQuery->executeForProductID($productID),
                'rating' => null,
                'errors' => ['Insufficient Permission.']
            ]);
        }
        $comment = $this->getParam(self::PARAM_COMMENT);
        if ($comment == null) {
            $comment = '';
        }
        if (!$this->tryGetParam(self::PARAM_GRADE, $grade) || $grade == null) {
            return $this->view('editRating', [
                'context' => $this->getParam(self::PARAM_CONTEXT),
                'user' => $this->signedInUserQuery->execute(),
                'product' => $this->productSearchQuery->executeIDFilter($rating->getProductID()),
                'rating' => $rating,
                'errors' => ['Enter valid Grade.']
            ]);
        }

        if (!$this->updateRatingCommand->execute($ratingID, $comment, $grade)) {
            return $this->view('detailView', [
                'context' => $this->getParam(self::PARAM_CONTEXT),
                'user' => $this->signedInUserQuery->execute(),
                'product' => $product,
                'ratings' => $this->ratingQuery->executeForProductID($productID),
                'rating' => null,
                'errors' => ['Failed to update Rating.']
            ]);
        }

        return $this->redirectToUri($this->getParam(self::PARAM_CONTEXT));
    }

    public function POST_Delete(): \Presentation\MVC\ActionResult {
        if (!$this->tryGetParam(self::PARAM_RATINGID, $ratingID)) {
            return $this->redirectToUri($this->getParam(self::PARAM_CONTEXT));
        }
        if (!$this->tryGetParam(self::PARAM_PRODUCTID, $productID)) {
            return $this->redirectToUri($this->getParam(self::PARAM_CONTEXT));
        }
        $rating = $this->ratingQuery->executeForRatingID($ratingID);
        $user = $this->signedInUserQuery->execute();
        $product = $this->productSearchQuery->executeIDFilter($rating->getProductID());
        if ($user === null) {
            return $this->view('login', [
                'user' => $this->signedInUserQuery->execute(),
                'userName' => '',
                'context' => $this->getRequestUri(),
                'errors' => ['You have to log in to review a product.']
            ]);
        }
        if ($user->getUserName() != $rating->getCreator()) {
            return $this->view('detailView', [
                'context' => $this->getParam(self::PARAM_CONTEXT),
                'user' => $this->signedInUserQuery->execute(),
                'product' => $product,
                'ratings' => $this->ratingQuery->executeForProductID($productID),
                'rating' => null,
                'errors' => ['Insufficient Permission.']
            ]);
        }

        if (!$this->removeRatingCommand->execute($ratingID, $productID)) {
            return $this->view('detailView', [
                'context' => $this->getParam(self::PARAM_CONTEXT),
                'user' => $this->signedInUserQuery->execute(),
                'product' => $product,
                'ratings' => $this->ratingQuery->executeForProductID($productID),
                'rating' => null,
                'errors' => ['Failed to remove Rating.']
            ]);
        }

        return $this->redirectToUri($this->getParam(self::PARAM_CONTEXT));
    }
}