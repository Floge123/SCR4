<?php if (!isset($data)): ?>
  <nav class="navbar-nav">
    <?php $link('Login', 'User', 'LogIn', cssClass: 'nav-item nav-link'); ?>
    <?php $link('Register', 'User', 'Register', cssClass: 'nav-item nav-link'); ?>
  </nav>
<?php else: ?>
  <?php $beginForm('User', 'LogOut', method: 'post', cssClass: 'form-inline'); ?>
    <span class="navbar-text me-2">Welcome, <strong><?php $htmlOut($data->getUserName()); ?></strong>.</span>
    <button class="btn btn-secondary">Log out</button>
  <?php $endForm(); ?>
<?php endif; ?>