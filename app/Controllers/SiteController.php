<?php

namespace app\Controllers;

use app\Models\User;
use framework\core\Application;
use framework\helpers\UrlHelper;

class SiteController extends \framework\core\Controller
{
    public function index()
    {
        if (Application::isGuest()) {
            return UrlHelper::redirect('site/login');
        }
        return $this->render('index');
    }

    public function signUp()
    {
        if (!Application::isGuest()) {
            return UrlHelper::redirect('site/index');
        }

        $model = new User;
        if ($model->populate($_POST)) {

            if ($model->validate()) {

                $model->generateToken();
                $model->generatePassword();

                if ($model->save(false)) {
                    return UrlHelper::redirect('site/login');
                }
            }
        }
        return $this->render('signup', [
            'model' => $model
        ]);
    }

    /**
     * @return string
     */
    public function login()
    {
        if (!Application::isGuest() || $this->isRemember()) {
            return UrlHelper::redirect('site/index');
        }

        $model = new User;
        if ($model->populate($_POST)) {

            /** @var User $user */
            $user = User::findOne(['username' => $model->username]);

            if ($user && $user->validatePassword($model->password)) {
                $this->setSession($user);

                if (isset($_POST['remember'])) {
                    $user->setToken();
                }
                return UrlHelper::redirect('site/index');
            }
            $model->setError('Invalid username or password');
        }

        return $this->render('login', [
            'model' => $model
        ]);
    }

    public function logout()
    {
        if (Application::isGuest()) {
            return UrlHelper::redirect('site/index');
        }

        $user = User::findOne(['id' => $_SESSION['userId']]);
        if ($user) {
            $user->destroyToken();
        }
        session_destroy();

        return UrlHelper::redirect('site/login');
    }

    protected function isRemember()
    {
        if (isset($_COOKIE['remember_token'])) {
            $token = $_COOKIE['remember_token'];

            $user = User::findOne(['auth_token' => $token]);
            if ($user && $user->token_expires > time() && $user->setToken(false)) {
                $this->setSession($user);
                return true;
            }
        }

        return false;
    }

    protected function setSession($user)
    {
        $_SESSION['userId'] = $user->id;
        $_SESSION['userName'] = $user->username;
        $_SESSION['userFullName'] = $user->full_name;
    }
}