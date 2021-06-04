<?php

namespace Presentation\Controllers;

use Application\ProductQuery;
use Application\ProductSearchQuery;

class Products extends \Presentation\MVC\Controller {
    const PARAM_NAME_FILTER = 'nameFilter';
    const PARAM_MANU_FILTER = 'manFilter';
    const PARAM_ADD_NAME = 'pn';
    const PARAM_ADD_MANUFACTURER = 'pm';
    const PARAM_ADD_DESCRIPTION = 'pd';
    const PARAM_CONTEXT = 'ctx';

    public function __construct(
        private \Application\ProductQuery $productQuery,
        private \Application\ProductSearchQuery $productSearchQuery,
        private \Application\SignedInUserQuery $signedInUserQuery,
        private \Application\AddProductCommand $addProductCommand,
        private \Application\UpdateProductCommand $updateProductCommand,
        private \Application\RemoveProductCommand $removeProductCommand

    ){
    }

    public function GET_Index(): \Presentation\MVC\ActionResult {
        return $this->view("productlist", [
            'products' => $this->productQuery->execute(),
            'context' => $this->getRequestUri(),
            'user' => $this->signedInUserQuery->execute()
        ]);
    }

    public function GET_Search(): \Presentation\MVC\ActionResult {
        $this->tryGetParam(self::PARAM_NAME_FILTER, $nameValue);
        $this->tryGetParam(self::PARAM_MANU_FILTER, $manuValue);
        $usedName = strlen($nameValue) > 0;
        $usedManu = strlen($manuValue) > 0;
        if ($usedName) {
            if ($usedManu) {
                $products = $this->productSearchQuery->executeFullFilter($nameValue, $manuValue);
            } else {
                $products = $this->productSearchQuery->executeNameFilter($nameValue);
            }
        } else if ($usedManu) {
            $products = $this->productSearchQuery->executeManuFilter($manuValue);
        } else {
            //get all products
            $products = $this->productQuery->execute();
        }

        return $this->view('productSearch', [
            'products' => $products,
            'nameFilter' => $this->tryGetParam(self::PARAM_NAME_FILTER, $value) ? $value : null,
            'manFilter' => $this->tryGetParam(self::PARAM_MANU_FILTER, $value) ? $value : null,
            'context' => $this->getRequestUri(),
            'user' => $this->signedInUserQuery->execute()
        ]);
    }

    public function GET_Add(): \Presentation\MVC\ActionResult {
        $user = $this->signedInUserQuery->execute();
        if ($user === null) {
            return $this->view('login', [
                'user' => $this->signedInUserQuery->execute(),
                'userName' => '',
                'context' => $this->getRequestUri(),
                'errors' => ['You have to log in to add a product.']
            ]);
        }
        return $this->view('addProduct', [
            'context' => $this->getRequestUri(),
            'user' => $this->signedInUserQuery->execute(),
            'productName' => $this->tryGetParam(self::PARAM_ADD_NAME, $value) ? $value : '',
            'manufacturer' => $this->tryGetParam(self::PARAM_ADD_MANUFACTURER, $value) ? $value : '',
            'description' => $this->tryGetParam(self::PARAM_ADD_DESCRIPTION, $value) ? $value : ''
        ]);
    }

    public function POST_Add(): \Presentation\MVC\ActionResult {
        $this->tryGetParam(self::PARAM_ADD_NAME, $newName);
        $this->tryGetParam(self::PARAM_ADD_MANUFACTURER, $newManu);
        $this->tryGetParam(self::PARAM_ADD_DESCRIPTION, $newDescription);
        $errors = [];
        if (strlen($newName) <= 0) {
            $errors[] = 'Enter a Product name.';
        }
        if (strlen($newManu) <= 0) {
            $errors[] = 'Enter a Manufacturer.';
        }
        if (sizeof($errors) != 0) {
            return $this->view('addProduct', [
                'context' => $this->getRequestUri(),
                'user' => $this->signedInUserQuery->execute(),
                'productName' => $this->tryGetParam(self::PARAM_ADD_NAME, $value) ? $value : '',
                'manufacturer' => $this->tryGetParam(self::PARAM_ADD_MANUFACTURER, $value) ? $value : '',
                'description' => $this->tryGetParam(self::PARAM_ADD_DESCRIPTION, $value) ? $value : '',
                'errors' => $errors
            ]);
        }

        if (!$this->addProductCommand->execute($newName, $newManu, $newDescription)) {
            return $this->view('addProduct', [
                'context' => $this->getRequestUri(),
                'user' => $this->signedInUserQuery->execute(),
                'productName' => $this->tryGetParam(self::PARAM_ADD_NAME, $value) ? $value : '',
                'manufacturer' => $this->tryGetParam(self::PARAM_ADD_MANUFACTURER, $value) ? $value : '',
                'description' => $this->tryGetParam(self::PARAM_ADD_DESCRIPTION, $value) ? $value : '',
                'errors' => ['Product couldn`t be added.']
            ]);
        }

        return $this->redirect('Products', 'Index');
    }

