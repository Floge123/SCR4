<?php $render('partial/header', $data); ?>
    <div>
        <h1>
            <?php $beginForm('Products', 'Add'); ?>
                Products
                <button class="btn btn-primary">Add Product</button>
            <?php $endForm(); ?>
        </h1>
    </div>
    <?php if(sizeof($data['products']) > 0) { ?>

    <?php $render('partial/products', $data); ?>

    <?php } else { ?>
        <p>No products available.</p>
    <?php } ?>
<?php $render('partial/footer', $data); ?>