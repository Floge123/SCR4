<?php $render('partial/header', $data); ?>

<h1>Register</h1>

<?php $beginForm('User', 'Register', array('ctx' => $data['context']), method: 'post'); ?>
<div class="mb-3">
    <label for="userName" class="form-label">User name</label>
    <input class="form-control" id="userName" name="un" value="<?php $htmlOut($data['userName']); ?>">
</div>
<div class="mb-3">
    <label for="password" class="form-label">Password</label>
    <input type="password" class="form-control" id="password" name="pwd">
</div>
<div class="mb-3">
    <label for="passwordConfirm" class="form-label">Confirm Password</label>
    <input type="password" class="form-control" id="passwordConfirm" name="pwd_conf">
</div>
<button class="btn btn-primary">Register Account</button>
<?php $endForm(); ?>

<?php $render('partial/footer', $data); ?>