    public function GET_Edit(): \Presentation\MVC\ActionResult {
        if (!$this->tryGetParam('product', $productID)) {
            return $this->redirectToUri($this->getParam(self::PARAM_CONTEXT));
        }
        $user = $this->signedInUserQuery->execute();
        $product = $this->productSearchQuery->executeIDFilter($productID);
        if ($product == null) {
            return $this->redirectToUri($this->getParam(self::PARAM_CONTEXT));
        }
        if ($user->getUserName() != $product->getCreator()) {
            return $this->redirectToUri($this->getParam(self::PARAM_CONTEXT));
        }
        return $this->view('editProduct', [
            'user' => $user,
            'product' => $product,
            'context' => $this->getParam(self::PARAM_CONTEXT),
            'productName' => $product->getName(),
            'manufacturer' => $product->getManufacturer(),
            'description' => $product->getDescription()
        ]);
    }

    public function POST_Update(): \Presentation\MVC\ActionResult {
        $this->tryGetParam('productID', $productID);
        $this->tryGetParam(self::PARAM_ADD_NAME, $newName);
        $this->tryGetParam(self::PARAM_ADD_MANUFACTURER, $newManu);
        $this->tryGetParam(self::PARAM_ADD_DESCRIPTION, $newDescription);
        $errors = [];
        if (strlen($newName) <= 0) {
            $errors[] = 'Enter a Product name.';
        }
        if (strlen($newManu) <= 0) {
            $errors[] = 'Enter a Manufacturer.';
        }
        if (sizeof($errors) != 0) {
            return $this->view('editProduct', [
                'context' => $this->getRequestUri(),
                'user' => $this->signedInUserQuery->execute(),
                'productName' => $this->tryGetParam(self::PARAM_ADD_NAME, $value) ? $value : '',
                'manufacturer' => $this->tryGetParam(self::PARAM_ADD_MANUFACTURER, $value) ? $value : '',
                'description' => $this->tryGetParam(self::PARAM_ADD_DESCRIPTION, $value) ? $value : '',
                'errors' => $errors
            ]);
        }
        if (!$this->updateProductCommand->execute($productID, $newName, $newManu, $newDescription)) {
            return $this->view('editProduct', [
                'context' => $this->getRequestUri(),
                'user' => $this->signedInUserQuery->execute(),
                'productName' => $this->tryGetParam(self::PARAM_ADD_NAME, $value) ? $value : '',
                'manufacturer' => $this->tryGetParam(self::PARAM_ADD_MANUFACTURER, $value) ? $value : '',
                'description' => $this->tryGetParam(self::PARAM_ADD_DESCRIPTION, $value) ? $value : '',
                'errors' => ['Product couldn`t be added.']
            ]);
        }
        return $this->redirectToUri($this->getParam(self::PARAM_CONTEXT));
    }

    public function POST_Remove(): \Presentation\MVC\ActionResult {
        $this->tryGetParam('product', $productID);
        $errors = [];
        $user = $this->signedInUserQuery->execute();
        $product = $this->productSearchQuery->executeIDFilter($productID);
        if ($product == null) {
            return $this->redirectToUri($this->getParam(self::PARAM_CONTEXT));
        }
        if ($user->getUserName() != $product->getCreator()) {
            return $this->redirectToUri($this->getParam(self::PARAM_CONTEXT));
        }
        if (!$this->removeProductCommand->execute($productID)) {
            return $this->view('detailView', [
                'context' => $this->getRequestUri(),
                'user' => $this->signedInUserQuery->execute(),
                'errors' => ['Product couldn`t be added.']
            ]);
        }
        return $this->redirect("Products", "Index");
    }

}