<?php
/**
 * @var \app\Models\User $model
 */
?>

<div class="col-md-4">
    <h3>Signup form</h3>
    <form  action="/site/sign-up"  method="post">

        <div class="form-group">
            <label for="full_name"><?= $model->getLabel('full_name') ?></label>
            <input type="text" class="form-control" id="full_name" name="User[full_name]" value="<?= $model->full_name ?>">
        </div>
        <div class="form-group">
            <label for="username"><?= $model->getLabel('username') ?></label>
            <input type="text" class="form-control" id="username" name="User[username]" value="<?= $model->username ?>">
        </div>
        <div class="form-group">
            <label for="password"><?= $model->getLabel('password') ?></label>
            <input type="password" class="form-control" id="password" name="User[password]">
        </div>

        <?php if ($model->hasErrors()) foreach($model->getErrors() as $error): ?>
            <div class="text-danger">
                <?= $error ?>
            </div>
        <?php endforeach; ?>

        <button type="submit" class="btn btn-primary">Sign up</button>
    </form>
    <br>
    <a class="btn btn-warning" href="/site/login">Login</a>
</div>