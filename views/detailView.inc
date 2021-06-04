<?php $render('partial/header', $data); ?>

<div class="container" style="border:1px solid black;">
    <div class="row">
        <div class="col">
            <h2><?php $htmlOut($data['product']->getName()); ?></h2>
            <h6>Added By: <?php $htmlOut($data['product']->getCreator()); ?></h6>
            <h6>Manufacturer: <?php $htmlOut($data['product']->getManufacturer()); ?></h6>
        </div>
        <div class="col text-center">
            <h4>Grade (<?php $htmlOut($data['product']->getRatingCount()); ?> ratings)<br/>
                <?php if ($data['product']->getRatingCount() == 0): ?>
                    <p>No ratings given</p>
                <?php else: ?>
                    <?php $htmlOut($data['product']->getAverageRating()); ?>
                <?php endif; ?>
            </h4>
        </div>
        <div class="col text-end">
            <?php if ($data['user'] != null): ?>
                <button class="btn btn-outline-success mt-2" type="button" data-bs-toggle="collapse" data-bs-target="#ratingMenu">Rate</button>
                <?php if ($data['user']->getUserName() == $data['product']->getCreator()): ?>
                    <?php $beginForm('Products', 'Edit', array('product' => $data['product']->getID(), 'ctx' => $data['context'])) ?>
                        <button class="btn btn-outline-primary mt-2">Edit</button>
                    <?php $endForm(); ?>
                    <?php $beginForm('Products', 'Remove', array('product' => $data['product']->getID(), 'ctx' => $data['context']), method: 'post') ?>
                        <button class="btn btn-outline-danger mt-2">Remove</button>
                    <?php $endForm(); ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <hr>

    <div class="row">
        <div class="col">
            <h6>Description:</h6>
            <p class="text-break"><?php $htmlOut($data['product']->getDescription()); ?></p>
        </div>
    </div>
</div>

<div class="container collapse mt-2 w-50 text-center" id="ratingMenu" style="border:1px solid gray">

    <h4>Add Rating</h4>
    <?php $beginForm('Rating', 'Submit', array('ctx' => $data['context'], 'productID' => $data['product']->getID()), method: 'post'); ?>
    <?php $render('partial/ratingMenu', $data); ?>
        <button class="btn btn-primary mb-2">Submit</button>
    <?php $endForm(); ?>
</div>

<div class="container mt-2 w-50">
    <?php if(sizeof($data['ratings']) > 0): ?>
        <?php foreach($data['ratings'] as $rating): ?>
            <div class="card mt-4">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <?php
                            $htmlOut($rating->getCreator());
                            if (!$data['user'] == null && $rating->getCreator() == $data['user']->getUserName()):
                                $htmlOut("(You)");
                            endif ?>;
                        </div>
                        <div class="col text-center">
                            <?php
                            $htmlOut("Grade: ");
                            $htmlOut($rating->getGrade());
                            ?>
                        </div>
                        <div class="col text-end">
                        <?php if (!$data['user'] == null && $rating->getCreator() == $data['user']->getUserName()): ?>
                            <?php $beginForm('Rating', 'Edit', array('productID' => $data['product']->getID(), 'ctx' => $data['context'], 'ratingID' => $rating->getID())) ?>
                            <button class="btn btn-sm btn-outline-primary">Edit</button>
                            <?php $endForm(); ?>
                        <?php endif; ?>
                        </div>
                    </div>

                </div>
                <div class="card-body">
                    <?php $htmlOut($rating->getComment()); ?>
                </div>
                <div class="card-footer text-muted">
                    <?php $htmlOut($rating->getCreateDate()); ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-center">No ratings for this product.</p>
    <?php endif; ?>
</div>


<?php $render('partial/footer', $data); ?>