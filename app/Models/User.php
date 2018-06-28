<?php

namespace app\Models;

use framework\core\Application;

/**
 * Class User
 * @package app\Models
 *
 * @property string $username
 * @property string $full_name
 * @property string $password
 * @property string $auth_token
 * @property integer $token_expires
 * @property integer $id
 */
class User extends \framework\core\Model
{
    public $fields = [
        'id' => '',
        'username' => '',
        'full_name' => '',
        'password' => '',
        'auth_token' => '',
        'token_expires' => ''
    ];

    protected $labels = [
        'username' => 'Username',
        'full_name' => 'Full name',
        'password' => 'Password'
    ];

    protected $rules = [
        'username' => ['required', 'unique', ['string', 'max' => 100]],
        'full_name' => ['required', ['string', 'max' => 100]],
        'password' => ['required', ['string', 'min' => 8]]
    ];

    /**
     * @param $password
     * @return bool
     */
    public function validatePassword($password)
    {
        return $this->password === md5($password);
    }

    /**
     * Generate remember me token
     */
    public function generateToken($updateTime = true)
    {
        $newToken = md5(uniqid());
        $this->auth_token = $newToken;
        if ($updateTime) {
            $this->token_expires = time() + 86400;
        }
    }

    /**
     * Encrypt password
     */
    public function generatePassword()
    {
        $this->password = md5($this->password);
    }

    /**
     * Update "remember me" token
     * @return bool
     */
    public function setToken($updateTime = true)
    {
        $this->generateToken($updateTime);

        $db = Application::getDb();
        $tableName = static::getTableName();

        $sql = "UPDATE `{$tableName}` SET `auth_token`='{$this->auth_token}',"
            . " `token_expires`={$this->token_expires} WHERE `id`={$this->id}";

        $stm = $db->prepare($sql);

        if ($stm->execute()) {
            setcookie('remember_token', $this->auth_token, time() + 86400);
            return true;
        }
        return false;
    }

    /**
     * Reset "remember me" token time
     */
    public function destroyToken()
    {
        $this->generateToken(false);

        $db = Application::getDb();
        $tableName = static::getTableName();

        $sql = "UPDATE `{$tableName}` SET `auth_token`='{$this->auth_token}',"
            . " `token_expires`=1 WHERE `id`={$this->id}";

        $stm = $db->prepare($sql);

        if ($stm->execute()) {
            setcookie('remember_token', $this->auth_token, time() - 3600);
        }
    }
}