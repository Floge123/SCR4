<table class="table table-striped table-hover">
    <thead class="table-dark">
        <tr>
            <th>Name</th>
            <th>Manufacturer</th>
            <th>Creator</th>
            <th>Rating</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($data['products'] as $product): ?>
            <tr>
                <td><?php $htmlOut($product->getName()); ?></td>
                <td><?php $htmlOut($product->getManufacturer()); ?></td>
                <td><?php $htmlOut($product->getCreator()); ?></td>
                <td><?php $htmlOut($product->getAverageRating()); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>