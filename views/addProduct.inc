<?php $render('partial/header', $data); ?>
    <div>
        <h1 class="mb-3">Add a new Product</h1>
    </div>
    <?php $beginForm('Products', 'Add', method: 'post'); ?>
        <?php $render('partial/productDetails', $data); ?>
    <button class="btn btn-primary">Add Product</button>
    <?php $endForm(); ?>
<?php $render('partial/footer', $data); ?>


