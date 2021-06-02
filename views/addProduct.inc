<?php $render('partial/header', $data); ?>
    <div>
        <h1 class="mb-3">Add a new Product</h1>
    </div>
    <?php $beginForm('Products', 'Add', method: 'post'); ?>
        <div class="mb-3">
            <label for="name" class="form-label">Product name</label>
            <input class="form-control" id="name" name="pn" value="<?php $htmlOut($data['productName']); ?>">
        </div>
        <div class="mb-3">
            <label for="manufacturer" class="form-label">Manufacturer</label>
            <input class="form-control" id="manufacturer" name="pm" value="<?php $htmlOut($data['manufacturer']); ?>">
        </div>
    <button class="btn btn-primary">Add Product</button>
    <?php $endForm(); ?>
<?php $render('partial/footer', $data); ?>


