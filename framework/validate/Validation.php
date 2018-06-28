<?php

namespace framework\validate;

use framework\core\Application;
use framework\core\Model;

class Validation
{
    /**
     * @var Model $model
     */
    protected $model;

    /**
     * Validation messages
     * @var array
     */
    protected $messages = [
        'required' => '%s cannot be a blank',
        'integer' => '%s should be an integer',
        'string' => '%s should be a string',
        'max' => 'Maximum allowed %s length %d',
        'min' => 'Minimum allowed %s length %d',
        'unique' => '%s already exists',
    ];

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Walk through model rules and check each one
     */
    public function validate()
    {
        $rules = $this->model->getRules();
        foreach ($rules as $field => $val) {
            foreach ($val as $rule) {
                $this->checkValueByRule($field, $rule);
            }
        }
    }

    /**
     * @param $field
     * @param $rule
     */
    protected function checkValueByRule($field, $rule)
    {
        $value = $this->model->$field;

        if (!is_array($rule)) {
            switch ($rule) {
                case 'required':
                    if (!$value) {
                        $this->addErrorMessage($field, $rule);
                    }
                    break;
                case 'integer':
                    if (!is_integer($value)) {
                        $this->addErrorMessage($field, $rule);
                    }
                    break;
                case 'string':
                    if (!is_string($value)) {
                        $this->addErrorMessage($field, $rule);
                    }
                    break;
                case 'unique':

                    $db = Application::getDb();
                    $model = $this->model;
                    $table = $model::getTableName();

                    $stm = $db->prepare("SELECT * FROM {$table} WHERE {$field}='{$value}'");
                    $stm->execute();

                    if ($stm->fetch(\PDO::FETCH_OBJ)) {
                        $this->addErrorMessage($field, $rule);
                    }

                    break;
            }
        } else {
            switch ($rule[0]) {
                case 'string':
                    if (is_string($value)) {
                        if (isset($rule['max']) && strlen($value) > $rule['max']) {
                            $this->addErrorMessage($field, 'max', $rule['max']);
                        }
                        if (isset($rule['min']) && strlen($value) < $rule['min']) {
                            $this->addErrorMessage($field, 'min', $rule['min']);
                        }
                    } else {
                        $this->addErrorMessage($field, $rule[0]);
                    }
                    break;
            }
        }
    }

    protected function addErrorMessage($field, $rule, $param = '')
    {
        $message = sprintf($this->messages[$rule], $this->model->getLabel($field), $param);
        $this->model->setError($message);
    }
}