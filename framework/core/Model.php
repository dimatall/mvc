<?php

namespace framework\core;

use framework\validate\Validation;

/**
 * Class Model
 * @package framework
 */
class Model
{
    /**
     * Validation errors
     * @var array
     */
    protected $errors = [];

    /**
     * Model db fields
     * @var array $fields
     */
    public $fields = [];

    /**
     * Fields labels
     * @var array
     */
    protected $labels = [];

    /**
     * Validation rules
     * @var array $rules
     */
    protected $rules = [];


    public function __construct($attributes = [])
    {
        foreach ($attributes as $key => $val) {
            $this->$key = $val;
        }
    }

    /**
     * Save model
     * @param bool $validate
     * @return bool
     */
    public function save($validate = true)
    {
        $db = Application::getDb();

        $tableName = static::getTableName();

        if ($validate) {
            if (!$this->validate()) {
                return false;
            }
        }
        $attributes = $this->fields;
        unset($attributes['id']);

        $fields = implode(',', array_keys($attributes));

        $serialized = $this->serialize(array_values($attributes));
        $values = implode("','", $serialized);

        $sql = "INSERT INTO `{$tableName}` ({$fields}) VALUES ('{$values}')";

        $stm = $db->prepare($sql);
        return $stm->execute();
    }

    /**
     * @param $params
     */
    public static function findOne($params)
    {
        $data = self::find($params)->fetch(\PDO::FETCH_ASSOC);
        if ($data) {
            return new static($data);
        }
        return false;
    }

    /**
     * @param $params
     */
    public static function findAll($params = [])
    {
        $data = self::find($params)->fetchAll(\PDO::FETCH_ASSOC);

        $models = [];
        if (!empty($data)) foreach ($data as $item) {
            $models[] = new static($item);
        }
        return $models;
    }

    /**
     * Get models by condition
     * @param array $params
     * @return \PDOStatement
     */
    protected static function find($params = [])
    {
        $db = Application::getDb();

        $tableName = static::getTableName();

        $condition = [];
        $sql = "SELECT * FROM `{$tableName}`";

        foreach ($params as $field => $val) {
            $val = htmlspecialchars(addslashes($val));
            $condition[] = "`{$field}` = '{$val}'";
        }

        if (!empty($condition)) {
            $sql .= " WHERE " . implode(' AND ', $condition);
        }

        $stm = $db->prepare($sql);
        $stm->execute();
        return $stm;
    }

    public function serialize($array)
    {
        foreach ($array as $key => $val) {
            $array[$key] = htmlspecialchars(addslashes($val));
        }
        return $array;
    }

    public static function getTableName()
    {
        return strtolower(static::getClassName());
    }

    /**
     * Fill the model with from data
     * @param $post
     * @return bool
     */
    public function populate($post)
    {
        $modelName = static::getClassName();
        if (!empty($post[$modelName])) {

            foreach ($post[$modelName] as $field => $val) {
                $this->$field = $val;
            }
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function hasErrors()
    {
        return !empty($this->errors);
    }

    /**
     * @return mixed
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param $message
     * @return mixed
     */
    public function setError($message)
    {
        return $this->errors[] = $message;
    }

    /**
     * Validation rules
     * @return array
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * Field labels
     * @param string $field
     * @return array|string
     */
    public function getLabel($field)
    {
        return isset($this->labels[$field]) ? $this->labels[$field] : $field;
    }

    /**
     * Validate the model
     * @return bool
     */
    public function validate()
    {
        $validator = new Validation($this);
        $validator->validate();

        return empty($this->errors);
    }

    /**
     * Get base class name
     * @return string
     */
    public static function getClassName()
    {
        try {
            $reflection = new \ReflectionClass(new static);
            return $reflection->getShortName();
        } catch (\ReflectionException $e) {
            return '';
        }
    }

    /**
     * @param $name
     * @return mixed
     * @throws \Exception
     */
    public function __get($name)
    {
        if (isset($this->fields[$name])) {
            return $this->fields[$name];
        }
        $class = __CLASS__;
        throw new \Exception("Invalid property {$class}::{$name}");
    }

    /**
     * @param $name
     * @param $val
     * @throws \Exception
     */
    public function __set($name, $val)
    {
        if (isset($this->fields[$name])) {
            $this->fields[$name] = $val;
        } else {
            $class = __CLASS__;
            throw new \Exception("Invalid property {$class}::{$name}");
        }
    }
}