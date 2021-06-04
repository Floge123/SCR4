<div class="mb-3">
    <label for="name" class="form-label">Product name</label>
    <input class="form-control" id="name" name="pn" value="<?php $htmlOut($data['productName']); ?>">
</div>
<div class="mb-3">
    <label for="manufacturer" class="form-label">Manufacturer</label>
    <input class="form-control" id="manufacturer" name="pm" value="<?php $htmlOut($data['manufacturer']); ?>">
</div>
<div class="mb-3">
    <label for="description" class="form-label">Description</label>
    <textarea class="form-control shadow textarea" id="description" name="pd"><?php $htmlOut($data['description']); ?></textarea>
</div>

<script>
    $(document).ready(function() {
        $('textarea').on('keyup keypress', function() {
            $(this).height(0);
            $(this).height(this.scrollHeight);
        });
    });
</script>