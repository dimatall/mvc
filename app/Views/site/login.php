<?php
/**
 * @var \app\Models\User $model
 */
?>

<div class="col-md-4">
    <h3>Login form</h3>
    <form  action="/site/login"  method="post">

        <div class="form-group">
            <label for="username"><?= $model->getLabel('username') ?></label>
            <input type="text" class="form-control" id="username" name="User[username]" value="<?= $model->username ?>">
        </div>
        <div class="form-group">
            <label for="password"><?= $model->getLabel('password') ?></label>
            <input type="password" class="form-control" id="password" name="User[password]">
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="remember" id="remember">
            <label class="form-check-label" for="remember">
                Remember me
            </label>
        </div>

        <?php if ($model->hasErrors()) foreach($model->getErrors() as $error): ?>
            <div class="text-danger">
                <?= $error ?>
            </div>
        <?php endforeach; ?>

        <button type="submit" class="btn btn-primary">Login</button>
    </form>
    <br>
    <a class="btn btn-warning" href="/site/sign-up">Sign up</a>
</div>

