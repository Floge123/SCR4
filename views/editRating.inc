<?php $render('partial/header', $data); ?>
<div>
    <h1 class="mb-3">Edit Rating #<?php $htmlOut($data['rating']->getID());?></h1>
</div>
<?php $beginForm('Rating', 'Update', array('ctx' => $data['context'], 'productID' => $data['product']->getID(), 'ratingID' => $data['rating']->getID()), method:'post'); ?>
    <?php $render('partial/ratingMenu', $data); ?>
    <button class="btn btn-primary mb-2">Update</button>
<?php $endForm(); ?>
<?php $beginForm('Rating', 'Delete', array('ctx' => $data['context'], 'productID' => $data['product']->getID(), 'ratingID' => $data['rating']->getID()), method:'post'); ?>
    <button class="btn btn-danger mb-2">Delete</button>
<?php $endForm(); ?>

<?php $render('partial/footer', $data); ?>

