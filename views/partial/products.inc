<table class="table table-striped table-hover">
    <thead class="table-dark">
        <tr>
            <th>Name</th>
            <th>Manufacturer</th>
            <th>Creator</th>
            <th>Total Ratings</th>
            <th>Avg Rating</th>
            <th>Show More</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($data['products'] as $product): ?>
            <tr>
                <td><?php $htmlOut($product->getName()); ?></td>
                <td><?php $htmlOut($product->getManufacturer()); ?></td>
                <td><?php $htmlOut($product->getCreator()); ?></td>
                <td><?php $htmlOut($product->getRatingCount()); ?></td>
                <td><?php $htmlOut($product->getAverageRating()); ?></td>
                <td>
                    <?php $beginForm('Details', 'Index', array('product' => $product->getID())); ?>
                        <button id="detailbtn" class="btn btn-secondary">Expand</button>
                    <?php $endForm(); ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>