<?php $render('partial/header', $data); ?>
<div>
    <h1 class="mb-3">Edit <?php $htmlOut($data['product']->getName()); ?></h1>
</div>
<?php $beginForm('Products', 'Update', array('productID' => $data['product']->getID(), 'ctx' => $data['context']), method: 'post'); ?>
    <?php $render('partial/productDetails', $data); ?>
        <button class="btn btn-primary">Update Product</button>
<?php $endForm(); ?>

<?php $render('partial/footer', $data); ?>


