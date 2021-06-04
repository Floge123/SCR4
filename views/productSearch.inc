<?php $render('partial/header', $data); ?>

<h1>Search</h1>

<div class="my-3">
    <?php $beginForm('Products', 'Search'); ?>
        <div class="row g-3">
            <div class="col-auto">
                <label for="productname" class="form-label">By Product Name</label>
                <input class="form-control" id="productname" name="nameFilter" value="<?php $htmlOut($data['nameFilter'])?>">
            </div>
            <div class="col-auto">
                <label for="manufacturer" class="form-label">By Manufacturer</label>
                <input class="form-control" id="manufacturer" name="manFilter" value="<?php $htmlOut($data['manFilter'])?>">
            </div>
            <div class="col-auto">
                <br/>
                <button id="searchbtn" class="btn btn-primary" style="margin-top: 8px">Search</button>
            </div>
        </div>
    <?php $endForm(); ?>
</div>

<?php if ($data['products'] !== null): ?>
    <?php if (sizeof($data['products']) > 0) {
        $render('partial/products', $data);
    } else { ?>
        <p>No matching products found.</p>
    <?php } ?>
<?php endif; ?>

<?php $render('partial/footer', $data); ?>