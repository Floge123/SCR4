<div class="alert alert-danger">
  <p>
    <strong>Please correct the following and try again:</strong>
  </p>
  <ul>
    <?php foreach ($data as $errMsg): ?>
      <li><?php $htmlOut($errMsg); ?></li>
    <?php endforeach; ?>
  </ul>
</div>